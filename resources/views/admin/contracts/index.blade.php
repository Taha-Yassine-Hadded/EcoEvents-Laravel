@extends('layouts.admin')

@section('title', 'Gestion des Contrats - Echofy Admin')
@section('page-title', 'Gestion des Contrats')

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-file-contract fa-2x text-primary mb-3"></i>
            <h3 class="text-primary">{{ $stats['total_contracts'] }}</h3>
            <p class="text-muted mb-0">Total Contrats</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-dollar-sign fa-2x text-success mb-3"></i>
            <h3 class="text-success">{{ number_format($stats['total_amount'], 0, ',', ' ') }} €</h3>
            <p class="text-muted mb-0">Montant Total</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-calendar-alt fa-2x text-info mb-3"></i>
            <h3 class="text-info">{{ $stats['this_month'] }}</h3>
            <p class="text-muted mb-0">Ce Mois</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-users fa-2x text-warning mb-3"></i>
            <h3 class="text-warning">{{ $stats['unique_sponsors'] }}</h3>
            <p class="text-muted mb-0">Sponsors Uniques</p>
        </div>
    </div>
</div>

<!-- Filtres -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stats-card">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" placeholder="Sponsor, événement..." value="{{ request('search') }}">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label">Événement</label>
                    <select name="event_id" class="form-control">
                        <option value="">Tous les événements</option>
                        @foreach(\App\Models\Event::all() as $event)
                            <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                                {{ $event->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                
                <div class="col-md-1 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Tableau des contrats -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Liste des Contrats</h5>
                <div class="btn-group">
                    <a href="{{ route('admin.contracts.export') }}" class="btn btn-outline-success">
                        <i class="fas fa-download"></i> Exporter Tous
                    </a>
                    <a href="{{ route('admin.sponsors.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-users"></i> Voir les Sponsors
                    </a>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Sponsor</th>
                            <th>Événement</th>
                            <th>Package</th>
                            <th>Montant</th>
                            <th>Date Contrat</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contracts as $contract)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ substr($contract->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $contract->user->name }}</h6>
                                            <small class="text-muted">{{ $contract->user->company_name ?? 'Sans entreprise' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @php
                                            // Logique améliorée pour l'affichage du nom de l'événement
                                            $eventName = 'Événement non spécifié';
                                            $eventDate = null;
                                            
                                            // Priorité 1: Relation event chargée
                                            if ($contract->event && !empty($contract->event->title)) {
                                                $eventName = $contract->event->title;
                                                $eventDate = $contract->event->date;
                                            }
                                            // Priorité 2: Champ event_title dans la table
                                            elseif (!empty($contract->event_title)) {
                                                $eventName = $contract->event_title;
                                                $eventDate = $contract->event_date;
                                            }
                                            // Priorité 3: Essayer de charger l'événement si event_id existe
                                            elseif (!empty($contract->event_id)) {
                                                try {
                                                    $event = \App\Models\Event::find($contract->event_id);
                                                    if ($event) {
                                                        $eventName = $event->title;
                                                        $eventDate = $event->date;
                                                    }
                                                } catch (Exception $e) {
                                                    // En cas d'erreur, garder le nom par défaut
                                                }
                                            }
                                        @endphp
                                        <h6 class="mb-0 text-primary">{{ $eventName }}</h6>
                                        <small class="text-muted">
                                            @if($eventDate)
                                                {{ \Carbon\Carbon::parse($eventDate)->format('d/m/Y') }}
                                            @else
                                                Date non spécifiée
                                            @endif
                                        </small>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $contract->package_name }}</span>
                                </td>
                                <td>
                                    <strong class="text-success">{{ number_format($contract->amount, 0, ',', ' ') }} €</strong>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($contract->created_at)->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-success">Approuvé</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-primary btn-sm" 
                                                onclick="viewContract({{ $contract->id }})"
                                                title="Voir le contrat">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <a href="{{ route('admin.contracts.download', $contract->id) }}" 
                                           class="btn btn-outline-success btn-sm" 
                                           title="Télécharger">
                                            <i class="fas fa-download"></i>
                                        </a>
                                        
                                        <button class="btn btn-outline-warning btn-sm" 
                                                onclick="regenerateContract({{ $contract->id }})"
                                                title="Régénérer">
                                            <i class="fas fa-sync"></i>
                                        </button>
                                        
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteContract({{ $contract->id }})"
                                                title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun contrat trouvé</h5>
                                    <p class="text-muted">Il n'y a actuellement aucun contrat généré.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($contracts->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $contracts->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal pour voir le contrat -->
<div class="modal fade" id="contractModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Contrat de Sponsoring</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="contractContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-primary" onclick="downloadCurrentContract()">
                    <i class="fas fa-download"></i> Télécharger
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let currentContractId = null;

// Voir un contrat
async function viewContract(id) {
    currentContractId = id;
    try {
        const response = await fetch(`/admin/contracts/${id}/view`);
        const content = await response.text();
        
        document.getElementById('contractContent').innerHTML = content;
        const modal = new bootstrap.Modal(document.getElementById('contractModal'));
        modal.show();
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors du chargement du contrat');
    }
}

// Télécharger le contrat actuellement affiché
function downloadCurrentContract() {
    if (currentContractId) {
        window.open(`/admin/contracts/${currentContractId}/download`, '_blank');
    }
}

// Régénérer un contrat
async function regenerateContract(id) {
    if (!confirm('Êtes-vous sûr de vouloir régénérer ce contrat ?')) return;
    
    try {
        const response = await fetch(`/admin/contracts/${id}/regenerate`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showAlert('danger', data.error || 'Erreur lors de la régénération');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
}

// Supprimer un contrat
async function deleteContract(id) {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce contrat ? Cette action est irréversible.')) return;
    
    try {
        const response = await fetch(`/admin/contracts/${id}/delete`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
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
