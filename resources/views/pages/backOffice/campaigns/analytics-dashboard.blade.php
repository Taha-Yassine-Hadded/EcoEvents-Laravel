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

        <!-- Top Campagnes et Sentiment -->
        <div class="row">
            <!-- Top 5 Campagnes par Interactions -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Top 5 Campagnes par Interactions</h4>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                <tr>
                                    <th>Campagne</th>
                                    <th>Interactions</th>
                                    <th>Détail</th>
                                    <th>Sentiment</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($topCampaigns as $campaign)
                                    <tr>
                                        <td>{{ Str::limit($campaign->title, 30) }}</td>
                                        <td><strong>{{ $campaign->total_interactions }}</strong></td>
                                        <td>
                                            <small class="text-muted">
                                                💬 {{ $campaign->comments_count }} | ❤️ {{ $campaign->likes_count }}
                                            </small>
                                        </td>
                                        <td>
                                            {{ number_format($campaign->avg_sentiment ?? 0, 2) }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">Aucune interaction</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Répartition Sentiment -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Répartition Sentiment</h4>
                        <div class="sentiment-chart-container">
                            <canvas id="sentimentChart" height="300"></canvas>
                        </div>
                        <div class="mt-3">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="sentiment-legend positive">
                                        <strong>Positif</strong><br>
                                        <small>{{ $sentimentStats->positive ?? 0 }}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="sentiment-legend neutral">
                                        <strong>Neutre</strong><br>
                                        <small>{{ $sentimentStats->neutral ?? 0 }}</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="sentiment-legend negative">
                                        <strong>Négatif</strong><br>
                                        <small>{{ $sentimentStats->negative ?? 0 }}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🆕 NOUVEAUX DASHBOARDS : Catégories et Statuts -->
        <div class="row">
            <!-- Campagnes par Catégorie -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Campagnes par Catégorie</h4>
                        <div class="chart-container">
                            <canvas id="categoryChart" height="250"></canvas>
                        </div>
                        @if($campaignsByCategory->count() > 0)
                            <div class="mt-2">
                                <small class="text-muted">Total: {{ $campaignsByCategory->sum('count') }} campagnes</small>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Campagnes par Statut -->
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Campagnes par Statut</h4>
                        <div class="chart-container">
                            <canvas id="statusChart" height="250"></canvas>
                        </div>
                        <div class="mt-2">
                            <small class="text-muted">
                                @foreach($campaignsByComputedStatus as $status)
                                    {{ ucfirst($status->computed_status) }}: {{ $status->count }} •
                                @endforeach
                                <span class="text-danger">{{ $campaignsByComputedStatus->sum('count') != $totalCampaigns ? 'Vérifiez les dates' : '' }}</span>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique des Langues (minimisé) -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Distribution des Langues</h4>
                        <div class="languages-chart-container">
                            <canvas id="languagesChart" height="80"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .sentiment-chart-container, .chart-container {
            position: relative;
            height: 250px;
            width: 100%;
        }

        .languages-chart-container {
            position: relative;
            height: 200px;
            width: 100%;
        }

        .sentiment-legend {
            padding: 8px;
            border-radius: 5px;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .sentiment-legend.positive {
            background-color: rgba(40, 167, 69, 0.1);
            border-left: 4px solid #28a745;
            color: #28a745;
        }
        .sentiment-legend.neutral {
            background-color: rgba(255, 193, 7, 0.1);
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .sentiment-legend.negative {
            background-color: rgba(220, 53, 69, 0.1);
            border-left: 4px solid #dc3545;
            color: #dc3545;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart Sentiment
        const ctx1 = document.getElementById('sentimentChart')?.getContext('2d');
        if (ctx1) {
            new Chart(ctx1, {
                type: 'doughnut',
                data: {
                    labels: ['Positif', 'Neutre', 'Négatif'],
                    datasets: [{
                        data: [{{ $sentimentStats->positive ?? 0 }}, {{ $sentimentStats->neutral ?? 0 }}, {{ $sentimentStats->negative ?? 0 }}],
                        backgroundColor: ['#28a745', '#ffc107', '#dc3545'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } }
                }
            });
        }

        // Chart Catégories
        const ctx2 = document.getElementById('categoryChart')?.getContext('2d');
        if (ctx2) {
            const categoryData = @json($campaignsByCategory->pluck('count', 'category'));
            new Chart(ctx2, {
                type: 'pie',
                data: {
                    labels: Object.keys(categoryData),
                    datasets: [{
                        data: Object.values(categoryData),
                        backgroundColor: ['#3498db', '#e74c3c', '#2ecc71', '#f39c12', '#9b59b6', '#1abc9c', '#34495e'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 20, usePointStyle: true }
                        }
                    }
                }
            });
        }

        // Chart Statuts
        const ctx3 = document.getElementById('statusChart')?.getContext('2d');
        if (ctx3) {
            const statusData = @json($campaignsByComputedStatus->pluck('count', 'computed_status'));
            new Chart(ctx3, {
                type: 'doughnut',
                data: {
                    labels: Object.keys(statusData),
                    datasets: [{
                        data: Object.values(statusData),
                        backgroundColor: ['#f39c12', '#27ae60', '#e74c3c'],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 20, usePointStyle: true }
                        }
                    }
                }
            });
        }

        // Chart Langues
        const ctx4 = document.getElementById('languagesChart')?.getContext('2d');
        if (ctx4) {
            const languageData = @json($languages->pluck('count', 'detected_language'));
            new Chart(ctx4, {
                type: 'bar',
                data: {
                    labels: Object.keys(languageData),
                    datasets: [{
                        data: Object.values(languageData),
                        backgroundColor: '#3498db',
                        borderRadius: 3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true } },
                    plugins: { legend: { display: false } }
                }
            });
        }
    </script>
@endsection
