@extends('layouts.admin')

@section('title', 'Propositions de Sponsoring en Attente - Echofy Admin')
@section('page-title', 'Propositions de Sponsoring en Attente')

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-handshake fa-2x text-warning mb-3"></i>
            <h3 class="text-warning">{{ $sponsorships->total() }}</h3>
            <p class="text-muted mb-0">Propositions en Attente</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-dollar-sign fa-2x text-success mb-3"></i>
            <h3 class="text-success">{{ number_format($sponsorships->sum('amount'), 0, ',', ' ') }} €</h3>
            <p class="text-muted mb-0">Montant Total</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-calendar-alt fa-2x text-info mb-3"></i>
            <h3 class="text-info">{{ $sponsorships->where('created_at', '>=', now()->subDays(7))->count() }}</h3>
            <p class="text-muted mb-0">Cette Semaine</p>
        </div>
    </div>
    
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-users fa-2x text-primary mb-3"></i>
            <h3 class="text-primary">{{ $sponsorships->unique('user_id')->count() }}</h3>
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

<!-- Tableau des propositions -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Liste des Propositions</h5>
                <div class="btn-group">
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
                            <th>Date</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($sponsorships as $sponsorship)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ substr($sponsorship->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $sponsorship->user->name }}</h6>
                                            <small class="text-muted">{{ $sponsorship->user->company_name ?? 'Sans entreprise' }}</small>
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
                                            if ($sponsorship->event && !empty($sponsorship->event->title)) {
                                                $eventName = $sponsorship->event->title;
                                                $eventDate = $sponsorship->event->date;
                                            }
                                            // Priorité 2: Champ event_title dans la table
                                            elseif (!empty($sponsorship->event_title)) {
                                                $eventName = $sponsorship->event_title;
                                                $eventDate = $sponsorship->event_date;
                                            }
                                            // Priorité 3: Essayer de charger l'événement si event_id existe
                                            elseif (!empty($sponsorship->event_id)) {
                                                try {
                                                    $event = \App\Models\Event::find($sponsorship->event_id);
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
                                    <span class="badge bg-info">{{ $sponsorship->package_name }}</span>
                                </td>
                                <td>
                                    <strong class="text-success">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</strong>
                                </td>
                                <td>
                                    <small>{{ \Carbon\Carbon::parse($sponsorship->created_at)->format('d/m/Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-warning">En Attente</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button class="btn btn-outline-success btn-sm" 
                                                onclick="approveSponsorship({{ $sponsorship->id }})"
                                                title="Approuver">
                                            <i class="fas fa-check"></i>
                                        </button>
                                        
                                        <button class="btn btn-outline-danger btn-sm" 
                                                onclick="rejectSponsorship({{ $sponsorship->id }})"
                                                title="Rejeter">
                                            <i class="fas fa-times"></i>
                                        </button>
                                        
                                        <button class="btn btn-outline-info btn-sm" 
                                                onclick="viewSponsorshipDetails({{ $sponsorship->id }})"
                                                title="Voir détails">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        @if($sponsorship->status === 'approved')
                                            <a href="{{ route('admin.sponsors.contract.view', $sponsorship->id) }}" 
                                               class="btn btn-outline-success btn-sm" 
                                               title="Voir le contrat" 
                                               target="_blank">
                                                <i class="fas fa-file-contract"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-handshake fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucune proposition en attente</h5>
                                    <p class="text-muted">Il n'y a actuellement aucune proposition de sponsoring en attente d'approbation.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Pagination -->
        @if($sponsorships->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $sponsorships->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal pour voir les détails -->
<div class="modal fade" id="sponsorshipDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails de la Proposition</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sponsorshipDetailsContent">
                <!-- Contenu chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Approuver une proposition
async function approveSponsorship(id) {
    if (!confirm('Êtes-vous sûr de vouloir approuver cette proposition de sponsoring ?')) return;
    
    try {
        // Récupérer le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('danger', 'Token de sécurité manquant');
            return;
        }
        
        const response = await fetch(`/admin/sponsors/sponsorships/${id}/approve`, {
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
            if (data.contract_generated) {
                let contractMessage = 'Contrat généré ! ';
                if (data.contract_download_url) {
                    contractMessage += '<a href="' + data.contract_download_url + '" target="_blank" class="alert-link">Télécharger le contrat</a>';
                }
                if (data.contract_view_url) {
                    contractMessage += ' | <a href="' + data.contract_view_url + '" target="_blank" class="alert-link">Voir le contrat</a>';
                }
                showAlert('info', contractMessage);
            }
            setTimeout(() => window.location.reload(), 2000);
        } else {
            showAlert('danger', data.error || 'Erreur lors de l\'approbation');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
}

// Rejeter une proposition
async function rejectSponsorship(id) {
    if (!confirm('Êtes-vous sûr de vouloir rejeter cette proposition de sponsoring ?')) return;
    
    try {
        // Récupérer le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('danger', 'Token de sécurité manquant');
            return;
        }
        
        const response = await fetch(`/admin/sponsors/sponsorships/${id}/reject`, {
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
            showAlert('danger', data.error || 'Erreur lors du rejet');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
}

// Voir les détails d'une proposition
async function viewSponsorshipDetails(id) {
    // Pour l'instant, on affiche juste un message
    // Plus tard, on pourrait charger les détails via AJAX
    showAlert('info', 'Fonctionnalité en cours de développement');
}

// Fonction pour afficher les alertes
function showAlert(type, message) {
    // Créer un conteneur d'alertes s'il n'existe pas
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
    
    // Auto-hide après 5 secondes
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
