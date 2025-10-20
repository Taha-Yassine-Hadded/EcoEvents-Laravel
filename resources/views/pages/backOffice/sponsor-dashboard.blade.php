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
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stats-number text-primary">{{ $stats['total_events_available'] ?? 0 }}</div>
            <div class="stats-label">Événements disponibles</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number text-success">{{ $stats['upcoming_events'] ?? 0 }}</div>
            <div class="stats-label">À venir</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stats-number text-success">{{ $stats['ongoing_events'] ?? 0 }}</div>
            <div class="stats-label">En cours</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stats-number text-warning">{{ $stats['total_categories'] ?? 0 }}</div>
            <div class="stats-label">Catégories</div>
        </div>
    </div>
</div>

<!-- Sponsoring Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3"><i class="fas fa-handshake"></i> Mes Sponsoring</h5>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-number text-primary">{{ $sponsorshipStats['total_proposals'] ?? 0 }}</div>
            <div class="stats-label">Total Propositions</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number text-warning">{{ $sponsorshipStats['pending_proposals'] ?? 0 }}</div>
            <div class="stats-label">En Attente</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number text-success">{{ $sponsorshipStats['approved_proposals'] ?? 0 }}</div>
            <div class="stats-label">Approuvés</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number text-danger">{{ $sponsorshipStats['rejected_proposals'] ?? 0 }}</div>
            <div class="stats-label">Rejetés</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stats-number text-info">{{ number_format($sponsorshipStats['total_invested'] ?? 0, 0, ',', ' ') }} €</div>
            <div class="stats-label">Total Investi</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                <i class="fas fa-list"></i>
            </div>
            <div class="stats-number text-secondary">Voir Tout</div>
            <div class="stats-label">
                <a href="{{ route('sponsor.sponsorships') }}" class="text-decoration-none">Mes Sponsoring</a>
            </div>
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

<!-- Recommander pour vous -->
<div class="row mb-4">
    <div class="col-12">
        <div class="campaign-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-brain fa-2x text-primary me-3"></i>
                    <div>
                        <h5 class="mb-0">Recommander pour vous</h5>
                        <small class="text-muted">Événements personnalisés pour votre profil</small>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshAIRecommendations()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                    <a href="{{ route('sponsor.ai.recommendations') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i> Voir Tout
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div id="aiRecommendationsContainer">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2 text-muted">L'IA analyse votre profil pour des recommandations personnalisées...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mes Sponsoring Acceptés -->
@if(isset($sponsorships) && $sponsorships->where('status', 'approved')->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="campaign-card">
            <div class="card-body py-4">
                <h5 class="mb-4"><i class="fas fa-check-circle text-success"></i> Mes Sponsoring Acceptés</h5>
                <div class="row">
                    @foreach($sponsorships->where('status', 'approved')->take(3) as $sponsorship)
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded h-100 bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 text-success">{{ $sponsorship->event->title ?? 'Événement' }}</h6>
                                    <span class="badge bg-success">Approuvé</span>
                                </div>
                                <div class="text-muted small mb-2">
                                    <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($sponsorship->event->date ?? now())->format('d/m/Y') }}
                                </div>
                                <div class="mb-2">
                                    <strong class="text-primary">{{ $sponsorship->package_name }}</strong>
                                    <div class="text-success font-weight-bold">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</div>
                                </div>
                                @if($sponsorship->notes)
                                    <p class="text-muted small mb-2">{{ Str::limit($sponsorship->notes, 60) }}</p>
                                @endif
                                <div class="text-muted small">
                                    <i class="fas fa-clock"></i> Accepté le {{ $sponsorship->updated_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($sponsorships->where('status', 'approved')->count() > 3)
                    <div class="text-center mt-3">
                        <a href="{{ route('sponsor.sponsorships') }}" class="btn btn-outline-success">
                            <i class="fas fa-list"></i> Voir tous mes sponsoring acceptés
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Featured Campaigns Row -->
<div class="row">
    <div class="col-12">
        <div class="campaign-card">
            @if(isset($events) && count($events))
                <div class="card-body py-4">
                    <h5 class="mb-4">Événements à sponsoriser</h5>
                    <div class="row">
                        @foreach($events as $event)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="p-3 border rounded h-100">
                                    <h6 class="mb-1">{{ $event->title }}</h6>
                                    <small class="text-muted">{{ optional($event->category)->name }} • {{ optional($event->date)->format('d/m/Y H:i') }}</small>
                                    <p class="mt-2 mb-0 text-muted" style="font-size: 13px;">{{ Str::limit($event->description, 90) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Voir tous les événements
                        </a>
                    </div>
                </div>
            @else
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Aucun événement disponible pour le moment</h4>
                    <p class="text-muted">Revenez plus tard pour découvrir de nouveaux événements à sponsoriser.</p>
                    <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                        <i class="fas fa-refresh"></i> Actualiser
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Charger les recommandations IA au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadAIRecommendations();
});

// Charger les recommandations IA pour le dashboard
async function loadAIRecommendations() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/ai/recommendations/events?limit=3', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success && data.recommendations.length > 0) {
                displayAIRecommendations(data.recommendations);
            } else {
                displayNoRecommendations();
            }
        } else {
            displayNoRecommendations();
        }
    } catch (error) {
        console.error('Erreur lors du chargement des recommandations IA:', error);
        displayNoRecommendations();
    }
}

// Afficher les recommandations IA
function displayAIRecommendations(recommendations) {
    const container = document.getElementById('aiRecommendationsContainer');
    
    let html = '<div class="row">';
    
    recommendations.forEach((rec, index) => {
        const event = rec.event;
        const score = rec.score;
        const estimatedROI = rec.estimated_roi;
        
        // Déterminer la classe CSS basée sur le score
        let scoreClass = 'success';
        let scoreText = 'Excellent';
        if (score < 60) {
            scoreClass = 'warning';
            scoreText = 'Bon';
        }
        if (score < 40) {
            scoreClass = 'danger';
            scoreText = 'Faible';
        }
        
        html += `
            <div class="col-md-4 mb-3">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h6 class="card-title mb-0">${event.title}</h6>
                            <span class="badge bg-${scoreClass}">${score.toFixed(1)}%</span>
                        </div>
                        
                        <div class="mb-2">
                            <small class="text-muted">
                                <i class="fas fa-map-marker-alt"></i> ${event.location || 'Lieu non spécifié'}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> 
                                ${event.date ? new Date(event.date).toLocaleDateString('fr-FR') : 'Date non spécifiée'}
                            </small>
                        </div>
                        
                        <div class="mb-3">
                            <div class="d-flex justify-content-between mb-1">
                                <small>ROI Estimé:</small>
                                <strong class="text-success">${estimatedROI.toFixed(1)}%</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>Packages:</small>
                                <span class="badge bg-info">${event.packages ? event.packages.length : 0}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="small mb-1">Pourquoi cette recommandation:</h6>
                            <ul class="list-unstyled small mb-0">
                                ${rec.reasons.slice(0, 2).map(reason => `<li><i class="fas fa-check text-success"></i> ${reason}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="proposeSponsorshipForEvent(${event.id})">
                                <i class="fas fa-paper-plane"></i> Proposer Sponsorship
                            </button>
                            <button class="btn btn-outline-info btn-sm" onclick="showEventDetails(${event.id})">
                                <i class="fas fa-info-circle"></i> Détails
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    
    // Ajouter un lien vers la page complète des recommandations
    html += `
        <div class="text-center mt-3">
            <a href="{{ route('sponsor.ai.recommendations') }}" class="btn btn-outline-primary">
                <i class="fas fa-brain"></i> Voir toutes les recommandations IA
            </a>
        </div>
    `;
    
    container.innerHTML = html;
}

// Afficher message quand aucune recommandation
function displayNoRecommendations() {
    const container = document.getElementById('aiRecommendationsContainer');
    container.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucune recommandation disponible</h5>
            <p class="text-muted">Complétez votre profil pour recevoir des recommandations personnalisées</p>
            <div class="d-flex justify-content-center gap-2">
                <a href="{{ route('sponsor.profile') }}" class="btn btn-primary">
                    <i class="fas fa-user-edit"></i> Compléter le Profil
                </a>
                <button class="btn btn-outline-primary" onclick="loadAIRecommendations()">
                    <i class="fas fa-sync-alt"></i> Réessayer
                </button>
            </div>
        </div>
    `;
}

// Actualiser les recommandations IA
function refreshAIRecommendations() {
    const container = document.getElementById('aiRecommendationsContainer');
    container.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Actualisation...</span>
            </div>
            <p class="mt-2 text-muted">Actualisation des recommandations...</p>
        </div>
    `;
    loadAIRecommendations();
}

// Proposer un sponsorship pour un événement
function proposeSponsorshipForEvent(eventId) {
    window.location.href = `/sponsor/campaigns/${eventId}`;
}

// Afficher les détails d'un événement
function showEventDetails(eventId) {
    window.location.href = `/events/${eventId}`;
}
</script>
@endpush

@endsection