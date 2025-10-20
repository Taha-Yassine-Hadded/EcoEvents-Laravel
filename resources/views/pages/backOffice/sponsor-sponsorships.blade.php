@extends('layouts.sponsor')

@section('title', 'Mes Sponsoring Acceptés - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-check-circle text-success"></i>
        Mes Sponsoring Acceptés
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Sponsorships</li>
        </ol>
    </nav>
</div>

<!-- Description -->
<div class="alert alert-info">
    <i class="fas fa-info-circle"></i>
    <strong>Sponsoring acceptés uniquement :</strong> Cette page affiche uniquement vos propositions de sponsoring qui ont été approuvées par l'administration. 
    <a href="{{ route('sponsor.all-sponsorships') }}" class="alert-link">Voir toutes mes propositions (en attente, rejetées, etc.)</a>
</div>

@if($sponsorships->count() > 0)
    <!-- Sponsorships List -->
    <div class="row">
        @foreach($sponsorships as $sponsorship)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            @php
                                // Logique améliorée pour l'affichage du nom de l'événement
                                $eventName = 'Événement non spécifié';
                                
                                // Priorité 1: Relation event chargée
                                if ($sponsorship->event && !empty($sponsorship->event->title)) {
                                    $eventName = $sponsorship->event->title;
                                }
                                // Priorité 2: Champ event_title dans la table
                                elseif (!empty($sponsorship->event_title)) {
                                    $eventName = $sponsorship->event_title;
                                }
                                // Priorité 3: Essayer de charger l'événement si event_id existe
                                elseif (!empty($sponsorship->event_id)) {
                                    try {
                                        $event = \App\Models\Event::find($sponsorship->event_id);
                                        if ($event) {
                                            $eventName = $event->title;
                                        }
                                    } catch (Exception $e) {
                                        // En cas d'erreur, garder le nom par défaut
                                    }
                                }
                            @endphp
                            {{ $eventName }}
                        </h6>
                        <span class="badge badge-{{ 
                            $sponsorship->status === 'approved' ? 'success' : 
                            ($sponsorship->status === 'pending' ? 'warning' : 
                            ($sponsorship->status === 'rejected' ? 'danger' : 
                            ($sponsorship->status === 'completed' ? 'info' : 'secondary'))) 
                        }}">
                            {{ ucfirst($sponsorship->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="sponsorship-info mb-3">
                            <div class="info-row mb-2">
                                <strong><i class="fas fa-gift text-success"></i> Package :</strong>
                                <span class="ms-2">{{ $sponsorship->package_name }}</span>
                            </div>
                            <div class="info-row mb-2">
                                <strong><i class="fas fa-coins text-warning"></i> Montant :</strong>
                                <span class="ms-2">{{ number_format($sponsorship->amount, 0) }} TND</span>
                            </div>
                            <div class="info-row mb-2">
                                <strong><i class="fas fa-calendar text-info"></i> Date de proposition :</strong>
                                <span class="ms-2">{{ $sponsorship->created_at->format('d/m/Y') }}</span>
                            </div>
                            @if($sponsorship->start_date)
                                <div class="info-row mb-2">
                                    <strong><i class="fas fa-play text-success"></i> Début :</strong>
                                    <span class="ms-2">{{ \Carbon\Carbon::parse($sponsorship->start_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                            @if($sponsorship->end_date)
                                <div class="info-row mb-2">
                                    <strong><i class="fas fa-stop text-danger"></i> Fin :</strong>
                                    <span class="ms-2">{{ \Carbon\Carbon::parse($sponsorship->end_date)->format('d/m/Y') }}</span>
                                </div>
                            @endif
                        </div>
                        
                        @if($sponsorship->notes)
                            <div class="notes-section mb-3">
                                <strong><i class="fas fa-sticky-note text-secondary"></i> Notes :</strong>
                                <div class="mt-1 p-2 bg-light rounded small">
                                    {{ $sponsorship->notes }}
                                </div>
                            </div>
                        @endif
                        
                        <div class="event-preview mb-3">
                            <strong><i class="fas fa-info-circle text-primary"></i> Événement :</strong>
                            <div class="mt-1 p-2 bg-light rounded small">
                                {{ Str::limit($sponsorship->event_description ?? ($sponsorship->event->description ?? 'Aucune description disponible'), 100) }}
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('sponsor.campaign.details', $sponsorship->event->id ?? '#') }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Voir l'Événement
                            </a>
                            
                            @if(in_array($sponsorship->status, ['pending', 'approved']))
                                <button class="btn btn-outline-danger btn-sm" 
                                        onclick="cancelSponsorship({{ $sponsorship->id }})">
                                    <i class="fas fa-trash-alt"></i> Supprimer
                                </button>
                            @endif
                            
                            @if($sponsorship->contract_pdf)
                                <a href="{{ asset('storage/' . $sponsorship->contract_pdf) }}" 
                                   target="_blank" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-file-pdf"></i> Contrat
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($sponsorships->hasPages())
        <div class="row">
            <div class="col-12">
                <nav aria-label="Pagination des sponsorships">
                    {{ $sponsorships->links() }}
                </nav>
            </div>
        </div>
    @endif
@else
    <!-- Empty State -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-handshake fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Aucun sponsorship pour le moment</h4>
                    <p class="text-muted">Vous n'avez pas encore de sponsoring accepté. Explorez les événements disponibles et proposez votre soutien !</p>
                    <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Explorer les Événements
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Cancel Sponsorship Modal -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">
                    <i class="fas fa-trash-alt text-danger"></i> Supprimer le Sponsorship
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Attention !</strong> Vous êtes sur le point de supprimer définitivement ce sponsorship.
                </div>
                <p><strong>Cette action va :</strong></p>
                <ul>
                    <li>Supprimer complètement le sponsorship de votre liste</li>
                    <li>Supprimer toutes les informations de la campagne associée</li>
                    <li>Cette action est <strong>irréversible</strong></li>
                </ul>
                <p class="text-danger"><strong>Êtes-vous absolument sûr de vouloir continuer ?</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times"></i> Non, garder
                </button>
                <button type="button" class="btn btn-danger" id="confirmCancel">
                    <i class="fas fa-trash-alt"></i> Oui, supprimer définitivement
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let sponsorshipToCancel = null;

function cancelSponsorship(sponsorshipId) {
    sponsorshipToCancel = sponsorshipId;
    const modal = new bootstrap.Modal(document.getElementById('cancelModal'));
    modal.show();
}

function showAlert(type, message) {
    const alertContainer = document.createElement('div');
    alertContainer.className = `alert alert-${type} alert-dismissible fade show mt-3`;
    alertContainer.setAttribute('role', 'alert');
    alertContainer.innerHTML = `
        ${message.replace(/\n/g, '<br>')}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    document.querySelector('.content-header').after(alertContainer);
    setTimeout(() => alertContainer.remove(), 5000);
}

// Attendre que le DOM soit chargé avant d'attacher les événements
document.addEventListener('DOMContentLoaded', function() {
    const confirmCancelBtn = document.getElementById('confirmCancel');
    if (confirmCancelBtn) {
        confirmCancelBtn.addEventListener('click', async function() {
            if (!sponsorshipToCancel) return;
            
            const submitBtn = this;
            const originalText = submitBtn.innerHTML;
            
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
            submitBtn.disabled = true;
            
            try {
                // Récupérer le token CSRF de manière sécurisée
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                                 document.querySelector('input[name="_token"]')?.value ||
                                 '{{ csrf_token() }}';

                const response = await fetch(`/sponsor/sponsorships/${sponsorshipToCancel}/cancel`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    showAlert('success', data.message);
                    const modal = bootstrap.Modal.getInstance(document.getElementById('cancelModal'));
                    modal.hide();
                    // Rechargement immédiat
                    window.location.reload();
                } else {
                    showAlert('danger', data.error || 'Erreur lors de la suppression du sponsorship');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showAlert('danger', 'Erreur de connexion');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                sponsorshipToCancel = null;
            }
        });
    }
});
</script>
@endpush
@endsection
