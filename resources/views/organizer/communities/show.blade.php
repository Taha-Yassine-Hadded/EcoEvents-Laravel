@extends('layouts.organizer')

@section('title', $community->name . ' - EcoEvents')
@section('page-title', $community->name)
@section('page-subtitle', 'Gestion de votre communauté')

@section('header-actions')
    <div class="btn-group">
        <a href="{{ route('organizer.communities.edit', $community) }}" class="btn btn-warning">
            <i class="fas fa-edit me-2"></i>Modifier
        </a>
        <form action="{{ route('organizer.communities.toggle-status', $community) }}" method="POST" class="d-inline">
            @csrf
            @method('PATCH')
            <button type="submit" class="btn btn-{{ $community->is_active ? 'secondary' : 'success' }}">
                <i class="fas fa-{{ $community->is_active ? 'pause' : 'play' }} me-2"></i>
                {{ $community->is_active ? 'Désactiver' : 'Activer' }}
            </button>
        </form>
        <a href="{{ route('organizer.communities.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
@endsection

@section('content')
<div class="row">
    <!-- Informations principales -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <img src="{{ $community->image_url }}" alt="{{ $community->name }}" 
                             class="img-fluid rounded mb-3" style="width: 100%; height: 200px; object-fit: cover;">
                    </div>
                    <div class="col-md-8">
                        <h4 class="card-title">{{ $community->name }}</h4>
                        <p class="text-muted mb-2">
                            <i class="fas fa-tag me-2"></i>{{ $community->category_label }}
                        </p>
                        @if($community->location)
                            <p class="text-muted mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ $community->location }}
                            </p>
                        @endif
                        <p class="text-muted mb-3">
                            <i class="fas fa-calendar me-2"></i>Créée le {{ $community->created_at->format('d/m/Y à H:i') }}
                        </p>
                        <div class="mb-3">
                            @if($community->is_active)
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle me-1"></i>Active
                                </span>
                            @else
                                <span class="badge bg-secondary fs-6">
                                    <i class="fas fa-pause-circle me-1"></i>Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
                
                <h6><i class="fas fa-info-circle me-2"></i>Description</h6>
                <p class="text-muted">{{ $community->description }}</p>
            </div>
                <!-- Demandes en attente -->
                @if($community->members->where('status', 'pending')->count() > 0)
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0">
                            ⏳ Demandes en attente
                            <span class="badge bg-dark">{{ $community->members->where('status', 'pending')->count() }}</span>
                        </h5>
                    </div>
                    <div class="card-body">
                        @foreach($community->members->where('status', 'pending') as $pendingMember)
                            <div class="d-flex justify-content-between align-items-center mb-3 p-3 border rounded">
                                <div>
                                    <strong>{{ $pendingMember->user->name }}</strong><br>
                                    <small class="text-muted">{{ $pendingMember->user->email }}</small><br>
                                    <small class="text-muted">Demande envoyée le {{ $pendingMember->created_at->format('d/m/Y à H:i') }}</small>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('organizer.communities.approve', [$community, $pendingMember->user->id]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm me-2">
                                            ✅ Accepter
                                        </button>
                                    </form>
                                    <form method="POST" action="{{ route('organizer.communities.reject', [$community, $pendingMember->user->id]) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Refuser cette demande ?')">
                                            ❌ Refuser
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Membres actifs -->
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>Membres actifs
                            <span class="badge bg-success">{{ $community->active_members_count }}</span>
                        </h5>
                    </div>
            <div class="card-body">
                @if($community->members->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                    <th>Membre</th>
                                    <th>Email</th>
                                    <th>Statut</th>
                                    <th>Rejoint le</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($community->members as $member)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @if($member->user->hasProfileImage())
                                                    <img src="{{ $member->user->profile_image_url }}" alt="{{ $member->user->name }}" 
                                                         class="rounded-circle me-3" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    <div class="bg-success rounded-circle d-flex align-items-center justify-content-center me-3 text-white fw-bold" 
                                                         style="width: 40px; height: 40px;">
                                                        {{ $member->user->initials }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h6 class="mb-0">{{ $member->user->name }}</h6>
                                                    <small class="text-muted">{{ $member->user->role }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $member->user->email }}</td>
                                        <td>
                                            @if($member->status == 'approved' && $member->is_active)
                                                <span class="badge bg-success">Actif</span>
                                            @elseif($member->status == 'pending')
                                                <span class="badge bg-warning">En attente</span>
                                            @else
                                                <span class="badge bg-secondary">Inactif</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->joined_at)
                                                <small class="text-muted">{{ $member->joined_at->format('d/m/Y') }}</small>
                                            @else
                                                <small class="text-muted">{{ $member->created_at->format('d/m/Y') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                @if($member->status == 'pending')
                                                    <button class="btn btn-outline-success" title="Approuver">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                    <button class="btn btn-outline-danger" title="Rejeter">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                @else
                                                    <button class="btn btn-outline-info" title="Voir profil">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-user-plus fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucun membre pour le moment</h6>
                        <p class="text-muted">Les utilisateurs pourront bientôt rejoindre votre communauté !</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistiques -->
    <div class="col-lg-4">
        <!-- Stats générales -->
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h6 class="mb-0">
                    <i class="fas fa-chart-bar me-2"></i>Statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <small class="text-muted">Membres actifs</small>
                        <strong>{{ $stats['total_members'] }}/{{ $community->max_members }}</strong>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar bg-success" style="width: {{ $stats['capacity_percentage'] }}%"></div>
                    </div>
                    <small class="text-muted">{{ number_format($stats['capacity_percentage'], 1) }}% de capacité</small>
                </div>

                @if($stats['pending_requests'] > 0)
                    <div class="alert alert-warning py-2">
                        <i class="fas fa-clock me-2"></i>
                        <strong>{{ $stats['pending_requests'] }}</strong> demande(s) en attente
                    </div>
                @endif

                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-success mb-0">{{ $stats['total_members'] }}</h4>
                            <small class="text-muted">Membres</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info mb-0">0</h4>
                        <small class="text-muted">Événements</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-bolt me-2"></i>Actions rapides
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <button class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-calendar-plus me-2"></i>Créer un événement
                    </button>
                    <button class="btn btn-outline-info btn-sm">
                        <i class="fas fa-bullhorn me-2"></i>Envoyer une annonce
                    </button>
                    <button class="btn btn-outline-success btn-sm">
                        <i class="fas fa-share-alt me-2"></i>Partager la communauté
                    </button>
                </div>
            </div>
        </div>

        <!-- Informations -->
        <div class="card mt-4">
            <div class="card-header bg-white">
                <h6 class="mb-0">
                    <i class="fas fa-info-circle me-2"></i>Informations
                </h6>
            </div>
            <div class="card-body">
                <small class="text-muted">
                    <strong>ID Communauté :</strong> {{ $community->id }}<br>
                    <strong>Créée le :</strong> {{ $community->created_at->format('d/m/Y à H:i') }}<br>
                    <strong>Dernière mise à jour :</strong> {{ $community->updated_at->format('d/m/Y à H:i') }}
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
