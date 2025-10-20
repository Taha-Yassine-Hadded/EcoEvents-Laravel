@extends('layouts.admin')

@section('title', 'Gestion des Packages - Echofy Admin')
@section('page-title', 'Gestion des Packages')

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-box fa-2x text-primary mb-3"></i>
            <h3 class="text-primary">{{ $packages->total() }}</h3>
            <p class="text-muted mb-0">Total Packages</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
            <h3 class="text-success">{{ $packages->where('is_active', true)->count() }}</h3>
            <p class="text-muted mb-0">Packages Actifs</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-star fa-2x text-warning mb-3"></i>
            <h3 class="text-warning">{{ $packages->where('is_featured', true)->count() }}</h3>
            <p class="text-muted mb-0">Packages Mis en Avant</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-dollar-sign fa-2x text-info mb-3"></i>
            <h3 class="text-info">{{ number_format($packages->avg('price'), 0, ',', ' ') }} €</h3>
            <p class="text-muted mb-0">Prix Moyen</p>
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
                    <input type="text" name="search" class="form-control" placeholder="Nom, description..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Événement</label>
                    <select name="event_id" class="form-control">
                        <option value="">Tous les événements</option>
                        @foreach($events as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Statut</label>
                    <select name="status" class="form-control">
                        <option value="">Tous</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actifs</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactifs</option>
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                    </div>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <a href="{{ route('admin.packages.create') }}" class="btn btn-success">
                            <i class="fas fa-plus"></i> Nouveau Package
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tableau des packages -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Liste des Packages</h5>
                <div class="btn-group">
                    <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-calendar"></i> Voir les Événements
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Package</th>
                            <th>Événement</th>
                            <th>Prix</th>
                            <th>Bénéfices</th>
                            <th>Statut</th>
                            <th>Sponsorships</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                            <tr>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $package->name }}</h6>
                                        @if($package->description)
                                            <small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                        @endif
                                        @if($package->is_featured)
                                            <br><span class="badge bg-warning"><i class="fas fa-star"></i> Mis en avant</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-0">{{ $package->event->title ?? 'Aucun événement' }}</h6>
                                        @if($package->event)
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($package->event->date)->format('d/m/Y') }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <strong class="text-success">{{ number_format($package->price, 0, ',', ' ') }} €</strong>
                                </td>
                                <td>
                                    @if($package->benefits && count($package->benefits) > 0)
                                        <span class="badge bg-info">{{ count($package->benefits) }} bénéfice(s)</span>
                                    @else
                                        <span class="text-muted">Aucun</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               {{ $package->is_active ? 'checked' : '' }}
                                               onchange="togglePackageStatus({{ $package->id }})">
                                        <label class="form-check-label">
                                            {{ $package->is_active ? 'Actif' : 'Inactif' }}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $package->sponsorships->count() }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.packages.show', $package->id) }}" 
                                           class="btn btn-outline-info btn-sm" title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        <a href="{{ route('admin.packages.edit', $package->id) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <button class="btn btn-outline-warning btn-sm" 
                                                onclick="duplicatePackage({{ $package->id }})"
                                                title="Dupliquer">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                        
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="deletePackage({{ $package->id }}, '{{ $package->name }}')"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-box fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun package trouvé</h5>
                                    <p class="text-muted">Commencez par créer votre premier package.</p>
                                    <a href="{{ route('admin.packages.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> Créer un Package
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($packages->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $packages->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Aperçu Sponsor -->
@if(request('event_id'))
    <div class="row mt-4">
        <div class="col-12">
            <div class="stats-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-eye text-info"></i> Aperçu Sponsor
                    </h5>
                    <small class="text-muted">Ce que voient les sponsors pour cet événement</small>
                </div>
                
                @php
                    $selectedEvent = \App\Models\Event::find(request('event_id'));
                    $sponsorPackages = $selectedEvent ? 
                        \App\Models\Package::where('event_id', $selectedEvent->id)
                            ->where('is_active', true)
                            ->orderBy('sort_order')
                            ->orderBy('price')
                            ->get() : collect();
                @endphp
                
                @if($selectedEvent && $sponsorPackages->count() > 0)
                    <div class="row">
                        @foreach($sponsorPackages as $package)
                            <div class="col-lg-4 col-md-6 mb-3">
                                <div class="card border-left-primary h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title text-primary mb-0">{{ $package->name }}</h6>
                                            <span class="badge bg-success">{{ number_format($package->price, 0) }} €</span>
                                        </div>
                                        
                                        @if($package->description)
                                            <p class="text-muted small mb-2">{{ Str::limit($package->description, 80) }}</p>
                                        @endif
                                        
                                        @if($package->benefits && count($package->benefits) > 0)
                                            <div class="mb-2">
                                                <small class="text-muted">Bénéfices ({{ count($package->benefits) }}):</small>
                                                <ul class="small mb-0">
                                                    @foreach(array_slice($package->benefits, 0, 3) as $benefit)
                                                        <li>{{ Str::limit($benefit, 50) }}</li>
                                                    @endforeach
                                                    @if(count($package->benefits) > 3)
                                                        <li class="text-muted">... et {{ count($package->benefits) - 3 }} autres</li>
                                                    @endif
                                                </ul>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                {{ $package->sponsorships->count() }} sponsorship(s)
                                            </small>
                                            @if($package->is_featured)
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-star"></i> Mis en avant
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @elseif($selectedEvent)
                    <div class="text-center py-4">
                        <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucun package actif</h6>
                        <p class="text-muted small">Les sponsors ne verront aucun package pour cet événement.</p>
                        <a href="{{ route('admin.packages.create', ['event_id' => $selectedEvent->id]) }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Créer un package
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-filter fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Sélectionnez un événement</h6>
                        <p class="text-muted small">Utilisez le filtre ci-dessus pour voir l'aperçu sponsor d'un événement spécifique.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

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
            setTimeout(() => window.location.reload(), 1500);
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
