@extends('layouts.admin')

@section('title', 'Sponsorships Approuvés - Echofy Admin')
@section('page-title', 'Sponsorships Approuvés')

@section('content')
<div class="row">
    <!-- Statistiques -->
    <div class="col-lg-3 col-md-6 mb-4">
        <div class="stats-card text-center">
            <i class="fas fa-check-circle fa-2x text-success mb-3"></i>
            <h3 class="text-success">{{ $sponsorships->total() }}</h3>
            <p class="text-muted mb-0">Sponsorships Approuvés</p>
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
            <i class="fas fa-file-contract fa-2x text-primary mb-3"></i>
            <h3 class="text-primary">{{ $sponsorships->where('contract_pdf', '!=', null)->count() }}</h3>
            <p class="text-muted mb-0">Contrats Générés</p>
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
                    <label class="form-label">Date de début</label>
                    <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Date de fin</label>
                    <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Filtrer</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Table des sponsorships approuvés -->
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="mb-0">Sponsorships Approuvés</h5>
                <div class="btn-group" role="group">
                    <a href="{{ route('admin.sponsors.pending-sponsorships') }}" class="btn btn-outline-warning">
                        <i class="fas fa-clock"></i> En Attente
                    </a>
                    <a href="{{ route('admin.sponsors.approved-sponsorships') }}" class="btn btn-success">
                        <i class="fas fa-check"></i> Approuvés
                    </a>
                </div>
            </div>
            
            @if($sponsorships->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Sponsor</th>
                                <th>Événement</th>
                                <th>Package</th>
                                <th>Montant</th>
                                <th>Date d'Approbation</th>
                                <th>Contrat</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sponsorships as $sponsorship)
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
                                        <div class="fw-bold">{{ $eventName }}</div>
                                        <small class="text-muted">
                                            @if($eventDate)
                                                {{ \Carbon\Carbon::parse($eventDate)->format('d/m/Y à H:i') }}
                                            @else
                                                Date non spécifiée
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $sponsorship->package_name ?? 'Package non spécifié' }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-success">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</div>
                                    </td>
                                    <td>
                                        <small>{{ \Carbon\Carbon::parse($sponsorship->updated_at)->format('d/m/Y à H:i') }}</small>
                                    </td>
                                    <td>
                                        @if($sponsorship->contract_pdf)
                                            <span class="badge bg-success">
                                                <i class="fas fa-file-pdf"></i> Généré
                                            </span>
                                        @else
                                            <span class="badge bg-warning">
                                                <i class="fas fa-exclamation-triangle"></i> Non généré
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            @if($sponsorship->contract_pdf)
                                                <a href="{{ route('contracts.sponsorship.download', $sponsorship->id) }}" 
                                                   class="btn btn-sm btn-outline-primary" 
                                                   title="Télécharger le contrat">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ route('contracts.sponsorship.view', $sponsorship->id) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   target="_blank"
                                                   title="Voir le contrat">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary" disabled title="Contrat non généré">
                                                    <i class="fas fa-file-pdf"></i>
                                                </button>
                                            @endif
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-success" 
                                                    onclick="completeSponsorship({{ $sponsorship->id }})"
                                                    title="Marquer comme terminé">
                                                <i class="fas fa-check-double"></i>
                                            </button>
                                            
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-info" 
                                                    onclick="viewSponsorshipDetails({{ $sponsorship->id }})"
                                                    title="Voir les détails">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $sponsorships->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h5>Aucun sponsorship approuvé</h5>
                    <p class="text-muted">Il n'y a actuellement aucun sponsorship approuvé.</p>
                    <a href="{{ route('admin.sponsors.pending-sponsorships') }}" class="btn btn-primary">
                        <i class="fas fa-clock"></i> Voir les propositions en attente
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal pour les détails -->
<div class="modal fade" id="sponsorshipDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Détails du Sponsorship</h5>
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
// Marquer un sponsorship comme terminé
async function completeSponsorship(id) {
    if (!confirm('Êtes-vous sûr de vouloir marquer ce sponsorship comme terminé ?')) return;
    
    try {
        // Récupérer le token CSRF
        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (!csrfToken) {
            showAlert('danger', 'Token de sécurité manquant');
            return;
        }
        
        const response = await fetch(`/admin/sponsors/sponsorships/${id}/complete`, {
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
            showAlert('danger', data.error || 'Erreur lors de la finalisation');
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
}

// Voir les détails d'un sponsorship
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
