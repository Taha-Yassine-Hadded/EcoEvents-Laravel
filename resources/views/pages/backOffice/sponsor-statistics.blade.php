@extends('layouts.sponsor')

@section('title', 'Statistiques - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-chart-line text-primary"></i>
        Mes Statistiques
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Statistiques</li>
        </ol>
    </nav>
</div>

<!-- Statistics Overview -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-handshake"></i>
            </div>
            <div class="stats-number text-primary">{{ $stats['total_sponsorships'] }}</div>
            <div class="stats-label">Total Sponsorships</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-coins"></i>
            </div>
            <div class="stats-number text-warning">{{ number_format($stats['total_invested'], 0) }} TND</div>
            <div class="stats-label">Total Investi</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number text-info">{{ $stats['pending_sponsorships'] }}</div>
            <div class="stats-label">En Attente</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number text-success">{{ $stats['approved_sponsorships'] }}</div>
            <div class="stats-label">Approuvés</div>
        </div>
    </div>
</div>

<!-- Detailed Statistics -->
<div class="row">
    <!-- Sponsorships Status Chart -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-primary"></i> Répartition des Sponsorships
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-clock text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">En Attente</div>
                                <div class="text-muted">{{ $stats['pending_sponsorships'] }} sponsorships</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="rounded-circle bg-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-check text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">Approuvés</div>
                                <div class="text-muted">{{ $stats['approved_sponsorships'] }} sponsorships</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-times text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">Rejetés</div>
                                <div class="text-muted">{{ $stats['rejected_sponsorships'] }} sponsorships</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <div class="rounded-circle bg-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="fas fa-flag-checkered text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="fw-bold">Terminés</div>
                                <div class="text-muted">{{ $stats['completed_sponsorships'] }} sponsorships</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Investment Summary -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-success"></i> Résumé des Investissements
                </h5>
            </div>
            <div class="card-body">
                <div class="investment-summary">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Total Investi :</span>
                            <span class="fw-bold text-success">{{ number_format($stats['total_invested'], 0) }} TND</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Sponsorships Actifs :</span>
                            <span class="fw-bold text-primary">{{ $stats['approved_sponsorships'] }}</span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar" 
                                 style="width: {{ $stats['total_sponsorships'] > 0 ? ($stats['approved_sponsorships'] / $stats['total_sponsorships']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span>Taux de Réussite :</span>
                            <span class="fw-bold text-info">
                                {{ $stats['total_sponsorships'] > 0 ? round(($stats['approved_sponsorships'] / $stats['total_sponsorships']) * 100, 1) : 0 }}%
                            </span>
                        </div>
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-info" role="progressbar" 
                                 style="width: {{ $stats['total_sponsorships'] > 0 ? ($stats['approved_sponsorships'] / $stats['total_sponsorships']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Insights -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lightbulb text-warning"></i> Insights de Performance
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="insight-card text-center p-3 border rounded">
                            <i class="fas fa-trophy fa-2x text-warning mb-2"></i>
                            <h6>Meilleure Performance</h6>
                            <p class="text-muted small mb-0">
                                @if($stats['approved_sponsorships'] > 0)
                                    {{ $stats['approved_sponsorships'] }} sponsorships approuvés
                                @else
                                    Aucun sponsorship approuvé pour le moment
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="insight-card text-center p-3 border rounded">
                            <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                            <h6>Investissement Moyen</h6>
                            <p class="text-muted small mb-0">
                                @if($stats['total_sponsorships'] > 0)
                                    {{ number_format($stats['total_invested'] / $stats['total_sponsorships'], 0) }} TND par sponsorship
                                @else
                                    Aucun investissement pour le moment
                                @endif
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="insight-card text-center p-3 border rounded">
                            <i class="fas fa-target fa-2x text-primary mb-2"></i>
                            <h6>Objectif</h6>
                            <p class="text-muted small mb-0">
                                Continuez à proposer des sponsorships pour augmenter votre impact écologique
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-bolt text-primary"></i> Actions Rapides
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary w-100">
                            <i class="fas fa-calendar-alt"></i> Voir les Campagnes
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('sponsor.sponsorships') }}" class="btn btn-success w-100">
                            <i class="fas fa-handshake"></i> Mes Sponsorships
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('sponsor.company') }}" class="btn btn-warning w-100">
                            <i class="fas fa-building"></i> Mon Entreprise
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="{{ route('sponsor.profile') }}" class="btn btn-info w-100">
                            <i class="fas fa-user-edit"></i> Mon Profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection