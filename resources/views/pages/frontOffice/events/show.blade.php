@extends('layouts.app')

@section('title', 'Echofy - Event Details')

@section('content')
    <!-- Breadcrumb Area -->
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Détails de l'Événement</h4>
                        </div>
                        <ul>
                            <li><a href="{{ url('/') }}"><img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy</a></li>
                            <li><a href="{{ route('front.events.index') }}">Événements</a></li>
                            <li>{{ Str::limit($event->title, 30) }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Area -->
    <div class="project-details-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-10 col-xl-9 mx-auto">
                    <div class="project-details-thumb">
                        <div class="event-image-container">
                            @if ($event->img && Storage::disk('public')->exists($event->img))
                                <img src="{{ asset('storage/' . $event->img) }}" alt="{{ $event->title }}">
                            @else
                                <img src="{{ asset('storage/events/default-event.jpg') }}" alt="{{ $event->title }}">
                            @endif
                            
                            <!-- Status Badge Overlay -->
                            <div class="event-status-overlay status-{{ $event->status }}">
                                @switch($event->status)
                                    @case('upcoming') À venir @break
                                    @case('ongoing') En cours @break
                                    @case('completed') Terminé @break
                                    @default Annulé
                                @endswitch
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="project-details-content">
                                <h4>{{ $event->title }}</h4>
                                <div class="event-basic-info mb-4">
                                    <div class="event-info-item">
                                        <i class="fas fa-calendar-alt"></i>
                                        <span><strong>Date:</strong> {{ $event->date ? $event->date->format('d M Y à H:i') : 'Date non définie' }}</span>
                                    </div>
                                    <div class="event-info-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><strong>Lieu:</strong> {{ $event->location }}</span>
                                    </div>
                                    @if ($event->capacity)
                                        <div class="event-info-item">
                                            <i class="fas fa-users"></i>
                                            <span><strong>Capacité:</strong> {{ $event->capacity }} personnes</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <h3>Description</h3>
                                <p class="project-details-desc">{{ $event->description ?? 'Aucune description disponible pour cet événement.' }}</p>
                                
                                @if ($event->organizer)
                                    <h3>Organisateur</h3>
                                    <div class="organizer-info">
                                        <div class="organizer-avatar">
                                            <span>{{ strtoupper(substr($event->organizer->name, 0, 1)) }}</span>
                                        </div>
                                        <div class="organizer-details">
                                            <h5>{{ $event->organizer->name }}</h5>
                                            @if ($event->organizer->email)
                                                <p><i class="fas fa-envelope"></i> {{ $event->organizer->email }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Event Actions - Initially Hidden -->
                                <div class="event-actions mt-4 d-flex flex-wrap align-items-center gap-3">
                                    <!-- Back Button - Always Visible -->
                                    <a href="{{ route('front.events.index') }}" class="btn btn-outline-secondary">
                                        <i class="fas fa-arrow-left"></i> Retour aux événements
                                    </a>
                                    
                                    <!-- Guest Actions - Show login button -->
                                    <div id="guest-actions" style="display: none;">
                                        @if ($event->status === 'upcoming')
                                            <a href="{{ route('login') }}" class="btn btn-success">
                                                <i class="fas fa-sign-in-alt"></i> Se connecter pour s'inscrire
                                            </a>
                                        @endif
                                    </div>
                                    
                                    <!-- Authenticated User Actions -->
                                    <div id="authenticated-actions" style="display: none;">
                                        <!-- Organizer Actions (Event Owner) -->
                                        <div id="organizer-actions" style="display: none;">
                                            <button class="btn btn-warning me-3" onclick="openEditModal()">
                                                <i class="fas fa-edit"></i> Modifier l'événement
                                            </button>
                                            <button class="btn btn-danger" onclick="deleteEvent({{ $event->id }})">
                                                <i class="fas fa-trash"></i> Supprimer
                                            </button>
                                        </div>
                                        
                                        <!-- Regular User Actions (Subscribe) -->
                                        <div id="regular-actions" style="display: none;">
                                            @if ($event->status === 'upcoming')
                                                <button class="btn btn-success" onclick="subscribeToEvent({{ $event->id }})">
                                                    <i class="fas fa-calendar-check"></i> S'inscrire
                                                </button>
                                            @endif
                                        </div>
                                        
                                        <!-- Admin Actions (Dashboard Management) -->
                                        <div id="admin-actions" style="display: none;">
                                            <a href="/admin/events" class="btn btn-primary">
                                                <i class="fas fa-cogs"></i> Gérer dans le tableau de bord
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="project-details-right">
                                <h5 class="sidebar-title">Informations sur l'Événement</h5>
                                
                                <div class="project-details-info">
                                    <p>Organisateur:</p>
                                    <h6>{{ $event->organizer->name ?? 'Non spécifié' }}</h6>
                                </div>
                                <div class="project-details-info">
                                    <p>Catégorie:</p>
                                    <h6>
                                        <i class="fas fa-leaf text-success"></i>
                                        {{ $event->category->name ?? 'Non catégorisé' }}
                                    </h6>
                                </div>
                                <div class="project-details-info">
                                    <p>Date & Heure:</p>
                                    <h6>{{ $event->date ? $event->date->format('d M Y à H:i') : 'Date non définie' }}</h6>
                                </div>
                                <div class="project-details-info">
                                    <p>Lieu:</p>
                                    <h6>{{ $event->location }}</h6>
                                </div>
                                @if ($event->capacity)
                                    <div class="project-details-info">
                                        <p>Capacité:</p>
                                        <h6>{{ $event->capacity }} personnes</h6>
                                    </div>
                                @endif
                                <div class="project-details-info">
                                    <p>Statut:</p>
                                    <h6>
                                        <span class="event-status-badge status-{{ $event->status }}">
                                            @switch($event->status)
                                                @case('upcoming') À venir @break
                                                @case('ongoing') En cours @break
                                                @case('completed') Terminé @break
                                                @default Annulé
                                            @endswitch
                                        </span>
                                    </h6>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Similar Events Area -->
    @if ($similarEvents->count() > 0)
        <div class="project-area inner">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 text-center">
                        <div class="section-title center">
                            <h4><img src="{{ Vite::asset('resources/assets/images/home1/section-shape.png') }}" alt="">Événements Similaires</h4>
                            <h1>Autres événements de la catégorie "{{ $event->category->name ?? 'Cette catégorie' }}"</h1>
                        </div>
                    </div>
                </div>
                <div class="row">
                    @foreach ($similarEvents->take(3) as $similarEvent)
                        <div class="col-lg-4 col-md-6">
                            <div class="single-project-box">
                                <div class="project-thumb">
                                    @if ($similarEvent->img && Storage::disk('public')->exists($similarEvent->img))
                                        <img src="{{ asset('storage/' . $similarEvent->img) }}" alt="{{ $similarEvent->title }}">
                                    @else
                                        <img src="{{ asset('storage/events/default-event.jpg') }}" alt="{{ $similarEvent->title }}">
                                    @endif
                                    
                                    <div class="event-status-badge status-{{ $similarEvent->status }}">
                                        @switch($similarEvent->status)
                                            @case('upcoming') À venir @break
                                            @case('ongoing') En cours @break
                                            @case('completed') Terminé @break
                                            @default Annulé
                                        @endswitch
                                    </div>
                                </div>
                                <div class="project-content">
                                    <h4>{{ $similarEvent->category->name ?? 'Non catégorisé' }}</h4>
                                    <a href="{{ route('front.events.show', $similarEvent->id) }}">{{ Str::limit($similarEvent->title, 40) }}</a>
                                    <a class="project-button" href="{{ route('front.events.show', $similarEvent->id) }}">Voir Détails<i class="bi bi-arrow-right-short"></i></a>
                                    <div class="project-shape">
                                        <img src="{{ Vite::asset('resources/assets/images/home1/project-shape.png') }}" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if ($similarEvents->count() > 3)
                    <div class="row mt-4">
                        <div class="col-12 text-center">
                            <a href="{{ route('front.events.index', ['category' => $event->category->name]) }}" class="btn btn-outline-success">
                                Voir tous les événements de cette catégorie
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Edit Event Modal -->
    <div class="modal fade" id="editEventModal" tabindex="-1" aria-labelledby="editEventModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEventModalLabel">
                        <i class="fas fa-edit"></i> Modifier l'événement
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEventForm" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_title" class="form-label">
                                        <i class="fas fa-heading"></i> Titre de l'événement *
                                    </label>
                                    <input type="text" class="form-control" id="edit_title" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_category_id" class="form-label">
                                        <i class="fas fa-tag"></i> Catégorie *
                                    </label>
                                    <select class="form-select" id="edit_category_id" name="category_id" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        <!-- Categories will be loaded via JavaScript -->
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_date" class="form-label">
                                        <i class="fas fa-calendar-alt"></i> Date et heure *
                                    </label>
                                    <input type="datetime-local" class="form-control" id="edit_date" name="date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_location" class="form-label">
                                        <i class="fas fa-map-marker-alt"></i> Lieu *
                                    </label>
                                    <input type="text" class="form-control" id="edit_location" name="location" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_capacity" class="form-label">
                                        <i class="fas fa-users"></i> Capacité
                                    </label>
                                    <input type="number" class="form-control" id="edit_capacity" name="capacity" min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit_status" class="form-label">
                                        <i class="fas fa-info-circle"></i> Statut *
                                    </label>
                                    <select class="form-select" id="edit_status" name="status" required>
                                        <option value="upcoming">À venir</option>
                                        <option value="ongoing">En cours</option>
                                        <option value="completed">Terminé</option>
                                        <option value="cancelled">Annulé</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit_description" class="form-label">
                                <i class="fas fa-align-left"></i> Description
                            </label>
                            <textarea class="form-control" id="edit_description" name="description" rows="4"></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="edit_image" class="form-label">
                                <i class="fas fa-image"></i> Image de l'événement
                            </label>
                            <input type="file" class="form-control" id="edit_image" name="img" accept="image/*">
                            <div class="form-text">Formats acceptés: JPG, PNG, GIF (Max: 2MB)</div>
                            
                            <!-- Current Image Preview -->
                            <div id="current-image-preview" class="mt-2">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="button" class="btn btn-success" onclick="saveEventChanges()">
                        <i class="fas fa-save"></i> Enregistrer les modifications
                    </button>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
    /* Event Image Styling */
    .event-image-container {
        position: relative;
        height: 500px;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }
    
    .event-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .default-event-image {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        font-size: 4rem;
    }
    
    .event-status-overlay {
        position: absolute;
        top: 20px;
        right: 20px;
        padding: 8px 16px;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 600;
        color: white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
        backdrop-filter: blur(10px);
    }
    
    /* Status Colors */
    .status-upcoming { background: rgba(255, 193, 7, 0.9); }
    .status-ongoing { background: rgba(40, 167, 69, 0.9); }
    .status-completed { background: rgba(108, 117, 125, 0.9); }
    .status-cancelled { background: rgba(220, 53, 69, 0.9); }
    
    /* Event Info Styling */
    .event-basic-info {
        background: #f8f9fa;
        padding: 25px;
        border-radius: 15px;
        border-left: 5px solid #28a745;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .event-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 15px;
        font-size: 1rem;
    }
    
    .event-info-item i {
        color: #28a745;
        width: 25px;
        margin-right: 15px;
        font-size: 1.1rem;
    }
    
    /* Organizer Styling */
    .organizer-info {
        display: flex;
        align-items: center;
        padding: 20px;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .organizer-avatar {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        font-weight: 600;
        margin-right: 20px;
        box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    }
    
    .organizer-details h5 {
        margin-bottom: 8px;
        color: #333;
        font-weight: 600;
    }
    
    .organizer-details p {
        margin: 0;
        color: #666;
        font-size: 0.95rem;
    }
    
    /* Sidebar Styling */
    .sidebar-title {
        color: #28a745;
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 3px solid #28a745;
        font-weight: 600;
    }
    
    .project-details-info {
        margin-bottom: 10px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    
    .project-details-info p {
        color: #666;
        font-size: 0.9rem;
        margin-bottom: 5px;
        font-weight: 500;
    }
    
    .project-details-info h6 {
        color: #333;
        font-weight: 600;
        margin: 0;
    }
    
    /* Event Actions Styling */
    .event-actions {
        padding: 25px 0;
        border-top: 2px solid #eee;
        gap: 15px;
    }
    
    .event-actions .btn {
        border-radius: 30px;
        font-weight: 600;
        padding: 12px 24px;
        transition: all 0.3s ease;
        white-space: nowrap;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .event-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
    }
    
    .event-actions .btn-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        border: none;
        color: #212529;
    }
    
    .event-actions .btn-warning:hover {
        background: linear-gradient(135deg, #e0a800, #d39e00);
        box-shadow: 0 8px 25px rgba(255, 193, 7, 0.4);
    }
    
    .event-actions .btn-warning {
        background: linear-gradient(135deg, #ffc107, #e0a800);
        border: none;
        color: #212529;
    }
    
    .event-actions .btn-danger {
        background: linear-gradient(135deg, #dc3545, #c82333);
        border: none;
        color: white;
    }
    
    .event-actions .btn-danger:hover {
        background: linear-gradient(135deg, #c82333, #bd2130);
        box-shadow: 0 8px 25px rgba(220, 53, 69, 0.4);
    }
    
    .event-actions .btn-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        border: none;
        color: white;
    }
    
    .event-actions .btn-success:hover {
        background: linear-gradient(135deg, #20c997, #1e7e34);
        box-shadow: 0 8px 25px rgba(40, 167, 69, 0.4);
    }
    
    .event-actions .btn-outline-secondary {
        border: 2px solid #6c757d;
        color: #6c757d;
        background: transparent;
    }
    
    .event-actions .btn-outline-secondary:hover {
        background: #6c757d;
        color: white;
        box-shadow: 0 8px 25px rgba(108, 117, 125, 0.3);
    }
    
    .event-actions .btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        color: white;
    }
    
    .event-actions .btn-primary:hover {
        background: linear-gradient(135deg, #0056b3, #004085);
        box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
    }
    
    /* Similar Events Styling */
    .project-thumb {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .default-similar-event {
        height: 200px;
        background: linear-gradient(135deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 2.5rem;
    }
    
    .project-thumb .event-status-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        font-size: 0.95rem;
        padding: 6px 12px;
        border-radius: 20px;
        color: white;
    }
    
    /* Modal Styling */
    .modal-header {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-bottom: none;
    }
    
    .modal-header .btn-close {
        filter: invert(1);
    }
    
    .modal-body {
        padding: 2rem;
    }
    
    .form-label {
        font-weight: 600;
        color: #333;
        margin-bottom: 0.5rem;
    }
    
    .form-label i {
        color: #28a745;
        margin-right: 0.5rem;
    }
    
    .form-control:focus,
    .form-select:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
    }
    
    .current-image-preview {
        max-width: 200px;
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .modal-footer {
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
    
    .modal-footer .btn {
        padding: 0.5rem 1.5rem;
        border-radius: 25px;
        font-weight: 600;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .event-image-container {
            height: 250px;
        }
        
        .event-actions {
            flex-direction: column;
            align-items: stretch;
        }
        
        .event-actions .btn {
            width: 100%;
            margin-bottom: 10px;
        }
        
        .organizer-avatar {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }
        
        .modal-body {
            padding: 1rem;
        }
    }
    
    @media (max-width: 480px) {
        .event-image-container {
            height: 200px;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    // Event data for JavaScript
    const eventData = {
        id: {{ $event->id }},
        organizerId: {{ $event->organizer_id ?? 'null' }},
        status: '{{ $event->status }}'
    };

    // JWT Authentication Check
    document.addEventListener('DOMContentLoaded', function() {
        checkAuthentication();
    });

    function checkAuthentication() {
        // Check for JWT token in localStorage or sessionStorage
        const token = getJWTToken();
        
        if (!token) {
            showGuestActions();
            return;
        }
        
        // Verify token with server
        fetch('/user', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Token invalid');
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.user) {
                showAuthenticatedActions(data.user);
            } else {
                throw new Error('Invalid response format');
            }
        })
        .catch(error => {
            console.error('Authentication error:', error);
            // Remove invalid token
            clearJWTTokens();
            showGuestActions();
        });
    }

    function getJWTToken() {
        // Check multiple possible token storage locations
        return localStorage.getItem('jwt_token') || 
               sessionStorage.getItem('jwt_token') ||
               localStorage.getItem('token') || 
               sessionStorage.getItem('token') ||
               localStorage.getItem('auth_token') || 
               sessionStorage.getItem('auth_token');
    }

    function clearJWTTokens() {
        localStorage.removeItem('jwt_token');
        localStorage.removeItem('token');
        localStorage.removeItem('auth_token');
        sessionStorage.removeItem('jwt_token');
        sessionStorage.removeItem('token');
        sessionStorage.removeItem('auth_token');
    }

    function showGuestActions() {
        // Hide authenticated actions
        const authActions = document.getElementById('authenticated-actions');
        if (authActions) authActions.style.display = 'none';
        
        // Show guest actions (login button)
        const guestActions = document.getElementById('guest-actions');
        if (guestActions) guestActions.style.display = 'inline-flex';
    }

    function showAuthenticatedActions(user) {
        // Hide guest actions
        const guestActions = document.getElementById('guest-actions');
        if (guestActions) guestActions.style.display = 'none';
        
        // Show authenticated actions container
        const authActions = document.getElementById('authenticated-actions');
        if (authActions) authActions.style.display = 'inline-flex';
        
        // Get action containers
        const organizerActions = document.getElementById('organizer-actions');
        const regularActions = document.getElementById('regular-actions');
        const adminActions = document.getElementById('admin-actions');
        
        // Hide all action containers first
        if (organizerActions) organizerActions.style.display = 'none';
        if (regularActions) regularActions.style.display = 'none';
        if (adminActions) adminActions.style.display = 'none';
        
        // Show appropriate actions based on user role and ownership
        if (user.role === 'admin') {
            // Admins see no action buttons
            if (adminActions) adminActions.style.display = 'inline-flex';
        } else if (user.id == eventData.organizerId) {
            // Event organizer/owner sees edit/delete buttons
            if (organizerActions) organizerActions.style.display = 'inline-flex';
        } else {
            // Regular users and organizers who don't own this event see subscribe button
            if (regularActions) {
                regularActions.style.display = 'inline-flex';
                // Check registration status for non-organizers
                checkRegistrationStatus(eventData.id);
            }
        }
    }

    // Event Action Functions
    function checkRegistrationStatus(eventId) {
        const token = getJWTToken();
        if (!token) return;

        fetch(`/events/${eventId}/registration-status`, {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateSubscribeButton(data);
            }
        })
        .catch(error => {
            console.error('Registration status check error:', error);
        });
    }

    function updateSubscribeButton(registrationData) {
        const subscribeBtn = document.querySelector('#regular-actions button');
        if (!subscribeBtn) return;

        if (registrationData.registered) {
            // User is already registered - show cancel option
            subscribeBtn.innerHTML = '<i class="fas fa-times-circle"></i> Annuler inscription';
            subscribeBtn.classList.remove('btn-success', 'btn-outline-success');
            subscribeBtn.classList.add('btn-warning');
            subscribeBtn.disabled = false;
            subscribeBtn.onclick = function() { unsubscribeFromEvent(eventData.id); };
        } else if (registrationData.is_full) {
            // Event is full
            subscribeBtn.innerHTML = '<i class="fas fa-times-circle"></i> Complet';
            subscribeBtn.classList.remove('btn-success', 'btn-warning');
            subscribeBtn.classList.add('btn-secondary');
            subscribeBtn.disabled = true;
            subscribeBtn.onclick = null;
        } else {
            // Can register
            let capacityText = '';
            if (registrationData.capacity) {
                const remaining = registrationData.capacity - registrationData.registration_count;
                capacityText = ` (${remaining} places restantes)`;
            }
            subscribeBtn.innerHTML = `<i class="fas fa-calendar-check"></i> S'inscrire${capacityText}`;
            subscribeBtn.classList.remove('btn-warning', 'btn-secondary', 'btn-outline-success');
            subscribeBtn.classList.add('btn-success');
            subscribeBtn.disabled = false;
            subscribeBtn.onclick = function() { subscribeToEvent(eventData.id); };
        }
    }

    function subscribeToEvent(eventId) {
        const token = getJWTToken();
        if (!token) {
            alert('Vous devez être connecté pour vous inscrire.');
            window.location.href = '{{ route("login") }}';
            return;
        }

        // Show loading state
        const subscribeBtn = document.querySelector('#regular-actions button');
        const originalContent = subscribeBtn.innerHTML;
        subscribeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Inscription...';
        subscribeBtn.disabled = true;

        fetch(`/events/${eventId}/subscribe`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Inscription réussie !');
                // Update button to show cancel option
                subscribeBtn.innerHTML = '<i class="fas fa-times-circle"></i> Annuler inscription';
                subscribeBtn.classList.remove('btn-success');
                subscribeBtn.classList.add('btn-warning');
                subscribeBtn.disabled = false;
                subscribeBtn.onclick = function() { unsubscribeFromEvent(eventData.id); };
                
                // Refresh registration status to get updated capacity info
                checkRegistrationStatus(eventData.id);
            } else {
                alert(data.message || 'Erreur lors de l\'inscription.');
                // Restore button
                subscribeBtn.innerHTML = originalContent;
                subscribeBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Subscription error:', error);
            alert('Erreur lors de l\'inscription. Veuillez réessayer.');
            // Restore button
            subscribeBtn.innerHTML = originalContent;
            subscribeBtn.disabled = false;
        });
    }

    function unsubscribeFromEvent(eventId) {
        if (!confirm('Êtes-vous sûr de vouloir annuler votre inscription à cet événement ?')) {
            return;
        }

        const token = getJWTToken();
        if (!token) {
            alert('Vous devez être connecté pour annuler votre inscription.');
            window.location.href = '{{ route("login") }}';
            return;
        }

        // Show loading state
        const subscribeBtn = document.querySelector('#regular-actions button');
        const originalContent = subscribeBtn.innerHTML;
        subscribeBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Annulation...';
        subscribeBtn.disabled = true;

        fetch(`/events/${eventId}/unsubscribe`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message || 'Inscription annulée avec succès !');
                // Update button to show subscribe state
                subscribeBtn.innerHTML = '<i class="fas fa-calendar-check"></i> S\'inscrire';
                subscribeBtn.classList.remove('btn-warning', 'btn-outline-success');
                subscribeBtn.classList.add('btn-success');
                subscribeBtn.disabled = false;
                subscribeBtn.onclick = function() { subscribeToEvent(eventData.id); };
                
                // Refresh registration status to get updated capacity info
                checkRegistrationStatus(eventData.id);
            } else {
                alert(data.message || 'Erreur lors de l\'annulation.');
                // Restore button
                subscribeBtn.innerHTML = originalContent;
                subscribeBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Unsubscribe error:', error);
            alert('Erreur lors de l\'annulation. Veuillez réessayer.');
            // Restore button
            subscribeBtn.innerHTML = originalContent;
            subscribeBtn.disabled = false;
        });
    }

    function deleteEvent(eventId) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.')) {
            return;
        }

        const token = getJWTToken();
        if (!token) {
            alert('Vous devez être connecté pour effectuer cette action.');
            window.location.href = '{{ route("login") }}';
            return;
        }

        fetch(`/organizer/events/${eventId}`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("front.events.index") }}';
            } else {
                alert(data.message || 'Erreur lors de la suppression.');
            }
        })
        .catch(error => {
            console.error('Delete error:', error);
            alert('Erreur lors de la suppression. Veuillez réessayer.');
        });
    }

    // Modal functions
    function openEditModal() {
        loadCategories();
        populateEventForm();
        const modal = new bootstrap.Modal(document.getElementById('editEventModal'));
        modal.show();
    }

    function loadCategories() {
        const token = getJWTToken();
        fetch('/api/categories', {
            method: 'GET',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            const categorySelect = document.getElementById('edit_category_id');
            categorySelect.innerHTML = '<option value="">Sélectionner une catégorie</option>';
            
            if (data.categories) {
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.id;
                    option.textContent = category.name;
                    categorySelect.appendChild(option);
                });
            }
        })
        .catch(error => {
            console.error('Error loading categories:', error);
        });
    }

    function populateEventForm() {
        // Populate form with current event data
        document.getElementById('edit_title').value = '{{ $event->title }}';
        document.getElementById('edit_location').value = '{{ $event->location }}';
        document.getElementById('edit_capacity').value = '{{ $event->capacity ?? "" }}';
        document.getElementById('edit_status').value = '{{ $event->status }}';
        document.getElementById('edit_description').value = `{{ $event->description ?? "" }}`;
        
        // Format date for datetime-local input
        @if($event->date)
            const eventDate = new Date('{{ $event->date->format("Y-m-d H:i:s") }}');
            const formattedDate = eventDate.toISOString().slice(0, 16);
            document.getElementById('edit_date').value = formattedDate;
        @endif
        
        // Set category (will be set after categories are loaded)
        setTimeout(() => {
            document.getElementById('edit_category_id').value = '{{ $event->category_id }}';
        }, 500);
        
        // Show current image if exists
        const imagePreview = document.getElementById('current-image-preview');
        @if($event->img && Storage::disk('public')->exists($event->img))
            imagePreview.innerHTML = `
                <div class="mt-2">
                    <label class="form-text">Image actuelle:</label><br>
                    <img src="{{ asset('storage/' . $event->img) }}" alt="Current image" class="current-image-preview">
                </div>
            `;
        @else
            imagePreview.innerHTML = '<div class="form-text text-muted">Aucune image actuelle</div>';
        @endif
    }

    function saveEventChanges() {
        const token = getJWTToken();
        if (!token) {
            alert('Vous devez être connecté pour effectuer cette action.');
            return;
        }

        const form = document.getElementById('editEventForm');
        const formData = new FormData(form);
        
        // Show loading state
        const saveBtn = document.querySelector('[onclick="saveEventChanges()"]');
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enregistrement...';
        saveBtn.disabled = true;

        fetch(`/organizer/events/{{ $event->id }}`, {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editEventModal'));
                modal.hide();
                // Reload page to show updated data
                window.location.reload();
            } else {
                alert(data.message || 'Erreur lors de la mise à jour.');
            }
        })
        .catch(error => {
            console.error('Update error:', error);
            alert('Erreur lors de la mise à jour. Veuillez réessayer.');
        })
        .finally(() => {
            // Restore button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;
        });
    }
</script>
@endpush
@endsection