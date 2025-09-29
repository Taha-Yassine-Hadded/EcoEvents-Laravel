@extends('layouts.admin')

@section('title', 'Gestion des Sponsors - Echofy Admin')
@section('page-title', 'Gestion des Sponsors')

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-users fa-2x text-primary mb-3"></i>
            <h3 class="text-primary">{{ $sponsors->total() }}</h3>
            <p class="text-muted mb-0">Total Sponsors</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-user-check fa-2x text-success mb-3"></i>
            <h3 class="text-success">{{ $sponsors->where('status', 'approved')->count() }}</h3>
            <p class="text-muted mb-0">Approuvés</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-user-clock fa-2x text-warning mb-3"></i>
            <h3 class="text-warning">{{ $sponsors->where('status', 'pending')->count() }}</h3>
            <p class="text-muted mb-0">En Attente</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-user-times fa-2x text-danger mb-3"></i>
            <h3 class="text-danger">{{ $sponsors->where('status', 'rejected')->count() }}</h3>
            <p class="text-muted mb-0">Rejetés</p>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Nom, email, entreprise..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-select">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En Attente</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approuvé</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeté</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date Début</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date Fin</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary btn-admin me-2">
                        <i class="fas fa-search"></i> Filtrer
                    </button>
                    <a href="{{ route('admin.sponsors.index') }}" class="btn btn-outline-secondary btn-admin">
                        <i class="fas fa-times"></i> Reset
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Liste des Sponsors -->
<div class="row">
    <div class="col-12">
        <div class="table-admin">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>Sponsor</th>
                            <th>Entreprise</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Date Inscription</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sponsors as $sponsor)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($sponsor->profile_image)
                                            <img src="{{ asset('storage/' . $sponsor->profile_image) }}" 
                                                 class="rounded-circle me-3" width="40" height="40" alt="Avatar">
                                        @else
                                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <h6 class="mb-0">{{ $sponsor->name }}</h6>
                                            <small class="text-muted">{{ $sponsor->phone ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $sponsor->company_name ?? 'N/A' }}</strong>
                                    @if($sponsor->website)
                                        <br><small class="text-muted">{{ $sponsor->website }}</small>
                                    @endif
                                </td>
                                <td>{{ $sponsor->email }}</td>
                                <td>
                                    @php
                                        $statusClass = '';
                                        $statusText = '';
                                        switch($sponsor->status) {
                                            case 'pending': $statusClass = 'bg-warning text-dark'; $statusText = 'En Attente'; break;
                                            case 'approved': $statusClass = 'bg-success'; $statusText = 'Approuvé'; break;
                                            case 'rejected': $statusClass = 'bg-danger'; $statusText = 'Rejeté'; break;
                                            case 'active': $statusClass = 'bg-primary'; $statusText = 'Actif'; break;
                                            case 'inactive': $statusClass = 'bg-secondary'; $statusText = 'Inactif'; break;
                                            default: $statusClass = 'bg-light text-dark'; $statusText = 'Inconnu'; break;
                                        }
                                    @endphp
                                    <span class="badge badge-status {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($sponsor->created_at)->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.sponsors.show', $sponsor->id) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($sponsor->status === 'pending')
                                            <button class="btn btn-outline-success btn-sm" 
                                                    onclick="approveSponsor({{ $sponsor->id }})">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" 
                                                    onclick="rejectSponsor({{ $sponsor->id }})">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        <button class="btn btn-outline-{{ $sponsor->status === 'active' ? 'warning' : 'success' }} btn-sm" 
                                                onclick="toggleStatus({{ $sponsor->id }}, '{{ $sponsor->status }}')">
                                            <i class="fas fa-{{ $sponsor->status === 'active' ? 'pause' : 'play' }}"></i>
                                        </button>
                                        
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteSponsor({{ $sponsor->id }})">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun sponsor trouvé</h5>
                                    <p class="text-muted">Aucun sponsor ne correspond aux critères de recherche.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($sponsors->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $sponsors->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Approuver un sponsor
async function approveSponsor(id) {
    if (!confirm('Êtes-vous sûr de vouloir approuver ce sponsor ?')) return;
    
    try {
        const response = await fetch(`/admin/sponsors/${id}/approve`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.error || 'Erreur lors de l\'approbation');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion');
    }
}

// Rejeter un sponsor
async function rejectSponsor(id) {
    if (!confirm('Êtes-vous sûr de vouloir rejeter ce sponsor ?')) return;
    
    try {
        const response = await fetch(`/admin/sponsors/${id}/reject`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.error || 'Erreur lors du rejet');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion');
    }
}

// Activer/Désactiver un sponsor
async function toggleStatus(id, currentStatus) {
    const action = currentStatus === 'active' ? 'désactiver' : 'activer';
    if (!confirm(`Êtes-vous sûr de vouloir ${action} ce sponsor ?`)) return;
    
    try {
        const response = await fetch(`/admin/sponsors/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.error || 'Erreur lors du changement de statut');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion');
    }
}

// Supprimer un sponsor
async function deleteSponsor(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer définitivement ce sponsor ?\n\nCette action supprimera :\n• Le compte sponsor\n• Tous ses sponsorships\n• Ses fichiers (logo, image de profil)')) return;
    
    try {
        const response = await fetch(`/admin/sponsors/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showAlert('danger', data.error || 'Erreur lors de la suppression');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion');
    }
}
</script>
@endpush
@endsection
