@extends('layouts.sponsor')

@section('title', 'Dashboard Analytique - Echofy Sponsor')

@push('styles')
<style>
    .analytics-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .metric-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        border: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
    }

    .metric-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    .metric-icon {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        margin-bottom: 1rem;
    }

    .metric-value {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }

    .metric-label {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .metric-change {
        font-size: 0.8rem;
        font-weight: 600;
    }

    .metric-change.positive {
        color: #28a745;
    }

    .metric-change.negative {
        color: #dc3545;
    }

    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .chart-header {
        display: flex;
        justify-content: between;
        align-items: center;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .chart-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .period-selector {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .period-btn {
        padding: 0.5rem 1rem;
        border: 2px solid #e9ecef;
        background: white;
        border-radius: 25px;
        font-size: 0.85rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .period-btn.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
    }

    .period-btn:hover {
        border-color: #667eea;
        color: #667eea;
    }

    .period-btn.active:hover {
        color: white;
    }

    .top-events-table {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .table-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }

    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
    }

    .status-approved {
        background: #d4edda;
        color: #155724;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-completed {
        background: #cce5ff;
        color: #004085;
    }

    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }

    .comparison-card {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 2rem;
    }

    .comparison-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .comparison-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
    }

    .comparison-value {
        font-size: 1.2rem;
        font-weight: bold;
    }

    .loading-spinner {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 200px;
    }

    .spinner {
        width: 40px;
        height: 40px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    .chart-canvas {
        max-height: 400px;
    }

    @media (max-width: 768px) {
        .chart-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .period-selector {
            margin-top: 1rem;
        }
        
        .metric-value {
            font-size: 1.5rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Analytics Header -->
<div class="analytics-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-2">
                    <i class="fas fa-chart-line me-2"></i>
                    Dashboard Analytique
                </h1>
                <p class="mb-0 opacity-75">
                    Analysez vos performances et optimisez vos investissements
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-flex justify-content-md-end gap-2">
                    <button class="btn btn-light" onclick="exportReport()">
                        <i class="fas fa-download me-1"></i>
                        Exporter
                    </button>
                    <button class="btn btn-outline-light" onclick="refreshData()">
                        <i class="fas fa-sync-alt me-1"></i>
                        Actualiser
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Period Selector -->
<div class="container mb-4">
    <div class="period-selector justify-content-center">
        <button class="period-btn {{ $period === '1month' ? 'active' : '' }}" onclick="changePeriod('1month')">
            1 Mois
        </button>
        <button class="period-btn {{ $period === '3months' ? 'active' : '' }}" onclick="changePeriod('3months')">
            3 Mois
        </button>
        <button class="period-btn {{ $period === '6months' ? 'active' : '' }}" onclick="changePeriod('6months')">
            6 Mois
        </button>
        <button class="period-btn {{ $period === '1year' ? 'active' : '' }}" onclick="changePeriod('1year')">
            1 An
        </button>
    </div>
</div>

<!-- Main Metrics -->
<div class="container mb-4">
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <i class="fas fa-coins"></i>
                </div>
                <div class="metric-value text-primary">{{ number_format($metrics['total_invested'], 0) }} TND</div>
                <div class="metric-label">Total Investi</div>
                <div class="metric-change {{ $comparison['amount_change'] >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ $comparison['amount_change'] >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ abs($comparison['amount_change']) }}% vs période précédente
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <i class="fas fa-handshake"></i>
                </div>
                <div class="metric-value text-success">{{ $metrics['total_sponsorships'] }}</div>
                <div class="metric-label">Total Sponsorships</div>
                <div class="metric-change {{ $comparison['count_change'] >= 0 ? 'positive' : 'negative' }}">
                    <i class="fas fa-arrow-{{ $comparison['count_change'] >= 0 ? 'up' : 'down' }} me-1"></i>
                    {{ abs($comparison['count_change']) }}% vs période précédente
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="metric-value text-info">{{ $metrics['average_roi'] }}%</div>
                <div class="metric-label">ROI Moyen</div>
                <div class="metric-change positive">
                    <i class="fas fa-trending-up me-1"></i>
                    Performance stable
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="metric-card">
                <div class="metric-icon" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="metric-value text-success">{{ $metrics['success_rate'] }}%</div>
                <div class="metric-label">Taux de Réussite</div>
                <div class="metric-change positive">
                    <i class="fas fa-check-circle me-1"></i>
                    {{ $metrics['approved_sponsorships'] }} approuvés
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="container">
    <div class="row">
        <!-- Investment Trend Chart -->
        <div class="col-lg-8 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-chart-area me-2"></i>
                        Tendance des Investissements
                    </h3>
                </div>
                <div class="chart-canvas">
                    <canvas id="investmentTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-pie-chart me-2"></i>
                        Répartition par Statut
                    </h3>
                </div>
                <div class="chart-canvas">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- ROI Performance -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-trophy me-2"></i>
                        Performance ROI par Événement
                    </h3>
                </div>
                <div class="chart-canvas">
                    <canvas id="roiChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Category Distribution -->
        <div class="col-lg-6 mb-4">
            <div class="chart-container">
                <div class="chart-header">
                    <h3 class="chart-title">
                        <i class="fas fa-tags me-2"></i>
                        Répartition par Catégorie
                    </h3>
                </div>
                <div class="chart-canvas">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Top Events Table -->
<div class="container mb-4">
    <div class="top-events-table">
        <div class="table-header">
            <h3 class="mb-0">
                <i class="fas fa-star me-2"></i>
                Top 10 des Événements Sponsorisés
            </h3>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Événement</th>
                        <th>Montant</th>
                        <th>Statut</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($topEvents as $event)
                    <tr>
                        <td>
                            <div class="fw-bold">{{ $event->event_title }}</div>
                        </td>
                        <td>
                            <span class="fw-bold text-success">{{ number_format($event->amount, 0) }} TND</span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $event->status }}">
                                {{ ucfirst($event->status) }}
                            </span>
                        </td>
                        <td>{{ \Carbon\Carbon::parse($event->created_at)->format('d/m/Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="fas fa-chart-line fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun événement sponsorisé pour cette période</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Monthly Performance -->
<div class="container mb-4">
    <div class="chart-container">
        <div class="chart-header">
            <h3 class="chart-title">
                <i class="fas fa-calendar-alt me-2"></i>
                Performance Mensuelle
            </h3>
        </div>
        <div class="chart-canvas">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
let charts = {};
let currentPeriod = '{{ $period }}';

// Chart colors
const colors = {
    primary: '#667eea',
    secondary: '#764ba2',
    success: '#28a745',
    warning: '#ffc107',
    danger: '#dc3545',
    info: '#17a2b8',
    light: '#f8f9fa',
    dark: '#343a40'
};

// Initialize all charts
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Investment Trend Chart
    charts.investmentTrend = new Chart(document.getElementById('investmentTrendChart'), {
        type: 'line',
        data: {
            labels: @json($chartData['investment_trend']['labels']),
            datasets: [{
                label: 'Montant Investi (TND)',
                data: @json($chartData['investment_trend']['amounts']),
                borderColor: colors.primary,
                backgroundColor: colors.primary + '20',
                borderWidth: 3,
                fill: true,
                tension: 0.4
            }, {
                label: 'Nombre de Sponsorships',
                data: @json($chartData['investment_trend']['counts']),
                borderColor: colors.secondary,
                backgroundColor: colors.secondary + '20',
                borderWidth: 3,
                fill: false,
                tension: 0.4,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (TND)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Nombre de Sponsorships'
                    },
                    grid: {
                        drawOnChartArea: false,
                    },
                }
            },
            plugins: {
                legend: {
                    position: 'top',
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                }
            }
        }
    });

    // Status Distribution Chart
    charts.status = new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: @json($chartData['status_distribution']['labels']),
            datasets: [{
                data: @json($chartData['status_distribution']['amounts']),
                backgroundColor: [
                    colors.success,
                    colors.warning,
                    colors.info,
                    colors.danger
                ],
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
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': ' + context.parsed.toLocaleString() + ' TND (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // ROI Performance Chart
    charts.roi = new Chart(document.getElementById('roiChart'), {
        type: 'bar',
        data: {
            labels: @json(collect($chartData['roi_performance'])->pluck('event_name')->take(10)->toArray()),
            datasets: [{
                label: 'ROI Estimé (%)',
                data: @json(collect($chartData['roi_performance'])->pluck('estimated_roi')->take(10)->toArray()),
                backgroundColor: colors.info,
                borderColor: colors.info,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'ROI (%)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'ROI: ' + context.parsed.y + '%';
                        }
                    }
                }
            }
        }
    });

    // Category Distribution Chart
    charts.category = new Chart(document.getElementById('categoryChart'), {
        type: 'bar',
        data: {
            labels: @json($chartData['category_distribution']['labels']),
            datasets: [{
                label: 'Montant Investi (TND)',
                data: @json($chartData['category_distribution']['amounts']),
                backgroundColor: colors.primary,
                borderColor: colors.primary,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (TND)'
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Monthly Performance Chart
    charts.monthly = new Chart(document.getElementById('monthlyChart'), {
        type: 'bar',
        data: {
            labels: @json($monthlyPerformance->pluck('month')->toArray()),
            datasets: [{
                label: 'Total Investi',
                data: @json($monthlyPerformance->pluck('total_invested')->toArray()),
                backgroundColor: colors.primary,
                borderColor: colors.primary,
                borderWidth: 1
            }, {
                label: 'Sponsorships Approuvés',
                data: @json($monthlyPerformance->pluck('approved_amount')->toArray()),
                backgroundColor: colors.success,
                borderColor: colors.success,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (TND)'
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

function changePeriod(period) {
    currentPeriod = period;
    
    // Update active button
    document.querySelectorAll('.period-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Reload page with new period
    window.location.href = `{{ route('sponsor.analytics') }}?period=${period}`;
}

function refreshData() {
    // Show loading state
    const refreshBtn = event.target;
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Actualisation...';
    refreshBtn.disabled = true;
    
    // Reload page
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

function exportReport() {
    // Create export data
    const exportData = {
        period: currentPeriod,
        metrics: @json($metrics),
        comparison: @json($comparison),
        topEvents: @json($topEvents),
        generatedAt: new Date().toISOString()
    };
    
    // Create and download file
    const dataStr = JSON.stringify(exportData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `rapport-analytique-${currentPeriod}-${new Date().toISOString().split('T')[0]}.json`;
    link.click();
    URL.revokeObjectURL(url);
}

// Auto-refresh every 5 minutes
setInterval(() => {
    // Only refresh if user is active
    if (document.hasFocus()) {
        refreshData();
    }
}, 300000);
</script>
@endpush
