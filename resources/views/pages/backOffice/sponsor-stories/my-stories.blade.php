@extends('layouts.sponsor')

@section('title', 'Mes Stories - Echofy Sponsor')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-camera text-primary"></i> Mes Stories
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sponsor.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Mes Stories</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('sponsor.stories.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus"></i> Créer une Story
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Stories
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_stories'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-camera fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Stories Actives
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_stories'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Vues
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_views']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Total Likes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_likes']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-heart fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Instagram-like Stories Grid -->
    <div class="row">
        @forelse($stories as $story)
        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
            <div class="instagram-story-card" onclick="viewStory({{ $story->id }})">
                <!-- Story Circle -->
                <div class="story-circle {{ $story->is_expired ? 'expired' : 'active' }}">
                    @if($story->media_type === 'image' && $story->media_url)
                        <img src="{{ $story->media_url }}" alt="Story" class="story-image">
                    @elseif($story->media_type === 'video' && $story->media_url)
                        <video class="story-image" muted>
                            <source src="{{ $story->media_url }}" type="video/mp4">
                        </video>
                    @else
                        <div class="story-text-preview" style="background: {{ $story->background_color }}; color: {{ $story->text_color }};">
                            <div class="story-text-content">
                                @if($story->title)
                                    <h6>{{ Str::limit($story->title, 20) }}</h6>
                                @endif
                                <p>{{ Str::limit($story->content, 30) }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Story Status Indicator -->
                    <div class="story-status">
                        @if($story->is_featured)
                            <i class="fas fa-star featured-star"></i>
                        @endif
                        @if($story->is_expired)
                            <i class="fas fa-clock expired-icon"></i>
                        @else
                            <i class="fas fa-play active-icon"></i>
                        @endif
                    </div>
                    
                    <!-- Progress Ring -->
                    <div class="progress-ring">
                        <svg class="progress-ring-svg" width="120" height="120">
                            <circle class="progress-ring-circle" 
                                    stroke="#e3e6f0" 
                                    stroke-width="3" 
                                    fill="transparent" 
                                    r="57" 
                                    cx="60" 
                                    cy="60"/>
                            <circle class="progress-ring-circle progress-ring-fill" 
                                    stroke="#28a745" 
                                    stroke-width="3" 
                                    fill="transparent" 
                                    r="57" 
                                    cx="60" 
                                    cy="60"
                                    style="stroke-dasharray: 358; stroke-dashoffset: {{ 358 - (358 * $story->progress_percentage / 100) }};"/>
                        </svg>
                    </div>
                </div>
                
                <!-- Story Info -->
                <div class="story-info">
                    <h6 class="story-title">{{ Str::limit($story->title ?: 'Story', 15) }}</h6>
                    <div class="story-stats">
                        <span class="stat-item">
                            <i class="fas fa-eye"></i> {{ $story->views_count }}
                        </span>
                        <span class="stat-item">
                            <i class="fas fa-heart"></i> {{ $story->likes_count }}
                        </span>
                    </div>
                    <div class="story-time">
                        {{ $story->time_remaining }}
                    </div>
                </div>
                
                <!-- Story Actions -->
                <div class="story-actions">
                    @if(!$story->is_expired)
                        <button class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); editStory({{ $story->id }})">
                            <i class="fas fa-edit"></i>
                        </button>
                        @if($story->is_featured)
                            <button class="btn btn-sm btn-warning" onclick="event.stopPropagation(); toggleFeatured({{ $story->id }}, false)">
                                <i class="fas fa-star"></i>
                            </button>
                        @else
                            <button class="btn btn-sm btn-outline-warning" onclick="event.stopPropagation(); toggleFeatured({{ $story->id }}, true)">
                                <i class="far fa-star"></i>
                            </button>
                        @endif
                        <button class="btn btn-sm btn-outline-danger" onclick="event.stopPropagation(); deleteStory({{ $story->id }})">
                            <i class="fas fa-trash"></i>
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-stories text-center py-5">
                <div class="empty-stories-icon">
                    <i class="fas fa-camera fa-4x text-muted"></i>
                </div>
                <h4 class="text-muted mt-3">Aucune story créée</h4>
                <p class="text-muted">Commencez par créer votre première story pour promouvoir vos événements sponsorisés !</p>
                <a href="{{ route('sponsor.stories.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Créer ma première Story
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($stories->hasPages())
        <div class="row mt-4">
            <div class="col-12">
                {{ $stories->links() }}
            </div>
        </div>
    @endif
</div>

<!-- Story Viewer Modal (Instagram-like) -->
<div class="modal fade" id="storyViewerModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg story-viewer-modal" role="document">
        <div class="modal-content story-viewer-content">
            <div class="story-viewer-header">
                <div class="story-viewer-info">
                    <div class="story-viewer-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="story-viewer-details">
                        <h6 id="viewer-story-title">Story Title</h6>
                        <small id="viewer-story-time">2h</small>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            
            <div class="story-viewer-body">
                <div id="story-viewer-content" class="story-viewer-media">
                    <!-- Story content will be loaded here -->
                </div>
                
                <div class="story-viewer-actions">
                    <button class="btn btn-danger btn-lg" onclick="likeCurrentStory()">
                        <i class="fas fa-heart"></i> Like
                    </button>
                </div>
            </div>
            
            <div class="story-viewer-footer">
                <div class="story-viewer-stats">
                    <span id="viewer-story-views">0 vues</span>
                    <span id="viewer-story-likes">0 likes</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Confirmation Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="confirmMessage"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" id="confirmAction">Confirmer</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let currentStoryId = null;

// Fonction pour voir une story (Instagram-like)
function viewStory(storyId) {
    currentStoryId = storyId;
    
    // Charger les données de la story
    fetch(`/sponsor/stories/${storyId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayStoryInViewer(data.story);
                $('#storyViewerModal').modal('show');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            showAlert('danger', 'Erreur lors du chargement de la story');
        });
}

// Fonction pour afficher la story dans le viewer
function displayStoryInViewer(story) {
    document.getElementById('viewer-story-title').textContent = story.title || 'Story';
    document.getElementById('viewer-story-time').textContent = story.time_remaining;
    document.getElementById('viewer-story-views').textContent = story.views_count + ' vues';
    document.getElementById('viewer-story-likes').textContent = story.likes_count + ' likes';
    
    const viewerContent = document.getElementById('story-viewer-content');
    
    if (story.media_type === 'image' && story.media_url) {
        viewerContent.innerHTML = `<img src="${story.media_url}" class="story-viewer-image" alt="Story">`;
    } else if (story.media_type === 'video' && story.media_url) {
        viewerContent.innerHTML = `
            <video class="story-viewer-video" controls autoplay>
                <source src="${story.media_url}" type="video/mp4">
            </video>
        `;
    } else {
        viewerContent.innerHTML = `
            <div class="story-viewer-text" style="background: ${story.background_color}; color: ${story.text_color};">
                <div class="story-viewer-text-content">
                    ${story.title ? `<h2>${story.title}</h2>` : ''}
                    <p>${story.content}</p>
                </div>
            </div>
        `;
    }
}

// Fonction pour liker la story actuelle
function likeCurrentStory() {
    if (!currentStoryId) return;
    
    fetch(`/sponsor/stories/${currentStoryId}/like`, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('viewer-story-likes').textContent = data.data.likes_count + ' likes';
            showAlert('success', data.message);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors du like');
    });
}

// Fonction pour éditer une story
function editStory(storyId) {
    window.location.href = `/sponsor/stories/${storyId}/edit`;
}

// Fonction pour basculer le statut vedette
function toggleFeatured(storyId, makeFeatured) {
    const message = makeFeatured 
        ? 'Êtes-vous sûr de vouloir mettre cette story en vedette ?'
        : 'Êtes-vous sûr de vouloir retirer cette story de la vedette ?';
    
    showConfirmModal(message, () => {
        performToggleFeatured(storyId, makeFeatured);
    });
}

// Fonction pour supprimer une story
function deleteStory(storyId) {
    showConfirmModal('Êtes-vous sûr de vouloir supprimer cette story ? Cette action est irréversible.', () => {
        performDeleteStory(storyId);
    });
}

// Fonction pour afficher la modal de confirmation
function showConfirmModal(message, callback) {
    document.getElementById('confirmMessage').textContent = message;
    document.getElementById('confirmAction').onclick = callback;
    $('#confirmModal').modal('show');
}

// Fonction pour effectuer le toggle vedette
async function performToggleFeatured(storyId, makeFeatured) {
    try {
        const url = makeFeatured 
            ? `/sponsor/stories/${storyId}/feature`
            : `/sponsor/stories/${storyId}/unfeature`;
        
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });

        const data = await response.json();
        
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message || 'Erreur lors de l\'opération');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de l\'opération');
    }
}

// Fonction pour supprimer une story
async function performDeleteStory(storyId) {
    try {
        const response = await fetch(`/sponsor/stories/${storyId}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });

        const data = await response.json();
        
        if (data.success) {
            showAlert('success', data.message);
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert('danger', data.message || 'Erreur lors de la suppression');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de la suppression');
    }
}

// Fonction pour afficher les alertes
function showAlert(type, message) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<style>
/* Instagram-like Stories Styles */
.instagram-story-card {
    cursor: pointer;
    transition: transform 0.2s ease-in-out;
    position: relative;
}

.instagram-story-card:hover {
    transform: scale(1.05);
}

.story-circle {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    margin: 0 auto 15px;
    position: relative;
    overflow: hidden;
    border: 3px solid #e3e6f0;
    transition: border-color 0.3s ease;
}

.story-circle.active {
    border-color: #28a745;
}

.story-circle.expired {
    border-color: #6c757d;
    opacity: 0.7;
}

.story-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%;
}

.story-text-preview {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 10px;
}

.story-text-content h6 {
    font-size: 0.8rem;
    margin-bottom: 5px;
    font-weight: bold;
}

.story-text-content p {
    font-size: 0.7rem;
    margin: 0;
    line-height: 1.2;
}

.story-status {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255, 255, 255, 0.9);
    border-radius: 50%;
    width: 25px;
    height: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
}

.featured-star {
    color: #ffc107;
}

.active-icon {
    color: #28a745;
}

.expired-icon {
    color: #6c757d;
}

.progress-ring {
    position: absolute;
    top: -3px;
    left: -3px;
    width: 126px;
    height: 126px;
}

.progress-ring-svg {
    transform: rotate(-90deg);
}

.progress-ring-circle {
    transition: stroke-dashoffset 0.3s ease;
}

.story-info {
    text-align: center;
    margin-bottom: 10px;
}

.story-title {
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: #333;
}

.story-stats {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 5px;
}

.stat-item {
    font-size: 0.8rem;
    color: #666;
}

.stat-item i {
    margin-right: 3px;
}

.story-time {
    font-size: 0.75rem;
    color: #999;
}

.story-actions {
    display: flex;
    justify-content: center;
    gap: 5px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.instagram-story-card:hover .story-actions {
    opacity: 1;
}

.story-actions .btn {
    padding: 5px 8px;
    font-size: 0.8rem;
}

/* Story Viewer Modal (Instagram-like) */
.story-viewer-modal {
    max-width: 500px;
}

.story-viewer-content {
    background: #000;
    color: #fff;
    border-radius: 15px;
    overflow: hidden;
}

.story-viewer-header {
    padding: 15px 20px;
    border-bottom: 1px solid #333;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.story-viewer-info {
    display: flex;
    align-items: center;
}

.story-viewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #333;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-right: 10px;
}

.story-viewer-details h6 {
    margin: 0;
    font-size: 0.9rem;
}

.story-viewer-details small {
    color: #999;
    font-size: 0.8rem;
}

.story-viewer-body {
    position: relative;
    height: 400px;
}

.story-viewer-media {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.story-viewer-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.story-viewer-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.story-viewer-text {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding: 20px;
}

.story-viewer-text-content h2 {
    font-size: 1.5rem;
    margin-bottom: 15px;
}

.story-viewer-text-content p {
    font-size: 1.1rem;
    line-height: 1.4;
}

.story-viewer-actions {
    position: absolute;
    bottom: 20px;
    left: 50%;
    transform: translateX(-50%);
}

.story-viewer-footer {
    padding: 15px 20px;
    border-top: 1px solid #333;
}

.story-viewer-stats {
    display: flex;
    justify-content: space-between;
    font-size: 0.9rem;
    color: #999;
}

/* Empty State */
.empty-stories {
    background: #f8f9fa;
    border-radius: 15px;
    border: 2px dashed #dee2e6;
}

.empty-stories-icon {
    margin-bottom: 20px;
}

/* Responsive */
@media (max-width: 768px) {
    .story-circle {
        width: 100px;
        height: 100px;
    }
    
    .progress-ring {
        width: 106px;
        height: 106px;
    }
    
    .story-viewer-modal {
        max-width: 95%;
    }
    
    .story-viewer-body {
        height: 300px;
    }
}
</style>
@endsection