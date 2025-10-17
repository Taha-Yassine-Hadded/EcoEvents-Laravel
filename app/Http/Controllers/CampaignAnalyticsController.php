<?php
// app/Http/Controllers/CampaignAnalyticsController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Campaign;
use App\Models\CampaignComment;
use App\Models\CampaignCommentSentiment;
use App\Models\CampaignLike;
use Illuminate\Support\Facades\DB;

class CampaignAnalyticsController extends Controller
{
    public function dashboard()
    {
        // Statistiques globales de toutes les campagnes
        $totalCampaigns = Campaign::count();
        $activeCampaigns = Campaign::where('status', 'active')->count();
        $totalComments = CampaignComment::count();
        $totalSentiments = CampaignCommentSentiment::count();

        // Statistiques de sentiment globales
        $sentimentStats = CampaignCommentSentiment::selectRaw('
            AVG(overall_sentiment_score) as avg_sentiment,
            COUNT(*) as total_analyzed,
            SUM(CASE WHEN overall_sentiment_score > 0.5 THEN 1 ELSE 0 END) as positive,
            SUM(CASE WHEN overall_sentiment_score < -0.5 THEN 1 ELSE 0 END) as negative,
            SUM(CASE WHEN overall_sentiment_score >= -0.5 AND overall_sentiment_score <= 0.5 THEN 1 ELSE 0 END) as neutral
        ')->first();

        // Top 5 campagnes par INTERACTIONS
        $topCampaigns = Campaign::withCount(['comments', 'likes'])
            ->select('campaigns.*')
            ->selectSub(function ($query) {
                $query->selectRaw('
                    COALESCE((
                        SELECT COUNT(*) FROM campaign_comments WHERE campaign_id = campaigns.id
                    ) + (
                        SELECT COUNT(*) FROM campaign_likes WHERE campaign_id = campaigns.id
                    ), 0)
                ');
            }, 'total_interactions')
            ->selectSub(function ($query) {
                $query->selectRaw('AVG(overall_sentiment_score)')
                    ->from('campaign_comments')
                    ->join('campaign_comment_sentiments', 'campaign_comments.id', '=', 'campaign_comment_sentiments.campaign_comment_id')
                    ->whereColumn('campaign_comments.campaign_id', 'campaigns.id');
            }, 'avg_sentiment')
            ->orderByDesc('total_interactions')
            ->take(5)
            ->get()
            ->each(function ($campaign) {
                $campaign->comments_count = $campaign->comments_count ?? 0;
                $campaign->likes_count = $campaign->likes_count ?? 0;
                $campaign->total_interactions = ($campaign->comments_count + $campaign->likes_count);
            });

        // 🆕 STATISTIQUES PAR CATÉGORIE
        $campaignsByCategory = Campaign::selectRaw('
            category,
            COUNT(*) as count,
            AVG(views_count) as avg_views
        ')
            ->groupBy('category')
            ->orderBy('count', 'desc')
            ->get();

        // 🆕 STATISTIQUES PAR STATUT - MÉTHODE PHP (évite le problème GROUP BY)
        $campaignsByComputedStatus = Campaign::select(['id', 'start_date', 'end_date'])
            ->get()
            ->groupBy(function ($campaign) {
                $now = now();
                if ($campaign->start_date > $now) {
                    return 'upcoming';
                } elseif ($campaign->start_date <= $now && $campaign->end_date >= $now) {
                    return 'active';
                } else {
                    return 'ended';
                }
            })
            ->map(function ($group, $status) {
                return (object) [
                    'computed_status' => $status,
                    'count' => $group->count()
                ];
            })
            ->values();

        // Alternative SQL si vous préférez (avec GROUP BY complet)
        /*
        $campaignsByComputedStatus = DB::select("
            SELECT
                CASE
                    WHEN start_date > NOW() THEN 'upcoming'
                    WHEN start_date <= NOW() AND end_date >= NOW() THEN 'active'
                    ELSE 'ended'
                END as computed_status,
                COUNT(*) as count
            FROM campaigns
            GROUP BY
                CASE
                    WHEN start_date > NOW() THEN 'upcoming'
                    WHEN start_date <= NOW() AND end_date >= NOW() THEN 'active'
                    ELSE 'ended'
                END
        ");
        $campaignsByComputedStatus = collect($campaignsByComputedStatus);
        */

        // Langues détectées
        $languages = CampaignCommentSentiment::selectRaw('
            detected_language,
            COUNT(*) as count
        ')
            ->groupBy('detected_language')
            ->orderBy('count', 'desc')
            ->get();

        return view('pages.backOffice.campaigns.analytics-dashboard', compact(
            'totalCampaigns',
            'activeCampaigns',
            'totalComments',
            'totalSentiments',
            'sentimentStats',
            'topCampaigns',
            'campaignsByCategory',
            'campaignsByComputedStatus',
            'languages'
        ));
    }

    public function apiStats()
    {
        $stats = [
            'total_campaigns' => Campaign::count(),
            'active_campaigns' => Campaign::where('status', 'active')->count(),
            'total_comments' => CampaignComment::count(),
            'sentiment_summary' => CampaignCommentSentiment::selectRaw('
                AVG(overall_sentiment_score) as avg_sentiment,
                COUNT(*) as total_analyzed,
                SUM(CASE WHEN overall_sentiment_score > 0.5 THEN 1 ELSE 0 END) as positive_count,
                SUM(CASE WHEN overall_sentiment_score < -0.5 THEN 1 ELSE 0 END) as negative_count
            ')->first(),
        ];

        return response()->json($stats);
    }
}
