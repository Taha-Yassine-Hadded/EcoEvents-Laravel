@extends('layouts.app')

@section('title', $community->name . ' - EcoEvents')

@push('styles')
<style>
    /* Styles améliorés pour la page de détails */
    body {
        background-color: #ffffff !important;
    }

    .card {
        border: none;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 25px rgba(0,0,0,0.12);
    }

    .badge {
        font-size: 0.85rem;
        padding: 0.6rem 1.2rem;
        border-radius: 50px;
    }

    .member-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 0.9rem;
    }

    .progress-modern {
        height: 12px;
        border-radius: 10px;
        background-color: #e9ecef;
        overflow: hidden;
    }

    .progress-bar-modern {
        background: linear-gradient(90deg, #28a745, #20c997);
        border-radius: 10px;
        transition: width 0.6s ease;
    }

    .action-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-left: 4px solid #28a745;
    }

    .stat-item {
        text-align: center;
        padding: 1rem;
        border-radius: 10px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        background: #e9ecef;
        transform: translateY(-2px);
    }

   
    .btn {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;

        position: relative !important;
    }

    .btn-success {
        background-color: #28a745 !important;
        border-color: #28a745 !important;
        color: white !important;
    }

    /* Forcer la visibilité du bouton créer */
    .btn-success:not(.d-none) {
        display: inline-block !important;
    }

    /* S'assurer que l'en-tête est au-dessus de tout */
    .bg-white {
        position: relative !important;
        z-index: 1040 !important;
    }

    /* Correction pour éviter que le contenu soit caché sous la navbar fixe */
    body {
        padding-top: 0 !important;
    }

    /* Navbar ne doit pas être fixe si elle cache le contenu */
    .navbar-fixed-top, .fixed-top {
        position: relative !important;
    }
</style>
@endpush

@section('content')


<!-- En-tête simplifié -->
<div class="bg-white py-4 border-bottom">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2">
                        <li class="breadcrumb-item"><a href="{{ route('communities.index') }}">Communautés</a></li>
                        <li class="breadcrumb-item active">{{ $community->name }}</li>
                    </ol>
                </nav>
                <h1 class="h2 mb-0">{{ $community->name }}</h1>
            </div>
            <div class="col-md-4 text-end">
                @auth
                    @if(Auth::user()->role === 'organizer')
                        <a href="{{ route('organizer.communities.create') }}"
                           class="btn btn-success btn-sm"
                           style="z-index: 1060 !important; position: relative !important; display: inline-block !important;">
                            ➕ Créer une communauté
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Informations de base -->
<div class="bg-light py-3">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <p class="mb-0 text-muted">{{ $community->description }}</p>
            </div>
            <div class="col-md-4">
                <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                    <span class="badge bg-success">
                        📂 {{ $community->getCategories()[$community->category] ?? $community->category }}
                    </span>
                    @if($community->location)
                        <span class="badge bg-info">
                            📍 {{ $community->location }}
                        </span>
                    @endif
                    <span class="badge bg-warning">
                        👥 {{ $community->members->count() }} membres
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Contenu principal -->
<div class="container py-4">
    <div class="row">
        <!-- Contenu principal -->
        <div class="col-lg-8">
            <!-- Actions d'adhésion modernes -->
            <div class="card mb-4 action-card">
                <div class="card-body">
                    @if($currentUser)
                        @if($userIsMember)
                            @if($membershipStatus === 'approved')
                                <div class="alert alert-success border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-success rounded-circle p-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-check text-white fa-lg"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="mb-1 text-success">🎉 Félicitations !</h5>
                                            <p class="mb-2">Vous êtes officiellement membre de cette communauté</p>
                                            <small class="text-muted">
                                                <i class="fas fa-star text-warning me-1"></i>
                                                Vous pouvez maintenant participer à toutes les activités
                                            </small>
                                        </div>
                                        <div>
                                            <form action="{{ route('communities.leave', $community) }}" method="POST" class="d-inline"
                                                  onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir quitter cette communauté ? Cette action est irréversible.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm">
                                                    <i class="fas fa-sign-out-alt me-1"></i>Quitter
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @elseif($membershipStatus === 'pending')
                                <div class="alert alert-warning border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-warning rounded-circle p-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-hourglass-half text-white fa-lg"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-warning">⏳ Demande en attente</h6>
                                            <p class="mb-2">Votre demande d'adhésion a été envoyée avec succès</p>
                                            <small class="text-muted">
                                                <i class="fas fa-info-circle me-1"></i>
                                                L'organisateur {{ $community->organizer->name }} l'examinera bientôt
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @else
                            @if($community->isFull())
                                <div class="alert alert-danger border-0 shadow-sm">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <div class="bg-danger rounded-circle p-2" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-users text-white fa-lg"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 text-danger">🚫 Communauté complète</h6>
                                            <p class="mb-2">Cette communauté a atteint sa capacité maximale</p>
                                            <small class="text-muted">
                                                <i class="fas fa-users me-1"></i>
                                                {{ $community->max_members }} membres maximum - Revenez plus tard
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <div class="mb-4">
                                        <div class="bg-success rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-leaf text-white fa-2x"></i>
                                        </div>
                                        <h4 class="text-success mb-2">Rejoignez notre communauté !</h4>
                                        <p class="text-muted mb-4">Connectez-vous avec {{ $community->members->where('status', 'approved')->count() }} autres passionnés d'écologie</p>
                                    </div>

                                    <form action="{{ route('communities.join', $community) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-lg px-5">
                                            <i class="fas fa-plus-circle me-2"></i>
                                            Envoyer ma demande d'adhésion
                                        </button>
                                    </form>

                                    <div class="mt-3">
                                        <small class="text-muted">
                                            <i class="fas fa-shield-alt me-1"></i>
                                            Votre demande sera examinée par l'organisateur
                                        </small>
                                    </div>
                                </div>
                            @endif
                        @endif
                    @else
                        <div class="text-center py-4">
                            <div class="mb-4">
                                <div class="bg-primary rounded-circle mx-auto mb-3" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-user-plus text-white fa-2x"></i>
                                </div>
                                <h4 class="text-primary mb-2">Créez votre compte</h4>
                                <p class="text-muted mb-4">Rejoignez notre plateforme pour accéder à toutes les communautés écologiques</p>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                                <a href="{{ route('login') }}" class="btn btn-success btn-lg">
                                    <i class="fas fa-sign-in-alt me-2"></i>Se connecter
                                </a>
                                <a href="{{ route('register') }}" class="btn btn-outline-success btn-lg">
                                    <i class="fas fa-user-plus me-2"></i>Créer un compte
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

                <!-- Informations de la communauté -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">À propos de cette communauté</h5>
                        <p class="card-text">{{ $community->description }}</p>

                        <div class="row g-3 mt-3">
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <div>
                                        <small class="text-muted">Créée le</small><br>
                                        <span>{{ $community->created_at->format('d/m/Y') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user text-muted me-2"></i>
                                    <div>
                                        <small class="text-muted">Organisateur</small><br>
                                        <span>{{ $community->organizer->name }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des membres améliorée -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-users text-success me-2"></i>
                                Membres de la communauté
                            </h5>
                            <span class="badge bg-success">{{ $community->members->where('status', 'approved')->count() }}</span>
                        </div>

                        @if($community->members->where('status', 'approved')->count() > 0)
                            <div class="row g-3">
                                @foreach($community->members->where('status', 'approved')->take(8) as $member)
                                    <div class="col-md-6">
                                        <div class="d-flex align-items-center p-3 bg-light rounded-3 border">
                                            <div class="member-avatar me-3">
                                                {{ strtoupper(substr($member->user->name, 0, 1)) }}
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="fw-semibold">{{ $member->user->name }}</div>
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar-alt me-1"></i>
                                                    Membre depuis {{ $member->created_at->format('M Y') }}
                                                </small>
                                            </div>
                                            @if($member->user_id === $community->organizer_id)
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-crown"></i> Organisateur
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @if($community->members->where('status', 'approved')->count() > 8)
                                <div class="text-center mt-4">
                                    <div class="alert alert-info border-0">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Et <strong>{{ $community->members->where('status', 'approved')->count() - 8 }}</strong> autres membres dans cette communauté
                                    </div>
                                </div>
                            @endif
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="fas fa-users fa-3x text-muted"></i>
                                </div>
                                <h6 class="text-muted">Aucun membre pour le moment</h6>
                                <p class="text-muted mb-3">Cette communauté attend ses premiers membres passionnés !</p>
                                @if(!$currentUser)
                                    <a href="{{ route('login') }}" class="btn btn-outline-success">
                                        Connectez-vous pour être le premier
                                    </a>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Image de la communauté -->
                @if($community->image)
                    <div class="card mb-4">
                        <img src="{{ asset('storage/' . $community->image) }}" alt="{{ $community->name }}"
                             class="card-img-top" style="height: 250px; object-fit: cover;">
                    </div>
                @endif

                <!-- Statistiques détaillées -->
                <div class="card mb-4">
                    <div class="card-body">
                        <h6 class="card-title mb-3">
                            <i class="fas fa-chart-bar text-primary me-2"></i>
                            Statistiques de la communauté
                        </h6>

                        <!-- Statistiques principales -->
                        <div class="row g-2 mb-4">
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="h4 text-success mb-1">{{ $community->members->where('status', 'approved')->count() }}</div>
                                    <small class="text-muted">Membres actifs</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="stat-item">
                                    <div class="h4 text-info mb-1">{{ $community->max_members }}</div>
                                    <small class="text-muted">Capacité max</small>
                                </div>
                            </div>
                        </div>

                        <!-- Barre de progression moderne -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">Taux de remplissage</small>
                                <small class="fw-bold text-success">
                                    {{ round(($community->members->where('status', 'approved')->count() / $community->max_members) * 100) }}%
                                </small>
                            </div>
                            <div class="progress-modern">
                                <div class="progress-bar-modern"
                                     style="width: {{ ($community->members->where('status', 'approved')->count() / $community->max_members) * 100 }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="row g-2 mb-3">
                            <div class="col-12">
                                <div class="d-flex align-items-center p-2 bg-light rounded">
                                    <i class="fas fa-calendar text-muted me-2"></i>
                                    <div>
                                        <small class="text-muted">Créée le</small><br>
                                        <span class="fw-semibold">{{ $community->created_at->format('d M Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary btn-sm"
                                    onclick="navigator.share ? navigator.share({title: '{{ $community->name }}', url: window.location.href}) : alert('Partagez cette URL : ' + window.location.href)">
                                <i class="fas fa-share-alt me-2"></i>Partager cette communauté
                            </button>
                            <a href="{{ route('communities.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-arrow-left me-2"></i>Retour aux communautés
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Informations organisateur -->
                <div class="card">
                    <div class="card-body text-center">
                        <h6 class="card-title">
                            <i class="fas fa-user-tie text-warning me-2"></i>
                            Organisateur
                        </h6>
                        <div class="member-avatar mx-auto mb-3" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ strtoupper(substr($community->organizer->name, 0, 1)) }}
                        </div>
                        <h6 class="mb-1">{{ $community->organizer->name }}</h6>
                        <small class="text-muted">Responsable de cette communauté</small>

                        @if($community->organizer->email)
                            <div class="mt-3">
                                <a href="mailto:{{ $community->organizer->email }}" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-envelope me-2"></i>Contacter
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
