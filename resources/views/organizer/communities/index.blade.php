@extends('layouts.organizer')

@section('title', 'Mes Communautés - EcoEvents')
@section('page-title', 'Mes Communautés')
@section('page-subtitle', 'Gérez toutes vos communautés écologiques')

@section('header-actions')
    <a href="{{ route('organizer.communities.create') }}" class="btn btn-eco">
        <i class="fas fa-plus me-2"></i>Nouvelle Communauté
    </a>
@endsection

@section('content')
<!-- Message d'information pour l'organisateur -->
<div class="alert alert-info mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-1">👨‍💼 Interface Organisateur</h5>
            <p class="mb-0">Ici vous gérez VOS communautés. Pour voir toutes les communautés publiques, utilisez l'interface générale.</p>
        </div>
        <a href="{{ route('communities.index') }}" class="btn btn-outline-primary btn-sm">
            🌍 Interface Publique
        </a>
    </div>
</div>

<div class="row">
    <!-- Statistiques -->
    <div class="col-md-4 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title text-success">{{ $communities->total() }}</h5>
                        <p class="card-text text-muted mb-0">Communautés créées</p>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-users fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title text-success">{{ $communities->where('is_active', true)->count() }}</h5>
                        <p class="card-text text-muted mb-0">Communautés actives</p>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        @php
                            $pendingCount = \App\Models\CommunityMember::whereHas('community', function($query) {
                                $query->where('organizer_id', Auth::id());
                            })->where('status', 'pending')->count();
                        @endphp
                        <h5 class="card-title text-warning">{{ $pendingCount }}</h5>
                        <p class="card-text text-muted mb-0">Demandes en attente</p>
                    </div>
                    <div class="text-warning">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
                @if($pendingCount > 0)
                    <div class="mt-2">
                        <a href="{{ route('organizer.membership-requests') }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-eye me-1"></i>Voir les demandes
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card stats-card">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-grow-1">
                        <h5 class="card-title text-success">{{ $communities->sum(function($c) { return $c->members->count(); }) }}</h5>
                        <p class="card-text text-muted mb-0">Membres total</p>
                    </div>
                    <div class="text-success">
                        <i class="fas fa-user-friends fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Liste des communautés -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-list me-2"></i>Liste de vos communautés
        </h5>
    </div>
    <div class="card-body">
        @if($communities->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Communauté</th>
                            <th>Catégorie</th>
                            <th>Localisation</th>
                            <th>Membres</th>
                            <th>Statut</th>
                            <th>Créée le</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($communities as $community)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $community->image_url }}" alt="{{ $community->name }}" 
                                             class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                        <div>
                                            <h6 class="mb-0">{{ $community->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($community->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $community->category_label }}</span>
                                </td>
                                <td>
                                    <i class="fas fa-map-marker-alt text-muted me-1"></i>
                                    {{ $community->location ?? 'Non spécifiée' }}
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ $community->members->count() }}/{{ $community->max_members }}</span>
                                        <div class="progress" style="width: 60px; height: 8px;">
                                            <div class="progress-bar bg-success" 
                                                 style="width: {{ ($community->members->count() / $community->max_members) * 100 }}%"></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($community->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">{{ $community->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('organizer.communities.show', $community) }}" 
                                           class="btn btn-sm btn-outline-primary" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('organizer.communities.edit', $community) }}" 
                                           class="btn btn-sm btn-outline-warning" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('organizer.communities.toggle-status', $community) }}" 
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-info" 
                                                    title="{{ $community->is_active ? 'Désactiver' : 'Activer' }}">
                                                <i class="fas fa-{{ $community->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>
                                        @if($community->members->count() == 0)
                                            <form action="{{ route('organizer.communities.destroy', $community) }}" 
                                                  method="POST" class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette communauté ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $communities->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Aucune communauté créée</h5>
                <p class="text-muted">Commencez par créer votre première communauté écologique !</p>
                <a href="{{ route('organizer.communities.create') }}" class="btn btn-eco">
                    <i class="fas fa-plus me-2"></i>Créer ma première communauté
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
