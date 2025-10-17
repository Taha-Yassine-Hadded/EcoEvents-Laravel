@extends('layouts.admin')

@section('title', 'Analytics Campagnes')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Analytics Campagnes</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Analytics Campagnes</h4>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="fas fa-bullhorn fa-3x"></i>
                        </div>
                        <h6 class="text-white">Total Campagnes</h6>
                        <h2 class="text-white">{{ $totalCampaigns }}</h2>
                        <p class="mb-0">Actives: {{ $activeCampaigns }}</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="fas fa-comments fa-3x"></i>
                        </div>
                        <h6 class="text-white">Total Commentaires</h6>
                        <h2 class="text-white">{{ $totalComments }}</h2>
                        <p class="mb-0">Analysés: {{ $totalSentiments }}</p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="fas fa-chart-line fa-3x"></i>
                        </div>
                        <h6 class="text-white">Sentiment Moyen</h6>
                        <h2 class="text-white">{{ number_format($sentimentStats->avg_sentiment ?? 0, 2) }}</h2>
                        <p class="mb-0">
                            Positif: {{ $sentimentStats->positive ?? 0 }} |
                            Négatif: {{ $sentimentStats->negative ?? 0 }}
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="float-right">
                            <i class="fas fa-globe fa-3x"></i>
                        </div>
                        <h6 class="text-white">Langues Détectées</h6>
                        <h2 class="text-white">{{ $languages->sum('count') }}</h2>
                        <p class="mb-0">Commentaires multilingues</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Top Campagnes -->
            <div class="col-xl-8">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Top 5 Campagnes par Commentaires</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Campagne</th>
                                    <th>Commentaires</th>
                                    <th>Sentiment Moyen</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($topCampaigns as $campaign)
                                    <tr>
                                        <td>{{ Str::limit($campaign->title, 30) }}</td>
                                        <td>{{ $campaign->comments_count }}</td>
                                        <td>
                                            @php
                                                $avgSentiment = $campaign->comments()
                                                    ->join('campaign_comment_sentiments', 'campaign_comments.id', '=', 'campaign_comment_sentiments.comment_id')
                                                    ->avg('overall_sentiment_score');
                                            @endphp
                                            {{ number_format($avgSentiment ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Répartition Sentiment -->
            <div class="col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Répartition Sentiment</h4>
                        <canvas id="sentimentChart" height="200"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique Évolution -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Évolution des Commentaires (6 derniers mois)</h4>
                        <canvas id="monthlyChart" height="100"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart Sentiment
        const ctx1 = document.getElementById('sentimentChart').getContext('2d');
        new Chart(ctx1, {
            type: 'doughnut',
            data: {
                labels: ['Positif', 'Neutre', 'Négatif'],
                datasets: [{
                    data: [{{ $sentimentStats->positive ?? 0 }}, {{ $sentimentStats->neutral ?? 0 }}, {{ $sentimentStats->negative ?? 0 }}],
                    backgroundColor: ['#28a745', '#ffc107', '#dc3545']
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Chart Mensuel
        const ctx2 = document.getElementById('monthlyChart').getContext('2d');
        new Chart(ctx2, {
            type: 'line',
            data: {
                labels: @json($monthlyComments->pluck('month')),
                datasets: [{
                    label: 'Nouveaux Commentaires',
                    data: @json($monthlyComments->pluck('count')),
                    borderColor: '#3498db',
                    fill: false
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
@endsection
