@extends('layouts.sponsor')

@section('title', 'Recommander pour vous - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-brain text-primary"></i>
        Recommander pour vous
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Recommander pour vous</li>
        </ol>
    </nav>
</div>

<!-- AI Recommendations Dashboard -->
<div class="row">
    <!-- Profile Insights Card -->
    <div class="col-lg-4 mb-4">
        <div class="stats-card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-user-cog fa-2x text-info me-3"></i>
                <div>
                    <h5 class="mb-0">Profil Sponsor</h5>
                    <small class="text-muted">Analyse de votre profil</small>
                </div>
            </div>
            <div class="card-body">
                <div id="profileInsights">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2">Analyse de votre profil en cours...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Confidence Score Card -->
    <div class="col-lg-4 mb-4">
        <div class="stats-card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-chart-line fa-2x text-success me-3"></i>
                <div>
                    <h5 class="mb-0">Score de Confiance</h5>
                    <small class="text-muted">Qualité des recommandations</small>
                </div>
            </div>
            <div class="card-body">
                <div id="confidenceScore">
                    <div class="text-center">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <p class="mt-2">Calcul du score de confiance...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-lg-4 mb-4">
        <div class="stats-card">
            <div class="card-header d-flex align-items-center">
                <i class="fas fa-bolt fa-2x text-warning me-3"></i>
                <div>
                    <h5 class="mb-0">Actions Rapides</h5>
                    <small class="text-muted">Recommandations personnalisées</small>
                </div>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" onclick="refreshRecommendations()">
                        <i class="fas fa-sync-alt"></i> Actualiser les Recommandations
                    </button>
                    <button class="btn btn-outline-info" onclick="showProfileInsights()">
                        <i class="fas fa-chart-pie"></i> Voir les Insights
                    </button>
                    <button class="btn btn-outline-success" onclick="exportRecommendations()">
                        <i class="fas fa-download"></i> Exporter les Données
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- AI Recommendations Content -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-magic fa-2x text-primary me-3"></i>
                    <div>
                        <h5 class="mb-0">Événements Recommandés</h5>
                        <small class="text-muted">Basés sur votre profil et historique</small>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" onclick="filterRecommendations('high')">
                        <i class="fas fa-star"></i> Score Élevé
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="filterRecommendations('medium')">
                        <i class="fas fa-thumbs-up"></i> Score Moyen
                    </button>
                    <button class="btn btn-outline-warning btn-sm" onclick="filterRecommendations('low')">
                        <i class="fas fa-info-circle"></i> Tous
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="recommendationsContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <h5 class="mt-3">Analyse de vos préférences...</h5>
                        <p class="text-muted">L'IA analyse votre profil pour vous proposer les meilleures opportunités</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour les détails d'une recommandation -->
<div class="modal fade" id="recommendationDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-brain text-primary"></i>
                    Détails de la Recommandation
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="recommendationDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="proposeSponsorship()">
                    <i class="fas fa-paper-plane"></i> Proposer un Sponsorship
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentRecommendations = [];
let currentProfile = null;

// Charger les recommandations au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadRecommendations();
    loadProfileInsights();
});

// Charger les recommandations d'événements
async function loadRecommendations() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/ai/recommendations/events', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                currentRecommendations = data.recommendations;
                displayRecommendations(data.recommendations);
                updateConfidenceScore(data.confidence_score);
                currentProfile = data.user_profile;
            } else {
                showError('Erreur lors du chargement des recommandations: ' + data.error);
            }
        } else {
            showError('Erreur de connexion lors du chargement des recommandations');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur lors du chargement des recommandations');
    }
}

// Afficher les recommandations
function displayRecommendations(recommendations) {
    const container = document.getElementById('recommendationsContainer');
    
    if (recommendations.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune recommandation disponible</h5>
                <p class="text-muted">Complétez votre profil pour recevoir des recommandations personnalisées</p>
            </div>
        `;
        return;
    }

    let html = '<div class="row">';
    
    recommendations.forEach((rec, index) => {
        const event = rec.event;
        const score = rec.score;
        const riskLevel = rec.risk_level;
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
        
        // Déterminer la classe de risque
        let riskClass = 'success';
        let riskText = 'Faible';
        if (riskLevel === 'medium') {
            riskClass = 'warning';
            riskText = 'Moyen';
        } else if (riskLevel === 'high') {
            riskClass = 'danger';
            riskText = 'Élevé';
        }
        
        html += `
            <div class="col-lg-6 mb-4 recommendation-card" data-score="${score}">
                <div class="card h-100 shadow-sm">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">${event.title}</h6>
                        <span class="badge bg-${scoreClass}">${score.toFixed(1)}%</span>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <small class="text-muted">${event.location || 'Lieu non spécifié'}</small>
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
                            <div class="d-flex justify-content-between mb-1">
                                <small>Risque:</small>
                                <span class="badge bg-${riskClass}">${riskText}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <small>Packages:</small>
                                <span class="badge bg-info">${event.packages ? event.packages.length : 0}</span>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <h6 class="small mb-2">Raisons de la recommandation:</h6>
                            <ul class="list-unstyled small">
                                ${rec.reasons.map(reason => `<li><i class="fas fa-check text-success"></i> ${reason}</li>`).join('')}
                            </ul>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-grid gap-2">
                            <button class="btn btn-primary btn-sm" onclick="showRecommendationDetails(${index})">
                                <i class="fas fa-eye"></i> Voir Détails
                            </button>
                            <button class="btn btn-outline-success btn-sm" onclick="proposeSponsorshipForEvent(${event.id})">
                                <i class="fas fa-paper-plane"></i> Proposer Sponsorship
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    html += '</div>';
    container.innerHTML = html;
}

// Afficher les détails d'une recommandation
function showRecommendationDetails(index) {
    const rec = currentRecommendations[index];
    const event = rec.event;
    
    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-info-circle text-primary"></i> Informations de l'Événement</h6>
                <p><strong>Titre:</strong> ${event.title}</p>
                <p><strong>Description:</strong> ${event.description || 'Aucune description disponible'}</p>
                <p><strong>Lieu:</strong> ${event.location || 'Non spécifié'}</p>
                <p><strong>Date:</strong> ${event.date ? new Date(event.date).toLocaleDateString('fr-FR') : 'Non spécifiée'}</p>
                <p><strong>Capacité:</strong> ${event.capacity || 'Non spécifiée'}</p>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-chart-bar text-success"></i> Analyse IA</h6>
                <p><strong>Score de Compatibilité:</strong> <span class="badge bg-success">${rec.score.toFixed(1)}%</span></p>
                <p><strong>ROI Estimé:</strong> <span class="text-success">${rec.estimated_roi.toFixed(1)}%</span></p>
                <p><strong>Niveau de Risque:</strong> <span class="badge bg-${rec.risk_level === 'low' ? 'success' : rec.risk_level === 'medium' ? 'warning' : 'danger'}">${rec.risk_level}</span></p>
                
                <h6 class="mt-3">Raisons de la Recommandation:</h6>
                <ul>
                    ${rec.reasons.map(reason => `<li>${reason}</li>`).join('')}
                </ul>
            </div>
        </div>
        
        ${event.packages && event.packages.length > 0 ? `
            <div class="mt-4">
                <h6><i class="fas fa-box text-info"></i> Packages Disponibles</h6>
                <div class="row">
                    ${event.packages.map(pkg => `
                        <div class="col-md-4 mb-2">
                            <div class="card">
                                <div class="card-body p-2">
                                    <h6 class="card-title">${pkg.name}</h6>
                                    <p class="card-text small">${pkg.description || 'Aucune description'}</p>
                                    <strong class="text-primary">${pkg.price} TND</strong>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            </div>
        ` : ''}
    `;
    
    document.getElementById('recommendationDetailsContent').innerHTML = content;
    
    const modal = new bootstrap.Modal(document.getElementById('recommendationDetailsModal'));
    modal.show();
}

// Charger les insights du profil
async function loadProfileInsights() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/ai/insights/profile', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                displayProfileInsights(data.insights);
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des insights:', error);
    }
}

// Afficher les insights du profil
function displayProfileInsights(insights) {
    const container = document.getElementById('profileInsights');
    
    const profileCompleteness = insights.profile_completeness;
    const history = insights.sponsorship_history;
    
    container.innerHTML = `
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <small>Complétude du Profil</small>
                <strong>${profileCompleteness.percentage}%</strong>
            </div>
            <div class="progress" style="height: 8px;">
                <div class="progress-bar bg-info" style="width: ${profileCompleteness.percentage}%"></div>
            </div>
        </div>
        
        <div class="mb-3">
            <div class="row text-center">
                <div class="col-6">
                    <div class="border-end">
                        <h5 class="text-primary mb-0">${history.total_sponsorships}</h5>
                        <small class="text-muted">Sponsorships</small>
                    </div>
                </div>
                <div class="col-6">
                    <h5 class="text-success mb-0">${history.success_rate}%</h5>
                    <small class="text-muted">Taux de Succès</small>
                </div>
            </div>
        </div>
        
        <div class="mb-2">
            <small class="text-muted">Investissement Total:</small>
            <strong class="text-success">${history.total_invested.toLocaleString()} TND</strong>
        </div>
        
        ${insights.recommendations.length > 0 ? `
            <div class="mt-3">
                <small class="text-muted">Recommandations:</small>
                <ul class="list-unstyled small mt-1">
                    ${insights.recommendations.map(rec => `<li><i class="fas fa-lightbulb text-warning"></i> ${rec}</li>`).join('')}
                </ul>
            </div>
        ` : ''}
    `;
}

// Mettre à jour le score de confiance
function updateConfidenceScore(score) {
    const container = document.getElementById('confidenceScore');
    
    let scoreClass = 'success';
    let scoreText = 'Excellent';
    if (score < 70) {
        scoreClass = 'warning';
        scoreText = 'Bon';
    }
    if (score < 50) {
        scoreClass = 'danger';
        scoreText = 'Faible';
    }
    
    container.innerHTML = `
        <div class="text-center">
            <div class="display-4 text-${scoreClass} mb-2">${score.toFixed(1)}%</div>
            <p class="mb-0">${scoreText}</p>
            <small class="text-muted">Qualité des données</small>
        </div>
    `;
}

// Filtrer les recommandations
function filterRecommendations(filter) {
    const cards = document.querySelectorAll('.recommendation-card');
    
    cards.forEach(card => {
        const score = parseFloat(card.dataset.score);
        let show = true;
        
        switch(filter) {
            case 'high':
                show = score >= 70;
                break;
            case 'medium':
                show = score >= 40 && score < 70;
                break;
            case 'low':
                show = true;
                break;
        }
        
        card.style.display = show ? 'block' : 'none';
    });
}

// Actualiser les recommandations
function refreshRecommendations() {
    loadRecommendations();
    loadProfileInsights();
}

// Proposer un sponsorship pour un événement
function proposeSponsorshipForEvent(eventId) {
    window.location.href = `/sponsor/campaigns/${eventId}`;
}

// Afficher les insights du profil
function showProfileInsights() {
    // Implémenter l'affichage détaillé des insights
    alert('Fonctionnalité en cours de développement');
}

// Exporter les recommandations
function exportRecommendations() {
    // Implémenter l'export des recommandations
    alert('Fonctionnalité en cours de développement');
}

// Afficher une erreur
function showError(message) {
    const container = document.getElementById('recommendationsContainer');
    container.innerHTML = `
        <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
            <h5>Erreur</h5>
            <p>${message}</p>
            <button class="btn btn-primary" onclick="loadRecommendations()">
                <i class="fas fa-retry"></i> Réessayer
            </button>
        </div>
    `;
}
</script>
@endpush

@endsection
