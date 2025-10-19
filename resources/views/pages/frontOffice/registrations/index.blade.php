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
                                    <img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">EcoEvents
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

                    {{-- Search and Filter Section --}}
                    <div class="search-filter-section mb-4">
                        <div class="row">
                            <div class="col-lg-8">
                                <form method="GET" action="{{ route('registrations.index') }}" class="search-form">
                                    <div class="input-group">
                                        <input type="text" 
                                               name="search" 
                                               class="form-control" 
                                               placeholder="Rechercher par titre, lieu ou description..."
                                               value="{{ request('search') }}"
                                               id="searchInput">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i> Rechercher
                                        </button>
                                        @if(request('search') || request('status'))
                                            <a href="{{ route('registrations.index') }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times"></i> Effacer
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                            <div class="col-lg-4">
                                <form method="GET" action="{{ route('registrations.index') }}" class="filter-form">
                                    <input type="hidden" name="search" value="{{ request('search') }}">
                                    <select name="status" class="form-select" onchange="this.form.submit()">
                                        <option value="">Tous les statuts</option>
                                        <option value="upcoming" {{ request('status') == 'upcoming' ? 'selected' : '' }}>À venir</option>
                                        <option value="ongoing" {{ request('status') == 'ongoing' ? 'selected' : '' }}>En cours</option>
                                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Annulé</option>
                                    </select>
                                </form>
                            </div>
                        </div>

                        {{-- Results Summary --}}
                        @if(request('search') || request('status'))
                            <div class="results-summary mt-3">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    @if(request('search'))
                                        Recherche pour: "<strong>{{ request('search') }}</strong>"
                                    @endif
                                    @if(request('status'))
                                        | Statut: <strong>{{ ucfirst(request('status')) }}</strong>
                                    @endif
                                    | {{ $registrations->total() }} résultat(s) trouvé(s)
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(isset($error))
                        {{-- Error state --}}
                        <div class="text-center py-5">
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                                <h4>Erreur</h4>
                                <p>{{ $error }}</p>
                                <div class="mt-3">
                                    <a href="{{ route('front.events.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left"></i> Retour aux événements
                                    </a>
                                </div>
                            </div>
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
                                                    <img src="{{ asset('storage/events/default-event.jpg') }}" alt="{{ $registration->event->title }}">
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
                                                        Inscrit le {{ $registration->registered_at && $registration->registered_at instanceof \Carbon\Carbon ? $registration->registered_at->format('d M Y à H:i') : $registration->created_at->format('d M Y à H:i') }}
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
                                                    <button onclick="cancelRegistration({{ $registration->event->id }}, '{{ addslashes($registration->event->title) }}')" 
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
                                            <h4>{{ $totalRegistrations }}</h4>
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
                                            <h4>{{ $upcomingCount }}</h4>
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
                                            <h4>{{ $ongoingCount }}</h4>
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
                                            <h4>{{ $completedCount }}</h4>
                                            <p>Terminés</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Pagination --}}
                        <div class="pagination-section mt-4">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="pagination-info">
                                    <span class="text-muted">
                                        Affichage de {{ $registrations->firstItem() ?? 0 }} à {{ $registrations->lastItem() ?? 0 }} 
                                        sur {{ $registrations->total() }} inscription(s)
                                    </span>
                                </div>
                                <div class="pagination-links">
                                    {{ $registrations->appends(request()->query())->links() }}
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
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.08);
            border-left: 6px solid #28a745;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .registration-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #28a745, #20c997, #17a2b8);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .registration-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .registration-card:hover::before {
            opacity: 1;
        }

        /* Event Image */
        .event-image-container {
            position: relative;
            height: 180px;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            transition: all 0.3s ease;
        }

        .event-image-container:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }

        .event-image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s ease;
        }

        .event-image-container:hover img {
            transform: scale(1.1);
        }


        .event-status-badge {
            position: absolute;
            top: 15px;
            right: 15px;
            padding: 8px 16px;
            border-radius: 25px;
            font-size: 0.85rem;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255,255,255,0.2);
        }

        .status-upcoming { 
            background: linear-gradient(135deg, #ffc107, #ff8c00);
            animation: pulse 2s infinite;
        }
        .status-ongoing { 
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        .status-completed { 
            background: linear-gradient(135deg, #6c757d, #495057);
        }
        .status-cancelled { 
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }

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
            border-radius: 25px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
        }

        .registration-actions .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .registration-actions .btn:hover {
            transform: translateY(-3px) scale(1.05);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .registration-actions .btn:hover::before {
            left: 100%;
        }

        .registration-actions .btn-outline-primary {
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
        }

        .registration-actions .btn-outline-primary:hover {
            background: linear-gradient(135deg, #0056b3, #004085);
            color: white;
        }

        .registration-actions .btn-outline-danger {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            border: none;
        }

        .registration-actions .btn-outline-danger:hover {
            background: linear-gradient(135deg, #c82333, #a71e2a);
            color: white;
        }

        .registration-actions .btn-outline-danger:disabled {
            background: linear-gradient(135deg, #6c757d, #495057);
            color: white;
            opacity: 0.7;
        }

        /* Summary Statistics */
        .registrations-summary {
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            backdrop-filter: blur(10px);
        }

        .summary-card {
            display: flex;
            align-items: center;
            padding: 25px;
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 15px;
            margin-bottom: 20px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(135deg, #28a745, #20c997);
            transition: width 0.3s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .summary-card:hover::before {
            width: 8px;
        }

        .summary-icon {
            font-size: 3rem;
            margin-right: 25px;
            padding: 15px;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(40, 167, 69, 0.1), rgba(32, 201, 151, 0.1));
            transition: all 0.3s ease;
        }

        .summary-card:hover .summary-icon {
            transform: scale(1.1) rotate(5deg);
        }

        .summary-content h4 {
            font-size: 2.5rem;
            font-weight: 800;
            margin: 0;
            color: #333;
            background: linear-gradient(135deg, #28a745, #20c997);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .summary-content p {
            margin: 0;
            color: #666;
            font-weight: 600;
            font-size: 1.1rem;
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

        /* Search and Filter Section */
        .search-filter-section {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .search-form .input-group {
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .search-form .input-group-text {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border: none;
            padding: 12px 15px;
        }

        .search-form .form-control {
            border: none;
            padding: 12px 15px;
            font-size: 1rem;
        }

        .search-form .form-control:focus {
            box-shadow: none;
            border: none;
        }

        .search-form .btn {
            border-radius: 0 25px 25px 0;
            padding: 12px 20px;
            font-weight: 600;
        }

        .filter-form .form-select {
            border-radius: 25px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: all 0.3s ease;
        }

        .filter-form .form-select:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .results-summary .alert {
            border-radius: 15px;
            border: none;
            background: linear-gradient(135deg, #e3f2fd, #f3e5f5);
        }

        /* Pagination */
        .pagination-section {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .pagination-info {
            font-weight: 500;
            color: #666;
        }

        .pagination-links .pagination {
            margin: 0;
        }

        .pagination-links .page-link {
            border-radius: 10px;
            margin: 0 2px;
            border: 2px solid #e9ecef;
            color: #28a745;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .pagination-links .page-link:hover {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-color: #28a745;
            transform: translateY(-2px);
        }

        .pagination-links .page-item.active .page-link {
            background: linear-gradient(135deg, #28a745, #20c997);
            border-color: #28a745;
            color: white;
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

            .search-filter-section {
                padding: 15px;
            }

            .search-form .input-group {
                flex-direction: column;
                border-radius: 15px;
            }

            .search-form .input-group-text {
                border-radius: 15px 15px 0 0;
            }

            .search-form .form-control {
                border-radius: 0;
            }

            .search-form .btn {
                border-radius: 0 0 15px 15px;
                margin-top: 5px;
            }

            .filter-form .form-select {
                margin-top: 10px;
            }

            .pagination-section {
                padding: 15px;
            }

            .pagination-section .d-flex {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>
    @endpush

    @push('scripts')
    <script>

        function getJWTToken() {
            return localStorage.getItem('jwt_token') || 
                   sessionStorage.getItem('jwt_token') ||
                   localStorage.getItem('token') || 
                   sessionStorage.getItem('token') ||
                   localStorage.getItem('auth_token') || 
                   sessionStorage.getItem('auth_token');
        }

        function cancelRegistration(eventId, eventTitle) {
            showUnsubscribeConfirmation(eventId, eventTitle);
        }
        
        function showUnsubscribeConfirmation(eventId, eventTitle) {
            // Create custom confirmation modal
            const confirmationModal = document.createElement('div');
            confirmationModal.className = 'modal fade';
            confirmationModal.id = 'unsubscribeConfirmationModal';
            confirmationModal.innerHTML = `
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content" style="border: none; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.3);">
                        <div class="modal-body text-center p-4">
                            <div class="mb-3">
                                <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <h4 class="mb-3">Confirmer l'annulation</h4>
                            <p class="text-muted mb-4">Êtes-vous sûr de vouloir annuler votre inscription à l'événement <strong>"${eventTitle}"</strong> ?</p>
                            <div class="d-flex gap-2 justify-content-center">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 25px; padding: 8px 20px;">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </button>
                                <button type="button" class="btn btn-danger" id="confirmUnsubscribe" style="border-radius: 25px; padding: 8px 20px;">
                                    <i class="fas fa-check me-2"></i>Confirmer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
            
            document.body.appendChild(confirmationModal);
            
            const modal = new bootstrap.Modal(confirmationModal);
            modal.show();
            
            // Handle confirmation
            document.getElementById('confirmUnsubscribe').addEventListener('click', function() {
                modal.hide();
                setTimeout(() => {
                    document.body.removeChild(confirmationModal);
                    proceedWithUnsubscribe(eventId);
                }, 300);
            });
            
            // Clean up on cancel
            confirmationModal.addEventListener('hidden.bs.modal', function() {
                document.body.removeChild(confirmationModal);
            });
        }
        
        function proceedWithUnsubscribe(eventId) {
            const token = getJWTToken();
            if (!token) {
                showToast('error', 'Vous devez être connecté pour effectuer cette action.');
                window.location.href = '{{ route("login") }}';
                return;
            }

            // Find the button that was clicked and show loading state
            const buttons = document.querySelectorAll(`button[onclick*="cancelRegistration(${eventId}"]`);
            if (buttons.length > 0) {
                const btn = buttons[0];
                const originalContent = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Annulation...';
                btn.disabled = true;
                
                // Store original content for restoration
                btn.setAttribute('data-original-content', originalContent);
            }

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
                    showToast('success', data.message || 'Inscription annulée avec succès !');
                    // Reload page to show updated registrations
                    setTimeout(() => {
                        window.location.reload();
                    }, 1500);
                } else {
                    showToast('error', data.message || 'Erreur lors de l\'annulation.');
                    // Restore button
                    const buttons = document.querySelectorAll(`button[onclick*="cancelRegistration(${eventId}"]`);
                    if (buttons.length > 0) {
                        const btn = buttons[0];
                        const originalContent = btn.getAttribute('data-original-content');
                        if (originalContent) {
                            btn.innerHTML = originalContent;
                            btn.disabled = false;
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Cancel registration error:', error);
                showToast('error', 'Erreur lors de l\'annulation. Veuillez réessayer.');
                // Restore button
                const buttons = document.querySelectorAll(`button[onclick*="cancelRegistration(${eventId}"]`);
                if (buttons.length > 0) {
                    const btn = buttons[0];
                    const originalContent = btn.getAttribute('data-original-content');
                    if (originalContent) {
                        btn.innerHTML = originalContent;
                        btn.disabled = false;
                    }
                }
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
        
        // Toast notification functions
        function showToast(type, message) {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
                toastContainer.style.zIndex = '9999';
                document.body.appendChild(toastContainer);
            }
            
            // Create toast element
            const toastId = 'toast-' + Date.now();
            const iconClass = type === 'success' ? 'fas fa-check-circle text-success' : 'fas fa-exclamation-circle text-danger';
            const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
            
            const toastElement = document.createElement('div');
            toastElement.id = toastId;
            toastElement.className = `toast ${bgClass} text-white`;
            toastElement.setAttribute('role', 'alert');
            toastElement.innerHTML = `
                <div class="toast-header ${bgClass} text-white border-0">
                    <i class="${iconClass} me-2"></i>
                    <strong class="me-auto">${type === 'success' ? 'Succès' : 'Erreur'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            `;
            
            toastContainer.appendChild(toastElement);
            
            // Show toast
            const toast = new bootstrap.Toast(toastElement, {
                autohide: true,
                delay: 5000
            });
            toast.show();
            
            // Remove element after hiding
            toastElement.addEventListener('hidden.bs.toast', function() {
                if (toastContainer.contains(toastElement)) {
                    toastContainer.removeChild(toastElement);
                }
            });
        }
    </script>
    @endpush
@endsection