@extends('layouts.app')

@section('title', 'Communautés Écologiques - EcoEvents')

@push('styles')
<style>
    /* Styles simples et épurés */
    body {
        background-color: #ffffff !important;
    }
    
    /* Forcer l'arrière-plan blanc partout */
    .main, main, #main {
        background-color: #ffffff !important;
    }
    
    /* Supprimer tous les arrière-plans sombres */
    .bg-dark, .bg-black {
        background-color: #ffffff !important;
    }
    
    .card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border: none;
        background-color: #ffffff;
    }
    
    .card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    /* Footer avec arrière-plan clair */
    .footer-area.home-six {
        background: #343a40 !important;
        margin-top: 2rem;
    }
    
    /* S'assurer que les boutons sont visibles et au-dessus de la navbar */
    .btn {
        display: inline-block !important;
        visibility: visible !important;
        opacity: 1 !important;
        z-index: 1050 !important; /* Au-dessus de la navbar Bootstrap (1030) */
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
    
    /* Forcer l'arrière-plan blanc sur toute la page */
    html, body, .wrapper, .main-content {
        background-color: #ffffff !important;
    }
</style>
@endpush

@section('content')


<!-- En-tête simple et moderne -->
<div class="bg-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="h2 mb-3">Communautés Écologiques</h1>
                <p class="text-muted mb-0">Découvrez et rejoignez des communautés passionnées par l'écologie</p>
            </div>
            <div class="col-md-4 text-end">
                @auth
                    @if(Auth::user()->role === 'organizer')
                        <a href="{{ route('organizer.communities.create') }}" 
                           class="btn btn-success" 
                           style="z-index: 1060 !important; position: relative !important; display: inline-block !important;">
                            ➕ Créer une communauté
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>

<!-- Barre de recherche simple -->
<div class="bg-light py-4 border-bottom">
    <div class="container">
        <form method="GET" action="{{ route('communities.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <input type="text" class="form-control" name="search" 
                       value="{{ request('search') }}" placeholder="🔍 Rechercher une communauté...">
            </div>
            <div class="col-md-3">
                <select class="form-select" name="category">
                    <option value="">Toutes catégories</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" name="location" 
                       value="{{ request('location') }}" placeholder="📍 Localisation">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">Filtrer</button>
            </div>
        </form>
        
        @if(request()->hasAny(['search', 'category', 'location']))
            <div class="mt-3">
                <a href="{{ route('communities.index') }}" class="btn btn-outline-secondary btn-sm">
                    ✕ Effacer les filtres
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Statistiques discrètes -->
<div class="container py-3">
    <div class="row text-center">
        <div class="col-md-4">
            <div class="d-flex align-items-center justify-content-center">
                <div class="me-3">
                    <i class="fas fa-users text-success fa-2x"></i>
                </div>
                <div>
                    <div class="h4 mb-0">{{ $stats['total_communities'] }}</div>
                    <small class="text-muted">Communautés</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center justify-content-center">
                <div class="me-3">
                    <i class="fas fa-user-friends text-info fa-2x"></i>
                </div>
                <div>
                    <div class="h4 mb-0">{{ $stats['total_members'] }}</div>
                    <small class="text-muted">Membres</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="d-flex align-items-center justify-content-center">
                <div class="me-3">
                    <i class="fas fa-tags text-warning fa-2x"></i>
                </div>
                <div>
                    <div class="h4 mb-0">{{ $stats['categories_count'] }}</div>
                    <small class="text-muted">Catégories</small>
                </div>
            </div>
        </div>
    </div>
</div>

<hr class="my-4">

<!-- Liste des communautés -->
<div class="container pb-4">
    <div class="row">
        @forelse($communities as $community)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100 shadow-sm border-0">
                    @if($community->image)
                        <img src="{{ asset('storage/' . $community->image) }}" class="card-img-top" style="height: 200px; object-fit: cover;" alt="{{ $community->name }}">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="fas fa-leaf fa-3x text-success"></i>
                        </div>
                    @endif
                    
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title mb-0">{{ $community->name }}</h5>
                            <span class="badge bg-success">{{ $community->getCategories()[$community->category] ?? $community->category }}</span>
                        </div>
                        
                        <p class="card-text text-muted small mb-2">
                            📍 {{ $community->location }} | 👥 {{ $community->members->count() }}/{{ $community->max_members }} membres
                        </p>
                        
                        <p class="card-text flex-grow-1">{{ Str::limit($community->description, 100) }}</p>
                        
                        <div class="mt-auto">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-muted">Par {{ $community->organizer->name }}</small>
                                <a href="{{ route('communities.show', $community) }}" class="btn btn-outline-primary btn-sm">
                                    Voir détails
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-search fa-3x text-muted mb-3"></i>
                    <h4 class="text-muted">Aucune communauté trouvée</h4>
                    <p class="text-muted">Essayez de modifier vos critères de recherche ou 
                        @if($currentUser && $currentUser->role === 'organizer')
                            <a href="{{ route('organizer.communities.create') }}">créez la première communauté</a>
                        @else
                            revenez plus tard
                        @endif
                    </p>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Pagination -->
    @if($communities->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $communities->links() }}
        </div>
    @endif
</div>

<!-- Espacement avant le footer -->
<div style="height: 50px;"></div>

@endsection
