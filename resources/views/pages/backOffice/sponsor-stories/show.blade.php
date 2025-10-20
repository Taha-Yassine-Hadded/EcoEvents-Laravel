@extends('layouts.sponsor')

@section('title', 'Story - ' . ($story->title ?: 'Sans titre') . ' - Echofy Sponsor')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-camera text-primary"></i> {{ $story->title ?: 'Story' }}
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sponsor.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sponsor.stories.my-stories') }}">Mes Stories</a></li>
                            <li class="breadcrumb-item active">{{ $story->title ?: 'Story' }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('sponsor.stories.my-stories') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour aux Stories
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Story principale -->
        <div class="col-lg-8">
            <div class="card story-display-card shadow">
                <!-- Story Media -->
                <div class="story-media-display" style="background: linear-gradient(135deg, {{ $story->background_color }}, {{ $story->background_color }}dd);">
                    @if($story->media_type === 'image' && $story->media_url)
                        <img src="{{ $story->media_url }}" class="story-display-image" alt="Story Image">
                    @elseif($story->media_type === 'video' && $story->media_url)
                        <video class="story-display-video" controls>
                            <source src="{{ $story->media_url }}" type="video/mp4">
                            Votre navigateur ne supporte pas la lecture vidéo.
                        </video>
                    @else
                        <div class="story-text-display d-flex align-items-center justify-content-center h-100">
                            <div class="text-center text-white p-4">
                                @if($story->title)
                                    <h2 class="mb-4" style="color: {{ $story->text_color }}; font-size: {{ $story->font_size === 'large' ? '2rem' : ($story->font_size === 'small' ? '1.5rem' : '1.75rem') }};">{{ $story->title }}</h2>
                                @endif
                                <p class="mb-0" style="color: {{ $story->text_color }}; font-size: {{ $story->font_size === 'large' ? '1.25rem' : ($story->font_size === 'small' ? '1rem' : '1.1rem') }};">{{ $story->content }}</p>
                            </div>
                        </div>
                    @endif
                    
                    <!-- Story Status Badges -->
                    <div class="story-display-badges">
                        @if($story->is_featured)
                            <span class="badge badge-warning badge-lg">
                                <i class="fas fa-star"></i> En Vedette
                            </span>
                        @endif
                        @if($story->is_expired)
                            <span class="badge badge-secondary badge-lg">
                                <i class="fas fa-clock"></i> Expirée
                            </span>
                        @else
                            <span class="badge badge-success badge-lg">
                                <i class="fas fa-eye"></i> Active
                            </span>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    <div class="story-display-progress">
                        <div class="progress" style="height: 4px;">
                            <div class="progress-bar bg-white" role="progressbar" 
                                 style="width: {{ $story->progress_percentage }}%" 
                                 aria-valuenow="{{ $story->progress_percentage }}" 
                                 aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>
                </div>

                <!-- Story Actions -->
                <div class="card-body">
                    <div class="row align-items-center mb-3">
                        <div class="col-md-6">
                            <h5 class="mb-0">
                                @if($story->event)
                                    <i class="fas fa-calendar text-primary"></i> {{ $story->event->title }}
                                @else
                                    <i class="fas fa-camera text-primary"></i> Story Générale
                                @endif
                            </h5>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ $story->time_remaining }}
                            </small>
                        </div>
                    </div>

                    <!-- Story Stats -->
                    <div class="row text-center mb-4">
                        <div class="col-4">
                            <div class="stat-item">
                                <i class="fas fa-eye fa-2x text-primary mb-2"></i>
                                <h4 class="mb-0">{{ $story->views_count }}</h4>
                                <small class="text-muted">Vues</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <i class="fas fa-heart fa-2x text-danger mb-2"></i>
                                <h4 class="mb-0">{{ $story->likes_count }}</h4>
                                <small class="text-muted">Likes</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="stat-item">
                                <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                                <h4 class="mb-0">{{ $story->getStats()['engagement_rate'] }}%</h4>
                                <small class="text-muted">Engagement</small>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center">
                        @if(!$story->is_expired)
                            <button class="btn btn-danger btn-lg mr-3" onclick="likeStory({{ $story->id }})">
                                <i class="fas fa-heart"></i> Like
                            </button>
                        @endif
                        
                        @if($story->sponsor_id === auth()->id())
                            <div class="btn-group">
                                @if(!$story->is_expired)
                                    <a href="{{ route('sponsor.stories.edit', $story->id) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i> Modifier
                                    </a>
                                @endif
                                
                                @if($story->is_featured)
                                    <button class="btn btn-warning" onclick="toggleFeatured({{ $story->id }}, false)">
                                        <i class="fas fa-star"></i> Retirer de la vedette
                                    </button>
                                @else
                                    <button class="btn btn-outline-warning" onclick="toggleFeatured({{ $story->id }}, true)">
                                        <i class="far fa-star"></i> Mettre en vedette
                                    </button>
                                @endif
                                
                                <button class="btn btn-outline-danger" onclick="deleteStory({{ $story->id }})">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Story Footer -->
                <div class="card-footer bg-light">
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="fas fa-user"></i> Par {{ $story->sponsor->name }}
                                @if($story->sponsor->company_name)
                                    ({{ $story->sponsor->company_name }})
                                @endif
                            </small>
                        </div>
                        <div class="col-md-6 text-right">
                            <small class="text-muted">
                                <i class="fas fa-calendar"></i> Créée {{ $story->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations et statistiques -->
        <div class="col-lg-4">
            <!-- Informations de la story -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-info-circle"></i> Informations
                    </h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Type:</strong></td>
                            <td>
                                @if($story->media_type === 'image')
                                    <i class="fas fa-image text-primary"></i> Image
                                @elseif($story->media_type === 'video')
                                    <i class="fas fa-video text-primary"></i> Vidéo
                                @else
                                    <i class="fas fa-font text-primary"></i> Texte
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Statut:</strong></td>
                            <td>
                                @if($story->is_expired)
                                    <span class="badge badge-secondary">Expirée</span>
                                @else
                                    <span class="badge badge-success">Active</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>En vedette:</strong></td>
                            <td>
                                @if($story->is_featured)
                                    <span class="badge badge-warning">Oui</span>
                                @else
                                    <span class="badge badge-light">Non</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Créée le:</strong></td>
                            <td>{{ $story->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td><strong>Expire le:</strong></td>
                            <td>{{ $story->expires_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @if($story->event)
                        <tr>
                            <td><strong>Événement:</strong></td>
                            <td>{{ $story->event->title }}</td>
                        </tr>
                        @endif
                    </table>
                </div>
            </div>

            <!-- Actions rapides -->
            @if($story->sponsor_id === auth()->id())
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bolt"></i> Actions Rapides
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('sponsor.stories.my-stories') }}" class="btn btn-primary btn-block mb-2">
                        <i class="fas fa-list"></i> Voir toutes mes Stories
                    </a>
                    
                    @if(!$story->is_expired)
                        <a href="{{ route('sponsor.stories.edit', $story->id) }}" class="btn btn-outline-primary btn-block mb-2">
                            <i class="fas fa-edit"></i> Modifier cette Story
                        </a>
                    @endif
                    
                    <a href="{{ route('sponsor.stories.create') }}" class="btn btn-success btn-block">
                        <i class="fas fa-plus"></i> Créer une nouvelle Story
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Fonction pour liker une story
async function likeStory(storyId) {
    try {
        const response = await fetch(`/sponsor/stories/${storyId}/like`, {
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
            // Mettre à jour le compteur de likes
            const likesElement = document.querySelector('.stat-item .text-danger').nextElementSibling.nextElementSibling;
            if (likesElement) {
                likesElement.textContent = data.data.likes_count;
            }
        } else {
            showAlert('danger', data.message || 'Erreur lors du like');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors du like');
    }
}

// Fonction pour basculer le statut vedette
function toggleFeatured(storyId, makeFeatured) {
    const message = makeFeatured 
        ? 'Êtes-vous sûr de vouloir mettre cette story en vedette ?'
        : 'Êtes-vous sûr de vouloir retirer cette story de la vedette ?';
    
    if (confirm(message)) {
        performToggleFeatured(storyId, makeFeatured);
    }
}

// Fonction pour supprimer une story
function deleteStory(storyId) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette story ? Cette action est irréversible.')) {
        performDeleteStory(storyId);
    }
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
            setTimeout(() => {
                window.location.href = '{{ route("sponsor.stories.my-stories") }}';
            }, 1500);
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
.story-display-card {
    border-radius: 20px;
    overflow: hidden;
}

.story-media-display {
    height: 500px;
    position: relative;
    overflow: hidden;
}

.story-display-image, .story-display-video {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.story-text-display {
    height: 500px;
    background: linear-gradient(135deg, #3498db, #2980b9);
}

.story-display-badges {
    position: absolute;
    top: 20px;
    right: 20px;
    z-index: 10;
}

.story-display-progress {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    z-index: 10;
}

.badge-lg {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
}

.stat-item {
    padding: 1rem;
    border-radius: 10px;
    background: #f8f9fc;
    transition: transform 0.2s ease-in-out;
}

.stat-item:hover {
    transform: translateY(-2px);
}

.progress {
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

@media (max-width: 768px) {
    .story-media-display {
        height: 300px;
    }
    
    .story-text-display {
        height: 300px;
    }
    
    .stat-item {
        padding: 0.5rem;
    }
}
</style>
@endsection