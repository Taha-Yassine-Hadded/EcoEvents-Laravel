@extends('layouts.sponsor')

@section('title', 'Feedback & Commentaires - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-comments text-primary"></i>
        Feedback & Commentaires
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Feedback</li>
        </ol>
    </nav>
</div>

<!-- Filters and Actions -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary" onclick="filterFeedbacks('all')">
                <i class="fas fa-list"></i> Tous
            </button>
            <button type="button" class="btn btn-outline-success" onclick="filterFeedbacks('post_event')">
                <i class="fas fa-calendar-times"></i> Post-Événement
            </button>
            <button type="button" class="btn btn-outline-info" onclick="filterFeedbacks('experience_sharing')">
                <i class="fas fa-share-alt"></i> Expériences
            </button>
            <button type="button" class="btn btn-outline-warning" onclick="filterFeedbacks('improvement_suggestion')">
                <i class="fas fa-lightbulb"></i> Suggestions
            </button>
        </div>
    </div>
    <div class="col-md-6 text-end">
        <button class="btn btn-primary" onclick="showCreateFeedbackModal()">
            <i class="fas fa-plus"></i> Nouveau Feedback
        </button>
    </div>
</div>

<!-- Search Bar -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="input-group">
            <input type="text" class="form-control" id="searchInput" placeholder="Rechercher dans les feedbacks...">
            <button class="btn btn-outline-secondary" type="button" onclick="searchFeedbacks()">
                <i class="fas fa-search"></i>
            </button>
        </div>
    </div>
    <div class="col-md-4">
        <select class="form-select" id="sortSelect" onchange="sortFeedbacks()">
            <option value="created_at_desc">Plus récents</option>
            <option value="created_at_asc">Plus anciens</option>
            <option value="likes_count_desc">Plus populaires</option>
            <option value="rating_desc">Mieux notés</option>
        </select>
    </div>
</div>

<!-- Feedback Statistics -->
<div class="row mb-4" id="feedbackStats">
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stats-number text-primary" id="totalFeedbacks">0</div>
            <div class="stats-label">Total Feedbacks</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-star"></i>
            </div>
            <div class="stats-number text-warning" id="averageRating">0.0</div>
            <div class="stats-label">Note Moyenne</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stats-number text-info" id="totalLikes">0</div>
            <div class="stats-label">Total Likes</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div class="stats-number text-danger" id="eventsWithFeedback">0</div>
            <div class="stats-label">Événements Commentés</div>
        </div>
    </div>
</div>

<!-- Feedbacks Container -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <i class="fas fa-comments fa-2x text-primary me-3"></i>
                    <div>
                        <h5 class="mb-0">Feedbacks de la Communauté</h5>
                        <small class="text-muted">Partagez vos expériences et découvrez celles des autres sponsors</small>
                    </div>
                </div>
                <div class="btn-group">
                    <button class="btn btn-outline-primary btn-sm" onclick="refreshFeedbacks()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div id="feedbacksContainer">
                    <div class="text-center py-5">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                        <h5 class="mt-3">Chargement des feedbacks...</h5>
                        <p class="text-muted">Récupération des commentaires de la communauté</p>
                    </div>
                </div>
                
                <!-- Pagination -->
                <nav aria-label="Pagination des feedbacks" id="feedbackPagination" class="mt-4">
                    <!-- Pagination sera générée dynamiquement -->
                </nav>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour créer un feedback -->
<div class="modal fade" id="createFeedbackModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comment-plus text-primary"></i>
                    Nouveau Feedback
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="createFeedbackForm">
                    @csrf
                    
                    <!-- Type de feedback -->
                    <div class="mb-3">
                        <label for="feedbackType" class="form-label">Type de Feedback</label>
                        <select class="form-select" id="feedbackType" name="feedback_type" required>
                            <option value="">Sélectionner un type...</option>
                        </select>
                    </div>
                    
                    <!-- Événement -->
                    <div class="mb-3">
                        <label for="eventId" class="form-label">Événement</label>
                        <select class="form-select" id="eventId" name="event_id" required>
                            <option value="">Sélectionner un événement...</option>
                        </select>
                    </div>
                    
                    <!-- Sponsorship (optionnel) -->
                    <div class="mb-3">
                        <label for="sponsorshipTempId" class="form-label">Sponsorship (optionnel)</label>
                        <select class="form-select" id="sponsorshipTempId" name="sponsorship_temp_id">
                            <option value="">Aucun sponsorship spécifique</option>
                        </select>
                    </div>
                    
                    <!-- Note -->
                    <div class="mb-3">
                        <label for="rating" class="form-label">Note (optionnel)</label>
                        <div class="rating-input">
                            <input type="radio" name="rating" value="5" id="star5">
                            <label for="star5"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="4" id="star4">
                            <label for="star4"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="3" id="star3">
                            <label for="star3"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="2" id="star2">
                            <label for="star2"><i class="fas fa-star"></i></label>
                            <input type="radio" name="rating" value="1" id="star1">
                            <label for="star1"><i class="fas fa-star"></i></label>
                        </div>
                    </div>
                    
                    <!-- Titre -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Titre (optionnel)</label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Titre de votre feedback...">
                    </div>
                    
                    <!-- Contenu -->
                    <div class="mb-3">
                        <label for="content" class="form-label">Contenu <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="content" name="content" rows="5" placeholder="Partagez votre expérience..." required></textarea>
                        <div class="form-text">Minimum 10 caractères, maximum 2000 caractères</div>
                    </div>
                    
                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags (optionnel)</label>
                        <input type="text" class="form-control" id="tags" name="tags" placeholder="Ex: excellent, communication, value-for-money">
                        <div class="form-text">Séparez les tags par des virgules</div>
                    </div>
                    
                    <!-- Options -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="isAnonymous" name="is_anonymous">
                            <label class="form-check-label" for="isAnonymous">
                                Publier de manière anonyme
                            </label>
                        </div>
                    </div>
                    
                    <!-- Fichiers joints -->
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Fichiers joints (optionnel)</label>
                        <input type="file" class="form-control" id="attachments" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                        <div class="form-text">Formats acceptés: JPG, PNG, PDF, DOC, DOCX (max 5MB par fichier)</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="submitFeedback()">
                    <i class="fas fa-paper-plane"></i> Publier
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour voir les détails d'un feedback -->
<div class="modal fade" id="feedbackDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-comment text-primary"></i>
                    Détails du Feedback
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="feedbackDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentFeedbacks = [];
let currentFilters = {
    type: 'all',
    sort: 'created_at_desc',
    search: ''
};

// Charger les feedbacks au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadFeedbacks();
    loadFeedbackTypes();
    loadEvents();
    loadUserSponsorships();
});

// Charger les feedbacks
async function loadFeedbacks() {
    try {
        const token = localStorage.getItem('jwt_token');
        let url = '/api/sponsor/feedback?';
        
        if (currentFilters.type !== 'all') {
            url += `type=${currentFilters.type}&`;
        }
        
        url += `sort_by=${currentFilters.sort.split('_')[0]}&sort_order=${currentFilters.sort.split('_')[1]}`;
        
        if (currentFilters.search) {
            url += `&q=${encodeURIComponent(currentFilters.search)}`;
        }
        
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                currentFeedbacks = data.feedbacks.data || data.feedbacks;
                displayFeedbacks(currentFeedbacks);
                updateStats(data.stats);
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des feedbacks:', error);
        showError('Erreur lors du chargement des feedbacks');
    }
}

// Afficher les feedbacks
function displayFeedbacks(feedbacks) {
    const container = document.getElementById('feedbacksContainer');
    
    if (feedbacks.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun feedback trouvé</h5>
                <p class="text-muted">Soyez le premier à partager votre expérience !</p>
                <button class="btn btn-primary" onclick="showCreateFeedbackModal()">
                    <i class="fas fa-plus"></i> Créer un Feedback
                </button>
            </div>
        `;
        return;
    }

    let html = '';
    
    feedbacks.forEach(feedback => {
        const typeIcon = getTypeIcon(feedback.feedback_type);
        const typeLabel = getTypeLabel(feedback.feedback_type);
        const stars = generateStars(feedback.rating);
        
        html += `
            <div class="card mb-3 feedback-card" data-type="${feedback.feedback_type}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="d-flex align-items-center">
                            <img src="${feedback.display_avatar}" alt="Avatar" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                            <div>
                                <h6 class="mb-0">${feedback.display_name}</h6>
                                <small class="text-muted">
                                    <i class="${typeIcon}"></i> ${typeLabel}
                                    <span class="ms-2"><i class="fas fa-clock"></i> ${feedback.time_ago}</span>
                                </small>
                            </div>
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" onclick="showFeedbackDetails(${feedback.id})">
                                    <i class="fas fa-eye"></i> Voir détails
                                </a></li>
                                ${feedback.user_id === {{ $user->id }} ? `
                                    <li><a class="dropdown-item" href="#" onclick="editFeedback(${feedback.id})">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a></li>
                                    <li><a class="dropdown-item text-danger" href="#" onclick="deleteFeedback(${feedback.id})">
                                        <i class="fas fa-trash"></i> Supprimer
                                    </a></li>
                                ` : ''}
                            </ul>
                        </div>
                    </div>
                    
                    ${feedback.title ? `<h6 class="card-title">${feedback.title}</h6>` : ''}
                    
                    <p class="card-text">${feedback.content}</p>
                    
                    ${feedback.rating ? `
                        <div class="mb-2">
                            <span class="text-warning">${stars}</span>
                            <span class="ms-2 text-muted">(${feedback.rating}/5)</span>
                        </div>
                    ` : ''}
                    
                    ${feedback.tags && feedback.tags.length > 0 ? `
                        <div class="mb-2">
                            ${feedback.tags.map(tag => `<span class="badge bg-secondary me-1">${tag}</span>`).join('')}
                        </div>
                    ` : ''}
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <button class="btn btn-sm btn-outline-primary" onclick="toggleLike(${feedback.id})">
                                <i class="fas fa-heart"></i> ${feedback.likes_count || 0}
                            </button>
                            ${feedback.has_replies ? `
                                <button class="btn btn-sm btn-outline-info ms-2" onclick="showReplies(${feedback.id})">
                                    <i class="fas fa-reply"></i> Réponses
                                </button>
                            ` : ''}
                        </div>
                        <small class="text-muted">
                            Événement: <strong>${feedback.event ? feedback.event.title : 'N/A'}</strong>
                        </small>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Filtrer les feedbacks
function filterFeedbacks(type) {
    currentFilters.type = type;
    
    // Mettre à jour les boutons actifs
    document.querySelectorAll('.btn-group .btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    loadFeedbacks();
}

// Rechercher des feedbacks
function searchFeedbacks() {
    const query = document.getElementById('searchInput').value.trim();
    currentFilters.search = query;
    loadFeedbacks();
}

// Trier les feedbacks
function sortFeedbacks() {
    currentFilters.sort = document.getElementById('sortSelect').value;
    loadFeedbacks();
}

// Actualiser les feedbacks
function refreshFeedbacks() {
    loadFeedbacks();
}

// Afficher le modal de création
function showCreateFeedbackModal() {
    const modal = new bootstrap.Modal(document.getElementById('createFeedbackModal'));
    modal.show();
}

// Soumettre un feedback
async function submitFeedback() {
    const form = document.getElementById('createFeedbackForm');
    const formData = new FormData(form);
    
    // Convertir les tags en array
    const tagsInput = document.getElementById('tags').value;
    if (tagsInput) {
        formData.set('tags', JSON.stringify(tagsInput.split(',').map(tag => tag.trim())));
    }
    
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/feedback', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            },
            body: formData
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                // Fermer le modal
                bootstrap.Modal.getInstance(document.getElementById('createFeedbackModal')).hide();
                
                // Réinitialiser le formulaire
                form.reset();
                
                // Recharger les feedbacks
                loadFeedbacks();
                
                // Afficher un message de succès
                showSuccess('Feedback créé avec succès !');
            } else {
                showError(data.error || 'Erreur lors de la création du feedback');
            }
        } else {
            showError('Erreur lors de la création du feedback');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showError('Erreur lors de la création du feedback');
    }
}

// Toggle like
async function toggleLike(feedbackId) {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch(`/api/sponsor/feedback/${feedbackId}/like`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                // Mettre à jour le bouton de like
                const button = event.target.closest('button');
                const icon = button.querySelector('i');
                const count = button.textContent.trim();
                
                if (data.action === 'liked') {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    button.textContent = ` ${parseInt(count) + 1}`;
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    button.textContent = ` ${parseInt(count) - 1}`;
                }
            }
        }
    } catch (error) {
        console.error('Erreur lors du like:', error);
    }
}

// Charger les types de feedback
async function loadFeedbackTypes() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/feedback/types', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                const select = document.getElementById('feedbackType');
                select.innerHTML = '<option value="">Sélectionner un type...</option>';
                
                data.types.forEach(type => {
                    select.innerHTML += `
                        <option value="${type.value}">${type.label}</option>
                    `;
                });
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des types:', error);
    }
}

// Charger les événements
async function loadEvents() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/feedback/events', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                const select = document.getElementById('eventId');
                select.innerHTML = '<option value="">Sélectionner un événement...</option>';
                
                data.events.forEach(event => {
                    const date = new Date(event.start_date).toLocaleDateString('fr-FR');
                    select.innerHTML += `
                        <option value="${event.id}">${event.title} (${date})</option>
                    `;
                });
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des événements:', error);
    }
}

// Charger les sponsorships de l'utilisateur
async function loadUserSponsorships() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/feedback/sponsorships', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            if (data.success) {
                const select = document.getElementById('sponsorshipTempId');
                select.innerHTML = '<option value="">Aucun sponsorship spécifique</option>';
                
                data.sponsorships.forEach(sponsorship => {
                    const eventTitle = sponsorship.event ? sponsorship.event.title : 'Événement supprimé';
                    const packageName = sponsorship.package ? sponsorship.package.name : 'Package supprimé';
                    select.innerHTML += `
                        <option value="${sponsorship.id}">${eventTitle} - ${packageName}</option>
                    `;
                });
            }
        }
    } catch (error) {
        console.error('Erreur lors du chargement des sponsorships:', error);
    }
}

// Fonctions utilitaires
function getTypeIcon(type) {
    const icons = {
        'pre_event': 'fas fa-calendar-check',
        'post_event': 'fas fa-calendar-times',
        'package_feedback': 'fas fa-box',
        'organizer_feedback': 'fas fa-user-tie',
        'general_comment': 'fas fa-comment',
        'improvement_suggestion': 'fas fa-lightbulb',
        'experience_sharing': 'fas fa-share-alt'
    };
    return icons[type] || 'fas fa-comment';
}

function getTypeLabel(type) {
    const labels = {
        'pre_event': 'Avant l\'événement',
        'post_event': 'Après l\'événement',
        'package_feedback': 'Feedback package',
        'organizer_feedback': 'Feedback organisateur',
        'general_comment': 'Commentaire général',
        'improvement_suggestion': 'Suggestion',
        'experience_sharing': 'Partage d\'expérience'
    };
    return labels[type] || 'Commentaire';
}

function generateStars(rating) {
    if (!rating) return '';
    
    let stars = '';
    for (let i = 1; i <= 5; i++) {
        if (i <= rating) {
            stars += '<i class="fas fa-star text-warning"></i>';
        } else {
            stars += '<i class="far fa-star text-muted"></i>';
        }
    }
    return stars;
}

function updateStats(stats) {
    if (stats) {
        document.getElementById('totalFeedbacks').textContent = stats.total_feedbacks || 0;
        document.getElementById('averageRating').textContent = (stats.average_rating || 0).toFixed(1);
        // Autres statistiques...
    }
}

function showSuccess(message) {
    // Implémenter l'affichage des messages de succès
    alert(message);
}

function showError(message) {
    // Implémenter l'affichage des messages d'erreur
    alert('Erreur: ' + message);
}
</script>

<style>
.rating-input {
    display: flex;
    flex-direction: row-reverse;
    justify-content: flex-end;
}

.rating-input input {
    display: none;
}

.rating-input label {
    font-size: 1.5rem;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-input label:hover,
.rating-input label:hover ~ label,
.rating-input input:checked ~ label {
    color: #ffc107;
}

.feedback-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.feedback-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>
@endpush

@endsection
