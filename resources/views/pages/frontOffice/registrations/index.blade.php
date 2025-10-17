@extends('layouts.app')

@section('title', 'Echofy - Mes Inscriptions')

@section('content')
    {{-- Breadcrumb Section --}}
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Mes Inscriptions</h4>
                        </div>
                        <ul>
                            <li>
                                <a href="{{ url('/') }}">
                                    <img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">Echofy
                                </a>
                            </li>
                            <li><a href="{{ route('front.events.index') }}">Événements</a></li>
                            <li>Mes Inscriptions</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Registrations Area --}}
    <div class="registrations-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="registrations-header mb-4">
                        <h2><i class="fas fa-calendar-check text-success"></i> Mes Inscriptions aux Événements</h2>
                        <p class="text-muted">Gérez vos inscriptions et consultez les détails de vos événements</p>
                    </div>

                    @if(isset($needs_js_auth) && $needs_js_auth)
                        {{-- Loading state while checking authentication --}}
                        <div id="loading-auth" class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Chargement...</span>
                            </div>
                            <p class="mt-3">Vérification de l'authentification...</p>
                        </div>

                        {{-- Error state if not authenticated --}}
                        <div id="auth-error" class="text-center py-5 d-none">
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i>
                                <h4>Connexion requise</h4>
                                <p>Vous devez être connecté pour voir vos inscriptions.</p>
                                <div class="mt-3">
                                    <a href="{{ route('login') }}" class="btn btn-primary me-2">
                                        <i class="fas fa-sign-in-alt"></i> Se connecter
                                    </a>
                                    <a href="{{ route('front.events.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Retour aux événements
                                    </a>
                                </div>
                            </div>
                        </div>

                        {{-- Content container that will be populated by JavaScript --}}
                        <div id="registrations-content" class="d-none">
                            <!-- Content will be loaded dynamically -->
                        </div>
                    @else

                    @if($registrations && $registrations->count() > 0)
                        <div class="registrations-grid">
                            @foreach($registrations as $registration)
                                <div class="registration-card">
                                    <div class="row align-items-center">
                                        {{-- Event Image --}}
                                        <div class="col-lg-3 col-md-4">
                                            <div class="event-image-container">
                                                @if($registration->event->img && Storage::disk('public')->exists($registration->event->img))
                                                    <img src="{{ asset('storage/' . $registration->event->img) }}" alt="{{ $registration->event->title }}">
                                                @else
                                                    <div class="default-event-image">
                                                        <i class="fas fa-calendar-alt"></i>
                                                    </div>
                                                @endif
                                                
                                                {{-- Status Badge --}}
                                                <div class="event-status-badge status-{{ $registration->event->status }}">
                                                    @switch($registration->event->status)
                                                        @case('upcoming') À venir @break
                                                        @case('ongoing') En cours @break
                                                        @case('completed') Terminé @break
                                                        @default Annulé
                                                    @endswitch
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Event Details --}}
                                        <div class="col-lg-6 col-md-5">
                                            <div class="event-details">
                                                <h4 class="event-title">
                                                    <a href="{{ route('front.events.show', $registration->event->id) }}">
                                                        {{ $registration->event->title }}
                                                    </a>
                                                </h4>
                                                
                                                <div class="event-meta">
                                                    <div class="meta-item">
                                                        <i class="fas fa-calendar-alt"></i>
                                                        <span>{{ $registration->event->date ? $registration->event->date->format('d M Y à H:i') : 'Date non définie' }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <i class="fas fa-map-marker-alt"></i>
                                                        <span>{{ $registration->event->location }}</span>
                                                    </div>
                                                    <div class="meta-item">
                                                        <i class="fas fa-tag"></i>
                                                        <span>{{ $registration->event->category->name ?? 'Non catégorisé' }}</span>
                                                    </div>
                                                    @if($registration->event->organizer)
                                                        <div class="meta-item">
                                                            <i class="fas fa-user"></i>
                                                            <span>Organisé par {{ $registration->event->organizer->name }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="registration-info">
                                                    <small class="text-muted">
                                                        <i class="fas fa-clock"></i>
                                                        Inscrit le {{ $registration->registered_at ? $registration->registered_at->format('d M Y à H:i') : $registration->created_at->format('d M Y à H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Actions --}}
                                        <div class="col-lg-3 col-md-3">
                                            <div class="registration-actions">
                                                <a href="{{ route('front.events.show', $registration->event->id) }}" 
                                                   class="btn btn-outline-primary btn-sm mb-2 w-100">
                                                    <i class="fas fa-eye"></i> Voir Détails
                                                </a>
                                                
                                                @if($registration->event->status === 'upcoming')
                                                    <button onclick="cancelRegistration({{ $registration->event->id }}, '{{ $registration->event->title }}')" 
                                                            class="btn btn-outline-danger btn-sm w-100">
                                                        <i class="fas fa-times"></i> Annuler Inscription
                                                    </button>
                                                @elseif($registration->event->status === 'ongoing')
                                                    <span class="btn btn-outline-success btn-sm w-100 disabled">
                                                        <i class="fas fa-play-circle"></i> En cours
                                                    </span>
                                                @elseif($registration->event->status === 'completed')
                                                    <span class="btn btn-outline-secondary btn-sm w-100 disabled">
                                                        <i class="fas fa-check-circle"></i> Terminé
                                                    </span>
                                                @else
                                                    <span class="btn btn-outline-dark btn-sm w-100 disabled">
                                                        <i class="fas fa-ban"></i> Annulé
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Summary Statistics --}}
                        <div class="registrations-summary mt-5">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-calendar-check text-primary"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h4>{{ $registrations->count() }}</h4>
                                            <p>Total Inscriptions</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-clock text-warning"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h4>{{ $registrations->where('event.status', 'upcoming')->count() }}</h4>
                                            <p>À venir</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-play-circle text-success"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h4>{{ $registrations->where('event.status', 'ongoing')->count() }}</h4>
                                            <p>En cours</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="summary-card">
                                        <div class="summary-icon">
                                            <i class="fas fa-check-circle text-info"></i>
                                        </div>
                                        <div class="summary-content">
                                            <h4>{{ $registrations->where('event.status', 'completed')->count() }}</h4>
                                            <p>Terminés</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- No Registrations --}}
                        <div class="no-registrations">
                            <div class="text-center">
                                <div class="no-registrations-icon">
                                    <i class="fas fa-calendar-times"></i>
                                </div>
                                <h3>Aucune inscription trouvée</h3>
                                <p class="text-muted">Vous n'êtes inscrit à aucun événement pour le moment.</p>
                                <a href="{{ route('front.events.index') }}" class="btn btn-success btn-lg mt-3">
                                    <i class="fas fa-search"></i> Découvrir les événements
                                </a>
                            </div>
                        </div>
                    @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Registrations Area */
        .registrations-area {
            padding: 80px 0;
            background: #f8f9fa;
        }

        .registrations-header h2 {
            color: #333;
            font-weight: 600;
            margin-bottom: 10px;
        }

        /* Registration Cards */
        .registrations-grid {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        .registration-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            border-left: 5px solid #28a745;
            transition: all 0.3s ease;
        }

        .registration-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }

        /* Event Image */
        .event-image-container {
            position: relative;
            height: 150px;
            border-radius: 10px;
            overflow: hidden;
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
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2.5rem;
        }

        .event-status-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
        }

        .status-upcoming { background: rgba(255, 193, 7, 0.9); }
        .status-ongoing { background: rgba(40, 167, 69, 0.9); }
        .status-completed { background: rgba(108, 117, 125, 0.9); }
        .status-cancelled { background: rgba(220, 53, 69, 0.9); }

        /* Event Details */
        .event-details {
            padding: 0 20px;
        }

        .event-title a {
            color: #333;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.3rem;
        }

        .event-title a:hover {
            color: #28a745;
        }

        .event-meta {
            margin: 15px 0;
        }

        .meta-item {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9rem;
            color: #666;
        }

        .meta-item i {
            color: #28a745;
            width: 20px;
            margin-right: 10px;
        }

        .registration-info {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        /* Actions */
        .registration-actions {
            text-align: center;
        }

        .registration-actions .btn {
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .registration-actions .btn:hover {
            transform: translateY(-2px);
        }

        /* Summary Statistics */
        .registrations-summary {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .summary-card {
            display: flex;
            align-items: center;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .summary-icon {
            font-size: 2.5rem;
            margin-right: 20px;
        }

        .summary-content h4 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            color: #333;
        }

        .summary-content p {
            margin: 0;
            color: #666;
            font-weight: 500;
        }

        /* No Registrations */
        .no-registrations {
            text-align: center;
            padding: 80px 20px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .no-registrations-icon {
            font-size: 5rem;
            color: #ddd;
            margin-bottom: 30px;
        }

        .no-registrations h3 {
            color: #333;
            font-weight: 600;
            margin-bottom: 15px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .registrations-area {
                padding: 40px 0;
            }

            .registration-card {
                padding: 20px 15px;
            }

            .event-details {
                padding: 20px 0;
            }

            .event-image-container {
                height: 120px;
                margin-bottom: 15px;
            }

            .registration-actions {
                margin-top: 15px;
            }

            .summary-card {
                text-align: center;
                flex-direction: column;
            }

            .summary-icon {
                margin-right: 0;
                margin-bottom: 10px;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        @if(isset($needs_js_auth) && $needs_js_auth)
        // JWT Token Helper Functions
        function getJWTToken() {
            return localStorage.getItem('jwt_token') || 
                   sessionStorage.getItem('jwt_token') ||
                   localStorage.getItem('token') || 
                   sessionStorage.getItem('token') ||
                   localStorage.getItem('auth_token') || 
                   sessionStorage.getItem('auth_token');
        }

        // Authentication check on page load
        document.addEventListener('DOMContentLoaded', function() {
            const token = getJWTToken();
            
            if (!token) {
                showAuthError();
                return;
            }
            
            // Verify token with server and load registrations
            fetch('/my-registrations', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Authentication failed');
                }
                return response.json();
            })
            .then(data => {
                hideLoading();
                renderRegistrations(data.registrations);
            })
            .catch(error => {
                console.error('Auth error:', error);
                showAuthError();
            });
        });

        function hideLoading() {
            document.getElementById('loading-auth').classList.add('d-none');
        }

        function showAuthError() {
            document.getElementById('loading-auth').classList.add('d-none');
            document.getElementById('auth-error').classList.remove('d-none');
        }

        function renderRegistrations(registrations) {
            const container = document.getElementById('registrations-content');
            
            if (!registrations || registrations.length === 0) {
                container.innerHTML = `
                    <div class="no-registrations">
                        <div class="text-center">
                            <div class="no-registrations-icon">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <h3>Aucune inscription trouvée</h3>
                            <p class="text-muted">Vous n'êtes inscrit à aucun événement pour le moment.</p>
                            <a href="/events" class="btn btn-success btn-lg mt-3">
                                <i class="fas fa-search"></i> Découvrir les événements
                            </a>
                        </div>
                    </div>
                `;
            } else {
                // Render registration cards and summary
                let cardsHtml = '<div class="registrations-grid">';
                
                registrations.forEach(registration => {
                    const event = registration.event;
                    const statusText = {
                        'upcoming': 'À venir',
                        'ongoing': 'En cours', 
                        'completed': 'Terminé',
                        'cancelled': 'Annulé'
                    }[event.status] || 'Statut inconnu';
                    
                    cardsHtml += `
                        <div class="registration-card">
                            <div class="row align-items-center">
                                <div class="col-lg-3 col-md-4">
                                    <div class="event-image-container">
                                        ${event.img ? `<img src="/storage/${event.img}" alt="${event.title}">` : '<div class="default-event-image"><i class="fas fa-calendar-alt"></i></div>'}
                                        <div class="event-status-badge status-${event.status}">${statusText}</div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-5">
                                    <div class="event-details">
                                        <h4 class="event-title">
                                            <a href="/events/${event.id}">${event.title}</a>
                                        </h4>
                                        <div class="event-meta">
                                            <div class="meta-item">
                                                <i class="fas fa-calendar-alt"></i>
                                                <span>${event.date ? new Date(event.date).toLocaleDateString('fr-FR', {day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit'}) : 'Date non définie'}</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="fas fa-map-marker-alt"></i>
                                                <span>${event.location || 'Lieu non défini'}</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="fas fa-user"></i>
                                                <span>Organisé par ${event.organizer || 'Non défini'}</span>
                                            </div>
                                            <div class="meta-item">
                                                <i class="fas fa-tag"></i>
                                                <span>${event.category ? event.category.name : 'Catégorie non définie'}</span>
                                            </div>
                                        </div>
                                        <div class="registration-info">
                                            <small class="text-muted">
                                                <i class="fas fa-clock"></i>
                                                Inscrit le ${new Date(registration.created_at).toLocaleDateString('fr-FR')}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-3">
                                    <div class="event-actions text-end">
                                        <a href="/events/${event.id}" class="btn btn-outline-primary mb-2">
                                            <i class="fas fa-eye"></i> Voir détails
                                        </a>
                                        <button class="btn btn-outline-danger" onclick="cancelRegistration(${event.id}, '${event.title.replace(/'/g, "\\'")}')">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                cardsHtml += '</div>';
                
                // Add summary statistics
                const upcoming = registrations.filter(r => r.event.status === 'upcoming').length;
                const ongoing = registrations.filter(r => r.event.status === 'ongoing').length;
                const completed = registrations.filter(r => r.event.status === 'completed').length;
                
                cardsHtml += `
                    <div class="registrations-summary mt-5">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <div class="summary-icon">
                                        <i class="fas fa-calendar-check text-primary"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>${registrations.length}</h4>
                                        <p>Total Inscriptions</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <div class="summary-icon">
                                        <i class="fas fa-clock text-warning"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>${upcoming}</h4>
                                        <p>À venir</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <div class="summary-icon">
                                        <i class="fas fa-play-circle text-success"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>${ongoing}</h4>
                                        <p>En cours</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="summary-card">
                                    <div class="summary-icon">
                                        <i class="fas fa-check-circle text-info"></i>
                                    </div>
                                    <div class="summary-content">
                                        <h4>${completed}</h4>
                                        <p>Terminés</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                container.innerHTML = cardsHtml;
            }
            
            container.classList.remove('d-none');
        }
        @endif

        function getJWTToken() {
            return localStorage.getItem('jwt_token') || 
                   sessionStorage.getItem('jwt_token') ||
                   localStorage.getItem('token') || 
                   sessionStorage.getItem('token') ||
                   localStorage.getItem('auth_token') || 
                   sessionStorage.getItem('auth_token');
        }

        function cancelRegistration(eventId, eventTitle) {
            if (!confirm(`Êtes-vous sûr de vouloir annuler votre inscription à l'événement "${eventTitle}" ?`)) {
                return;
            }

            const token = getJWTToken();
            if (!token) {
                alert('Vous devez être connecté pour effectuer cette action.');
                window.location.href = '{{ route("login") }}';
                return;
            }

            // Show loading state
            const btn = event.target;
            const originalContent = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Annulation...';
            btn.disabled = true;

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
                    // Reload page to show updated registrations
                    window.location.reload();
                } else {
                    alert(data.message || 'Erreur lors de l\'annulation.');
                    // Restore button
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Cancel registration error:', error);
                alert('Erreur lors de l\'annulation. Veuillez réessayer.');
                // Restore button
                btn.innerHTML = originalContent;
                btn.disabled = false;
            });
        }

        function getJWTToken() {
            return localStorage.getItem('jwt_token') || 
                   sessionStorage.getItem('jwt_token') ||
                   localStorage.getItem('token') || 
                   sessionStorage.getItem('token') ||
                   localStorage.getItem('auth_token') || 
                   sessionStorage.getItem('auth_token');
        }
    </script>
    @endpush
@endsection