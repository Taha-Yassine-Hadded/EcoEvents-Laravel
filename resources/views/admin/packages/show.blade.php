@extends('layouts.admin')

@section('title', 'Détails du Package - Echofy Admin')
@section('page-title', 'Détails du Package')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">{{ $package->name }}</h5>
                <div class="btn-group">
                    <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-edit"></i> Modifier
                    </a>
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            
            <!-- Informations générales -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="info-card">
                        <h6><i class="fas fa-tag text-primary"></i> Informations générales</h6>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">Nom</small>
                                <div class="fw-bold">{{ $package->name }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Prix</small>
                                <div class="fw-bold text-success">{{ number_format($package->price, 0, ',', ' ') }} €</div>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-6">
                                <small class="text-muted">Ordre d'affichage</small>
                                <div class="fw-bold">{{ $package->sort_order }}</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">Statut</small>
                                <div>
                                    <span class="badge bg-{{ $package->is_active ? 'success' : 'secondary' }}">
                                        {{ $package->is_active ? 'Actif' : 'Inactif' }}
                                    </span>
                                    @if($package->is_featured)
                                        <span class="badge bg-warning ms-1">
                                            <i class="fas fa-star"></i> Mis en avant
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="info-card">
                        <h6><i class="fas fa-calendar text-info"></i> Événement associé</h6>
                        @if($package->event)
                            <div class="mb-2">
                                <small class="text-muted">Titre</small>
                                <div class="fw-bold">{{ $package->event->title }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Date</small>
                                <div>{{ \Carbon\Carbon::parse($package->event->date)->format('d/m/Y à H:i') }}</div>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Lieu</small>
                                <div>{{ $package->event->location }}</div>
                            </div>
                            <div>
                                <small class="text-muted">Statut</small>
                                <div>
                                    <span class="badge bg-{{ $package->event->status === 'upcoming' ? 'primary' : ($package->event->status === 'ongoing' ? 'success' : 'secondary') }}">
                                        {{ ucfirst($package->event->status) }}
                                    </span>
                                </div>
                            </div>
                        @else
                            <div class="text-muted">Aucun événement associé</div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            @if($package->description)
                <div class="mb-4">
                    <h6><i class="fas fa-align-left text-secondary"></i> Description</h6>
                    <div class="info-card">
                        <p class="mb-0">{{ $package->description }}</p>
                    </div>
                </div>
            @endif
            
            <!-- Bénéfices -->
            @if($package->benefits && count($package->benefits) > 0)
                <div class="mb-4">
                    <h6><i class="fas fa-gift text-success"></i> Bénéfices du Package</h6>
                    <div class="info-card">
                        <ul class="list-unstyled mb-0">
                            @foreach($package->benefits as $benefit)
                                <li class="mb-2">
                                    <i class="fas fa-check text-success me-2"></i>
                                    {{ $benefit }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            
            <!-- Sponsorships -->
            <div class="mb-4">
                <h6><i class="fas fa-handshake text-warning"></i> Sponsorships associés ({{ $package->sponsorships->count() }})</h6>
                @if($package->sponsorships->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Sponsor</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($package->sponsorships as $sponsorship)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                    {{ substr($sponsorship->user->name ?? 'S', 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold">{{ $sponsorship->user->name ?? 'Non spécifié' }}</div>
                                                    <small class="text-muted">{{ $sponsorship->user->company_name ?? 'Entreprise non spécifiée' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <strong class="text-success">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ 
                                                $sponsorship->status === 'approved' ? 'success' : 
                                                ($sponsorship->status === 'pending' ? 'warning' : 
                                                ($sponsorship->status === 'rejected' ? 'danger' : 'secondary')) 
                                            }}">
                                                {{ ucfirst($sponsorship->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <small>{{ \Carbon\Carbon::parse($sponsorship->created_at)->format('d/m/Y') }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="info-card text-center py-4">
                        <i class="fas fa-handshake fa-2x text-muted mb-2"></i>
                        <p class="text-muted mb-0">Aucun sponsorship associé à ce package</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card">
            <h6 class="card-title">Statistiques</h6>
            <div class="row mb-3">
                <div class="col-6">
                    <small class="text-muted">Sponsorships totaux</small>
                    <div class="fw-bold h5">{{ $package->sponsorships->count() }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Sponsorships approuvés</small>
                    <div class="fw-bold h5 text-success">{{ $package->sponsorships->where('status', 'approved')->count() }}</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-6">
                    <small class="text-muted">En attente</small>
                    <div class="fw-bold h5 text-warning">{{ $package->sponsorships->where('status', 'pending')->count() }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Rejetés</small>
                    <div class="fw-bold h5 text-danger">{{ $package->sponsorships->where('status', 'rejected')->count() }}</div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-12">
                    <small class="text-muted">Revenus potentiels</small>
                    <div class="fw-bold h5 text-success">
                        {{ number_format($package->sponsorships->where('status', 'approved')->sum('amount'), 0, ',', ' ') }} €
                    </div>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <h6 class="card-title">Informations techniques</h6>
            <div class="row mb-2">
                <div class="col-6">
                    <small class="text-muted">ID</small>
                    <div class="fw-bold">{{ $package->id }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Bénéfices</small>
                    <div class="fw-bold">{{ $package->benefits ? count($package->benefits) : 0 }}</div>
                </div>
            </div>
            
            <div class="row mb-2">
                <div class="col-6">
                    <small class="text-muted">Créé le</small>
                    <div>{{ \Carbon\Carbon::parse($package->created_at)->format('d/m/Y') }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted">Modifié le</small>
                    <div>{{ \Carbon\Carbon::parse($package->updated_at)->format('d/m/Y') }}</div>
                </div>
            </div>
        </div>
        
        <div class="stats-card">
            <h6 class="card-title">Actions rapides</h6>
            <div class="d-grid gap-2">
                <a href="{{ route('admin.packages.edit', $package->id) }}" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Modifier
                </a>
                
                <button class="btn btn-outline-warning" onclick="duplicatePackage({{ $package->id }})">
                    <i class="fas fa-copy"></i> Dupliquer
                </button>
                
                @if($package->is_active)
                    <button class="btn btn-outline-secondary" onclick="togglePackageStatus({{ $package->id }})">
                        <i class="fas fa-pause"></i> Désactiver
                    </button>
                @else
                    <button class="btn btn-outline-success" onclick="togglePackageStatus({{ $package->id }})">
                        <i class="fas fa-play"></i> Activer
                    </button>
                @endif
                
                <button class="btn btn-outline-danger" onclick="deletePackage({{ $package->id }}, '{{ $package->name }}')">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Toggle le statut d'un package
async function togglePackageStatus(id) {
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('danger', 'Token de sécurité manquant');
            return;
        }
        
        const response = await fetch(`/admin/packages/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
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
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
}

// Dupliquer un package
async function duplicatePackage(id) {
    if (!confirm('Êtes-vous sûr de vouloir dupliquer ce package ?')) return;
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('danger', 'Token de sécurité manquant');
            return;
        }
        
        const response = await fetch(`/admin/packages/${id}/duplicate`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1500);
            }
        } else {
            showAlert('danger', data.error || 'Erreur lors de la duplication');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
}

// Supprimer un package
async function deletePackage(id, name) {
    if (!confirm(`Êtes-vous sûr de vouloir supprimer le package "${name}" ?\n\nCette action est irréversible.`)) return;
    
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('danger', 'Token de sécurité manquant');
            return;
        }
        
        const response = await fetch(`/admin/packages/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            setTimeout(() => window.location.href = '{{ route("admin.packages.index") }}', 1500);
        } else {
            showAlert('danger', data.error || 'Erreur lors de la suppression');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
}

// Fonction pour afficher les alertes
function showAlert(type, message) {
    let alertContainer = document.querySelector('.alert-container');
    if (!alertContainer) {
        alertContainer = document.createElement('div');
        alertContainer.className = 'alert-container';
        alertContainer.style.position = 'fixed';
        alertContainer.style.top = '20px';
        alertContainer.style.right = '20px';
        alertContainer.style.zIndex = '9999';
        alertContainer.style.maxWidth = '400px';
        document.body.appendChild(alertContainer);
    }
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show mb-2`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    alertContainer.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.classList.remove('show');
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.remove();
                }
            }, 150);
        }
    }, 5000);
}
</script>
@endpush
@endsection
