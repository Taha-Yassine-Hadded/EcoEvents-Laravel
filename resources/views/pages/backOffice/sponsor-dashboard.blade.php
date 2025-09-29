@extends('layouts.sponsor')

@section('title', 'Dashboard Sponsor - Echofy')

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <h4><i class="fas fa-handshake"></i> Bienvenue, {{ $user->name }} !</h4>
    <p>En tant que sponsor, vous pouvez découvrir et soutenir des événements écologiques. Explorez les campagnes disponibles et proposez votre soutien financier.</p>
</div>

<!-- Statistics Cards Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stats-number text-primary">{{ $stats['total_campaigns_viewed'] }}</div>
            <div class="stats-label">Campagnes Vues</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-paper-plane"></i>
            </div>
            <div class="stats-number text-success">{{ $stats['sponsorships_proposed'] }}</div>
            <div class="stats-label">Sponsorships Proposés</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number text-success">{{ $stats['sponsorships_approved'] }}</div>
            <div class="stats-label">Sponsorships Approuvés</div>
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
</div>

<!-- Quick Actions Row -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
            <h5>Campagnes</h5>
            <p class="text-muted">Découvrez les événements à sponsoriser</p>
            <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> Voir les Campagnes
            </a>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-user-edit fa-3x text-success mb-3"></i>
            <h5>Mon Profil</h5>
            <p class="text-muted">Gérez vos informations personnelles</p>
            <a href="{{ route('sponsor.profile') }}" class="btn btn-success">
                <i class="fas fa-edit"></i> Modifier le Profil
            </a>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-building fa-3x text-primary mb-3"></i>
            <h5>Mon Entreprise</h5>
            <p class="text-muted">Gérez les informations de votre entreprise</p>
            <a href="{{ route('sponsor.company') }}" class="btn btn-primary">
                <i class="fas fa-building"></i> Gérer l'Entreprise
            </a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-file-contract fa-3x text-warning mb-3"></i>
            <h5>Mes Sponsorships</h5>
            <p class="text-muted">Suivez vos accords de sponsoring</p>
                <a href="{{ route('sponsor.sponsorships') }}" class="btn btn-warning">
                    <i class="fas fa-list"></i> Voir mes Sponsorships
                </a>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
            <h5>Statistiques</h5>
            <p class="text-muted">Analysez vos performances</p>
                <a href="{{ route('sponsor.statistics') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar"></i> Voir les Stats
                </a>
        </div>
    </div>
</div>

<!-- Featured Campaigns Row -->
<div class="row">
    <div class="col-12">
        <div class="campaign-card">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                <h4 class="text-muted">Aucune campagne disponible pour le moment</h4>
                <p class="text-muted">Revenez plus tard pour découvrir de nouveaux événements à sponsoriser.</p>
                <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                    <i class="fas fa-refresh"></i> Actualiser
                </a>
            </div>
        </div>
    </div>
</div>
@endsection