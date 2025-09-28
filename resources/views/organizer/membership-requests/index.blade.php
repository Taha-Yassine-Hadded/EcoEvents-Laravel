@extends('layouts.organizer')

@section('title', 'Demandes d\'adhésion - Interface Organisateur')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- En-tête -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-1">📋 Demandes d'adhésion</h1>
                    <p class="text-muted mb-0">Gérez les demandes d'adhésion à vos communautés</p>
                </div>
                <div>
                    <a href="{{ route('organizer.communities.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left me-2"></i>Retour aux communautés
                    </a>
                </div>
            </div>

            <!-- Statistiques -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <h4 class="mb-0">{{ $pendingRequests->total() }}</h4>
                            <small>Demandes en attente</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Liste des demandes -->
            @if($pendingRequests->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-users me-2"></i>
                            Demandes en attente ({{ $pendingRequests->total() }})
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Utilisateur</th>
                                        <th>Communauté</th>
                                        <th>Date de demande</th>
                                        <th>Statut communauté</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingRequests as $request)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle me-3">
                                                        {{ strtoupper(substr($request->user->name, 0, 1)) }}
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold">{{ $request->user->name }}</div>
                                                        <small class="text-muted">{{ $request->user->email }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <div class="fw-semibold">{{ $request->community->name }}</div>
                                                    <small class="text-muted">
                                                        {{ $request->community->members->where('status', 'approved')->count() }}/{{ $request->community->max_members }} membres
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    {{ $request->created_at->format('d/m/Y') }}
                                                    <br>
                                                    <small class="text-muted">{{ $request->created_at->format('H:i') }}</small>
                                                </div>
                                            </td>
                                            <td>
                                                @if($request->community->isFull())
                                                    <span class="badge bg-danger">
                                                        <i class="fas fa-users me-1"></i>Complète
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Places disponibles
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    @if(!$request->community->isFull())
                                                        <form action="{{ route('organizer.membership.approve', $request) }}" 
                                                              method="POST" class="d-inline">
                                                            @csrf
                                                            <button type="submit" 
                                                                    class="btn btn-success btn-sm"
                                                                    onclick="return confirm('Êtes-vous sûr de vouloir approuver cette demande ?')">
                                                                <i class="fas fa-check me-1"></i>Approuver
                                                            </button>
                                                        </form>
                                                    @else
                                                        <button class="btn btn-success btn-sm" disabled title="Communauté complète">
                                                            <i class="fas fa-check me-1"></i>Approuver
                                                        </button>
                                                    @endif
                                                    
                                                    <form action="{{ route('organizer.membership.reject', $request) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        <button type="submit" 
                                                                class="btn btn-danger btn-sm"
                                                                onclick="return confirm('Êtes-vous sûr de vouloir rejeter cette demande ?')">
                                                            <i class="fas fa-times me-1"></i>Rejeter
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    @if($pendingRequests->hasPages())
                        <div class="card-footer">
                            {{ $pendingRequests->links() }}
                        </div>
                    @endif
                </div>
            @else
                <!-- Aucune demande -->
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucune demande en attente</h4>
                        <p class="text-muted mb-4">
                            Il n'y a actuellement aucune demande d'adhésion en attente pour vos communautés.
                        </p>
                        <a href="{{ route('organizer.communities.index') }}" class="btn btn-primary">
                            <i class="fas fa-users me-2"></i>Voir mes communautés
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle {
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

.table td {
    vertical-align: middle;
}

.btn-group .btn {
    margin-right: 5px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>
@endsection
