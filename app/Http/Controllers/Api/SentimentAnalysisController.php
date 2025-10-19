<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CampaignCommentSentiment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SentimentAnalysisController extends Controller
{
    /**
     * URL de l'API Python (définie dans le constructeur)
     */
    private $pythonApiUrl;

    /**
     * Constructeur - Initialise l'URL de l'API Python
     */
    public function __construct()
    {
        $this->pythonApiUrl = env('PYTHON_API_URL', 'http://localhost:5000/analyze-comment');
    }









    /**
     * Analyse sentiment et sauvegarde en DB
     */
    public function analyzeComment(Request $request)
    {
        $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'comment_id' => 'required|exists:campaign_comments,id',
            'content' => 'required|string|max:2000',
            'user_id' => 'nullable|integer|exists:users,id'
        ]);

        Log::info('🔍 API SENTIMENT APPELÉE', [
            'campaign_id' => $request->campaign_id,
            'comment_id' => $request->comment_id,
            'content_preview' => substr($request->getContent(), 0, 50),
            'python_url' => $this->pythonApiUrl
        ]);

        try {
            // ✅ TEST CONNEXION AVANT APPEL
            $healthCheck = Http::timeout(5)->get(str_replace('/analyze-comment', '/health', $this->pythonApiUrl));
            Log::info('🏥 Python Health Check', [
                'status' => $healthCheck->status(),
                'response' => $healthCheck->body()
            ]);

            // Appel API Python
            $response = Http::timeout(30)
                ->withOptions(['verify' => false]) // Ignore SSL en dev
                ->post($this->pythonApiUrl, $request->all());

            Log::info('📡 Réponse Python API', [
                'status' => $response->status(),
                'headers' => $response->headers(),
                'body_preview' => substr($response->body(), 0, 500),
                'successful' => $response->successful()
            ]);

            if (!$response->successful()) {
                Log::error('❌ Python API ERREUR', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'url' => $this->pythonApiUrl
                ]);

                // ✅ FALLBACK : Sentiment mock pour debug
                return $this->saveMockSentiment($request);
            }

            $pythonData = $response->json();

            if (!isset($pythonData['data'])) {
                Log::error('❌ Format réponse Python invalide', [
                    'expected' => '{"data": {...}}',
                    'received' => $pythonData
                ]);
                return $this->saveMockSentiment($request);
            }

            $data = $pythonData['data'];

            // Validation données
            if (!isset($data['overall_sentiment_score'])) {
                Log::error('❌ Données sentiment manquantes', ['data' => $data]);
                return $this->saveMockSentiment($request);
            }

            // Sauvegarde
            $sentiment = CampaignCommentSentiment::updateOrCreate(
                ['campaign_comment_id' => $data['campaign_comment_id']],
                [
                    'campaign_id' => $data['campaign_id'],
                    'comment_content' => $data['comment_content'],
                    'anger' => $data['anger'] ?? 0,
                    'anticipation' => $data['anticipation'] ?? 0,
                    'disgust' => $data['disgust'] ?? 0,
                    'fear' => $data['fear'] ?? 0,
                    'joy' => $data['joy'] ?? 0,
                    'sadness' => $data['sadness'] ?? 0,
                    'surprise' => $data['surprise'] ?? 0,
                    'trust' => $data['trust'] ?? 0,
                    'positive' => $data['positive'] ?? 0,
                    'negative' => $data['negative'] ?? 0,
                    'overall_sentiment_score' => $data['overall_sentiment_score'],
                    'dominant_emotion' => $data['dominant_emotion'] ?? 'neutral',
                    'confidence' => $data['confidence'] ?? 0.5,
                    'detected_language' => $data['detected_language'] ?? 'unknown',
                    'raw_scores' => $data['raw_scores'] ?? [],
                    'matched_words' => $data['matched_words'] ?? [],
                ]
            );

            Log::info('✅ Sentiment SAUVÉ', [
                'sentiment_id' => $sentiment->id,
                'emotion' => $sentiment->dominant_emotion,
                'score' => $sentiment->overall_sentiment_score,
                'language' => $sentiment->detected_language
            ]);

            return response()->json([
                'success' => true,
                'sentiment_id' => $sentiment->id,
                'data' => $data,
                'message' => 'Analysis completed'
            ]);

        } catch (\Exception $e) {
            Log::error('💥 EXCEPTION API SENTIMENT', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'url' => $this->pythonApiUrl
            ]);

            return $this->saveMockSentiment($request);
        }
    }

    /**
     * Fallback : Sauvegarde sentiment mock pour debug
     */
    private function saveMockSentiment($request)
    {
        $mockData = [
            'campaign_id' => $request->campaign_id,
            'campaign_comment_id' => $request->comment_id,
            'comment_content' => $request->content,
            'overall_sentiment_score' => 0.7, // Positif mock
            'dominant_emotion' => 'joy',
            'confidence' => 0.85,
            'detected_language' => 'english',
            'joy' => 0.7, 'anger' => 0.1, 'positive' => 0.8, 'negative' => 0.2,
            // ... autres émotions à 0
        ];

        $sentiment = CampaignCommentSentiment::updateOrCreate(
            ['campaign_comment_id' => $request->comment_id],
            $mockData
        );

        Log::warning('🧪 MOCK Sentiment sauvé (Python KO)', [
            'comment_id' => $request->comment_id,
            'sentiment_id' => $sentiment->id
        ]);

        return response()->json([
            'success' => true,
            'sentiment_id' => $sentiment->id,
            'mock' => true,
            'message' => 'Mock analysis (Python API unavailable)',
            'data' => $mockData
        ]);
    }









    /**
     * Test connexion API Python
     */
    public function testConnection()
    {
        $testData = [
            'campaign_id' => 999,
            'comment_id' => 999,
            'content' => 'Ana farhèn b had l projet! 🇹🇳 yallah khouya zwin',
            'user_id' => auth()->id() ?? 1
        ];

        try {
            Log::info('Testing Python API connection', ['url' => $this->pythonApiUrl]);
            $response = Http::timeout(10)->post($this->pythonApiUrl, $testData);

            $status = $response->successful();
            $result = $status ? $response->json() : null;

            Log::info('Python API test result', [
                'status' => $status,
                'response_time' => $response->header('X-Response-Time') ?? 'unknown',
                'url' => $this->pythonApiUrl
            ]);

            return response()->json([
                'connection' => $status,
                'python_status' => $status ? 'OK' : 'ERROR',
                'api_url' => $this->pythonApiUrl,
                'test_result' => $result,
                'response_code' => $response->status(),
                'response_time' => $response->header('X-Response-Time') ?? 'N/A'
            ]);

        } catch (\Exception $e) {
            Log::error('Python API connection test failed', [
                'error' => $e->getMessage(),
                'url' => $this->pythonApiUrl
            ]);

            return response()->json([
                'connection' => false,
                'python_status' => 'ERROR',
                'api_url' => $this->pythonApiUrl,
                'error' => $e->getMessage(),
                'suggestion' => 'Vérifiez que le serveur Python tourne sur ' . $this->pythonApiUrl
            ], 503);
        }
    }

    /**
     * Obtenir les stats de sentiment pour une campagne
     */
    public function getCampaignSentiments($campaignId)
    {
        $sentiments = CampaignCommentSentiment::where('campaign_id', $campaignId)
            ->with('comment.user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = CampaignCommentSentiment::where('campaign_id', $campaignId)
            ->selectRaw('
                AVG(overall_sentiment_score) as avg_score,
                COUNT(*) as total_comments,
                SUM(CASE WHEN overall_sentiment_score > 0 THEN 1 ELSE 0 END) as positive,
                SUM(CASE WHEN overall_sentiment_score < 0 THEN 1 ELSE 0 END) as negative,
                COUNT(DISTINCT CASE WHEN detected_language = "tunisian" THEN 1 END) as tunisian_comments
            ')
            ->first();

        return response()->json([
            'success' => true,
            'sentiments' => $sentiments,
            'stats' => $stats,
            'avg_sentiment' => round($stats->avg_score ?? 0, 3)
        ]);
    }
}
