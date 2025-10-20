@extends('layouts.sponsor')

@section('title', 'Mes Campagnes - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-calendar-alt text-primary"></i>
        Campagnes Disponibles
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Campagnes</li>
        </ol>
    </nav>
</div>

<!-- Content -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Toutes les Campagnes
                </h5>
            </div>
            <div class="card-body">
                @php
                    // Fallback: utiliser $events ou $campaigns selon ce qui est disponible
                    $displayData = isset($events) ? $events : (isset($campaigns) ? $campaigns : collect());
                    
                @endphp
                
                
                @if($displayData && $displayData->count() > 0)
                    <div class="row">
                        @foreach($displayData as $event)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 campaign-card">
                                    @if($event->img)
                                        <img src="{{ asset('storage/' . $event->img) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $event->title }}</h5>
                                        <p class="card-text">{{ Str::limit($event->description, 100) }}</p>
                                        <div class="campaign-meta">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> {{ $event->date ? $event->date->format('d/m/Y H:i') : 'Date non définie' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-map-marker-alt"></i> {{ $event->location ?? 'Lieu non spécifié' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-tag"></i> {{ $event->category->name ?? 'Non spécifiée' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-users"></i> {{ $event->capacity ?? 'Illimité' }} places
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-circle text-{{ $event->status === 'upcoming' ? 'primary' : ($event->status === 'ongoing' ? 'success' : 'secondary') }}"></i> 
                                                {{ ucfirst($event->status) }}
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="#" class="btn btn-primary btn-sm" onclick="showEventDetails({{ $event->id }})">
                                            <i class="fas fa-eye"></i> Détails
                                        </a>
                                        <a href="#" class="btn btn-success btn-sm" onclick="showSponsorshipModal({{ $event->id }})">
                                            <i class="fas fa-handshake"></i> Sponsoriser
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if(method_exists($displayData, 'hasPages') && $displayData->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <nav aria-label="Pagination des événements">
                                    {{ $displayData->links() }}
                                </nav>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">Aucun événement disponible</h3>
                        <p class="text-muted">Les événements seront bientôt disponibles pour le sponsoring.</p>
                        <a href="{{ route('sponsor.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Retour au Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

<!-- Modal pour les détails de l'événement -->
<div class="modal fade" id="eventDetailsModal" tabindex="-1" aria-labelledby="eventDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventDetailsModalLabel">Détails de l'Événement</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="eventDetailsContent">
                <!-- Le contenu sera chargé dynamiquement -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-success" onclick="showSponsorshipModal(currentEventId)">
                    <i class="fas fa-handshake"></i> Sponsoriser cet événement
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal pour le sponsoring amélioré -->
<div class="modal fade" id="sponsorshipModal" tabindex="-1" aria-labelledby="sponsorshipModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white">
                <h5 class="modal-title" id="sponsorshipModalLabel">
                    <i class="fas fa-handshake me-2"></i>Proposer un Sponsoring
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Informations de l'événement -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="alert alert-info d-flex align-items-center">
                            <i class="fas fa-info-circle me-2"></i>
                            <div>
                                <strong>Événement sélectionné :</strong>
                                <span id="selected-event-title">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form id="sponsorshipForm">
                    @csrf
                    <input type="hidden" id="event_id" name="event_id">
                    
                    <!-- Sélection du package -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3">
                            <i class="fas fa-gift text-primary me-2"></i>Choisissez votre package de sponsoring :
                        </h6>
                        <div class="row">
                            @if(isset($packages) && $packages->count() > 0)
                                @foreach($packages as $package)
                                    <div class="col-lg-4 col-md-6 mb-3">
                                        <div class="card package-card h-100" data-package-id="{{ $package->id }}" data-package-price="{{ $package->price }}" onclick="selectPackage({{ $package->id }}, {{ $package->price }}, '{{ $package->name }}')">
                                            <div class="card-header text-center bg-light">
                                                <div class="package-icon mb-2">
                                                    @if(strtolower($package->name) === 'bronze')
                                                        <i class="fas fa-medal fa-2x text-warning"></i>
                                                    @elseif(strtolower($package->name) === 'silver')
                                                        <i class="fas fa-medal fa-2x text-secondary"></i>
                                                    @elseif(strtolower($package->name) === 'gold')
                                                        <i class="fas fa-crown fa-2x text-warning"></i>
                                                    @else
                                                        <i class="fas fa-star fa-2x text-primary"></i>
                                                    @endif
                                                </div>
                                                <h5 class="card-title mb-0 fw-bold">{{ $package->name }}</h5>
                                            </div>
                                            <div class="card-body text-center">
                                                <div class="price-display mb-3">
                                                    <span class="h3 text-primary fw-bold">{{ number_format($package->price, 0) }}</span>
                                                    <span class="text-muted"> TND</span>
                                                </div>
                                                <div class="benefits-list">
                                                    @if($package->benefits)
                                                        @foreach($package->benefits as $benefit)
                                                            <div class="benefit-item mb-2">
                                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                                <small>{{ $benefit }}</small>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="card-footer text-center">
                                                <div class="selection-indicator">
                                                    <i class="fas fa-check-circle text-success" style="display: none;"></i>
                                                    <span class="text-muted">Cliquez pour sélectionner</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-warning text-center">
                                        <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                        <h5>Aucun package disponible</h5>
                                        <p>Aucun package de sponsoring n'est disponible pour le moment.</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Résumé de la sélection -->
                    <div class="row mb-4" id="selection-summary" style="display: none;">
                        <div class="col-12">
                            <div class="card border-success">
                                <div class="card-body">
                                    <h6 class="card-title text-success">
                                        <i class="fas fa-check-circle me-2"></i>Package sélectionné
                                    </h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Package :</strong> <span id="selected-package-name">-</span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Prix suggéré :</strong> <span id="selected-package-price">-</span> TND
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Montant personnalisé -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="sponsorship_amount" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave text-success me-2"></i>Montant proposé (TND)
                            </label>
                            <div class="input-group">
                                <input type="number" class="form-control form-control-lg" id="sponsorship_amount" name="amount" step="0.01" min="0" required>
                                <span class="input-group-text">TND</span>
                            </div>
                            <div class="form-text">
                                <i class="fas fa-info-circle me-1"></i>
                                Vous pouvez proposer un montant différent du package sélectionné.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">
                                <i class="fas fa-calendar-alt text-primary me-2"></i>Date de proposition
                            </label>
                            <div class="form-control-plaintext">{{ now()->format('d/m/Y H:i') }}</div>
                        </div>
                    </div>
                    
                    <!-- Message personnalisé -->
                    <div class="mb-4">
                        <label for="sponsorship_message" class="form-label fw-bold">
                            <i class="fas fa-comment-dots text-info me-2"></i>Message personnalisé
                        </label>
                        <textarea class="form-control" id="sponsorship_message" name="message" rows="4" 
                                  placeholder="Décrivez votre proposition de sponsoring, vos motivations, et ce que vous souhaitez obtenir en retour..."></textarea>
                        <div class="form-text">
                            <i class="fas fa-lightbulb me-1"></i>
                            Un message personnalisé augmente vos chances d'être accepté.
                        </div>
                        <div class="d-flex justify-content-between mt-2">
                            <small class="text-muted">0 / 1000 caractères</small>
                            <small class="text-muted" id="char-count">0 caractères</small>
                        </div>
                    </div>

                    <!-- Conditions et informations -->
                    <div class="alert alert-light border">
                        <h6 class="fw-bold mb-2">
                            <i class="fas fa-info-circle text-primary me-2"></i>Informations importantes
                        </h6>
                        <ul class="mb-0 small">
                            <li>Votre proposition sera envoyée à l'organisateur de l'événement</li>
                            <li>L'organisateur aura 48h pour répondre à votre proposition</li>
                            <li>Une fois acceptée, vous recevrez un contrat de sponsoring</li>
                            <li>Le paiement s'effectuera selon les modalités convenues</li>
                        </ul>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <button type="button" class="btn btn-success btn-lg" onclick="submitSponsorship()" id="submit-sponsorship-btn" disabled>
                    <i class="fas fa-paper-plane me-2"></i>Envoyer la Proposition
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let selectedPackageId = null;
let selectedPackagePrice = 0;
let selectedPackageName = '';
let currentEventId = null; // Stocker l'ID de l'événement actuel

// Animation et effets visuels
document.addEventListener('DOMContentLoaded', function() {
    // Compteur de caractères pour le message
    const messageTextarea = document.getElementById('sponsorship_message');
    const charCount = document.getElementById('char-count');
    
    if (messageTextarea && charCount) {
        messageTextarea.addEventListener('input', function() {
            const length = this.value.length;
            charCount.textContent = length + ' caractères';
            
            // Changer la couleur selon la longueur
            if (length > 800) {
                charCount.className = 'text-danger';
            } else if (length > 500) {
                charCount.className = 'text-warning';
            } else {
                charCount.className = 'text-muted';
            }
        });
    }
    
    // Validation du formulaire en temps réel
    const amountInput = document.getElementById('sponsorship_amount');
    const submitBtn = document.getElementById('submit-sponsorship-btn');
    
    if (amountInput && submitBtn) {
        amountInput.addEventListener('input', validateForm);
    }
});

function validateForm() {
    const packageSelected = selectedPackageId !== null;
    const amountValid = document.getElementById('sponsorship_amount').value > 0;
    
    const submitBtn = document.getElementById('submit-sponsorship-btn');
    if (submitBtn) {
        submitBtn.disabled = !(packageSelected && amountValid);
        
        if (packageSelected && amountValid) {
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-success');
        } else {
            submitBtn.classList.remove('btn-success');
            submitBtn.classList.add('btn-secondary');
        }
    }
}

function showEventDetails(eventId) {
    // Stocker l'ID de l'événement actuel
    currentEventId = eventId;
    
    // Afficher un indicateur de chargement
    const modal = document.getElementById('eventDetailsModal');
    const modalBody = modal.querySelector('.modal-body');
    modalBody.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Chargement...</span></div><p class="mt-2">Chargement des détails...</p></div>';
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();
    
    fetch(`/api/events/${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const event = data.event;
                
                // Restaurer le contenu original du modal
                modalBody.innerHTML = `
                    <div class="row">
                        <div class="col-md-6">
                            <img id="event-image" src="/storage/${event.img}" class="img-fluid rounded mb-3" alt="${event.title}" style="display: none;">
                            <div id="no-image" class="text-center py-5 bg-light rounded mb-3" style="display: block;">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">Aucune image disponible</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h5 id="event-title">${event.title}</h5>
                            <p id="event-description">${event.description}</p>
                            <div class="row">
                                <div class="col-6">
                                    <strong>Date :</strong><br>
                                    <span id="event-date">${new Date(event.date).toLocaleDateString('fr-FR')}</span>
                                </div>
                                <div class="col-6">
                                    <strong>Lieu :</strong><br>
                                    <span id="event-location">${event.location}</span>
                                </div>
                            </div>
                            <div class="row mt-3">
                                <div class="col-6">
                                    <strong>Capacité :</strong><br>
                                    <span id="event-capacity">${event.capacity} personnes</span>
                                </div>
                                <div class="col-6">
                                    <strong>Statut :</strong><br>
                                    <span id="event-status" class="badge bg-${getStatusColor(event.status)}">${event.status}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                if (event.img) {
                    document.getElementById('event-image').style.display = 'block';
                    document.getElementById('no-image').style.display = 'none';
                }
                
            } else {
                modalBody.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement des détails de l\'événement</div>';
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            modalBody.innerHTML = '<div class="alert alert-danger"><i class="fas fa-exclamation-triangle"></i> Erreur lors du chargement des détails de l\'événement</div>';
        });
}

function getStatusColor(status) {
    switch(status) {
        case 'upcoming': return 'primary';
        case 'ongoing': return 'success';
        case 'completed': return 'secondary';
        case 'cancelled': return 'danger';
        default: return 'secondary';
    }
}

function showSponsorshipModal(eventId) {
    // Réinitialiser le formulaire
    selectedPackageId = null;
    selectedPackagePrice = 0;
    selectedPackageName = '';
    
    document.getElementById('event_id').value = eventId;
    document.getElementById('sponsorship_amount').value = '';
    document.getElementById('sponsorship_message').value = '';
    document.getElementById('submit-sponsorship-btn').disabled = true;
    document.getElementById('selection-summary').style.display = 'none';
    
    // Réinitialiser les cartes de packages
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('border-success', 'bg-light');
        card.querySelector('.selection-indicator i').style.display = 'none';
        card.querySelector('.selection-indicator span').textContent = 'Cliquez pour sélectionner';
    });
    
    // Récupérer le titre de l'événement
    fetch(`/api/events/${eventId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('selected-event-title').textContent = data.event.title;
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            document.getElementById('selected-event-title').textContent = 'Événement';
        });
    
    const modal = new bootstrap.Modal(document.getElementById('sponsorshipModal'));
    modal.show();
}

function selectPackage(packageId, price, name) {
    selectedPackageId = packageId;
    selectedPackagePrice = price;
    selectedPackageName = name;
    
    // Mettre à jour le champ montant
    document.getElementById('sponsorship_amount').value = price;
    
    // Mettre en surbrillance la carte sélectionnée
    document.querySelectorAll('.package-card').forEach(card => {
        card.classList.remove('border-success', 'bg-light');
        card.querySelector('.selection-indicator i').style.display = 'none';
        card.querySelector('.selection-indicator span').textContent = 'Cliquez pour sélectionner';
    });
    
    const selectedCard = document.querySelector(`[data-package-id="${packageId}"]`);
    selectedCard.classList.add('border-success', 'bg-light');
    selectedCard.querySelector('.selection-indicator i').style.display = 'inline';
    selectedCard.querySelector('.selection-indicator span').textContent = 'Sélectionné';
    
    // Afficher le résumé de sélection
    document.getElementById('selected-package-name').textContent = name;
    document.getElementById('selected-package-price').textContent = price.toLocaleString('fr-FR');
    document.getElementById('selection-summary').style.display = 'block';
    
    // Valider le formulaire
    validateForm();
    
    // Animation de sélection
    selectedCard.style.transform = 'scale(1.02)';
    setTimeout(() => {
        selectedCard.style.transform = 'scale(1)';
    }, 200);
}

function submitSponsorship() {
    const eventId = document.getElementById('event_id').value;
    const packageId = selectedPackageId;
    const amount = document.getElementById('sponsorship_amount').value;
    const message = document.getElementById('sponsorship_message').value;
    
    if (!packageId || !amount) {
        showAlert('Veuillez sélectionner un package et saisir un montant', 'warning');
        return;
    }
    
    if (parseFloat(amount) <= 0) {
        showAlert('Le montant doit être supérieur à 0', 'warning');
        return;
    }
    
    // Désactiver le bouton pendant l'envoi
    const submitBtn = document.getElementById('submit-sponsorship-btn');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Envoi en cours...';
    
    const formData = {
        event_id: eventId,
        package_id: packageId,
        amount: amount,
        message: message,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };
    
    fetch('/sponsor/sponsorships', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Proposition de sponsoring envoyée avec succès !', 'success');
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('sponsorshipModal'));
            modal.hide();
            // Recharger la page après un délai
            setTimeout(() => {
                location.reload();
            }, 2000);
        } else {
            showAlert('Erreur lors de l\'envoi de la proposition : ' + (data.error || 'Erreur inconnue'), 'danger');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('Erreur lors de l\'envoi de la proposition', 'danger');
    })
    .finally(() => {
        // Restaurer le bouton
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function showAlert(message, type) {
    // Créer une alerte temporaire
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'times-circle'} me-2"></i>
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>

<style>
/* Styles personnalisés pour améliorer l'interface */
.package-card {
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.package-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
}

.package-card.border-success {
    border-color: #28a745 !important;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.benefit-item {
    display: flex;
    align-items: flex-start;
    text-align: left;
}

.benefit-item i {
    margin-top: 2px;
    flex-shrink: 0;
}

.price-display {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    padding: 15px;
    border-radius: 10px;
    border: 1px solid #dee2e6;
}

.selection-indicator {
    transition: all 0.3s ease;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
}

.campaign-card {
    transition: all 0.3s ease;
}

.campaign-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.btn-lg {
    padding: 12px 30px;
    font-size: 1.1rem;
}

.form-control-lg {
    padding: 12px 16px;
    font-size: 1.1rem;
}

/* Animation pour les modales */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
    transform: scale(0.8);
}

.modal.show .modal-dialog {
    transform: scale(1);
}

/* Responsive design */
@media (max-width: 768px) {
    .package-card {
        margin-bottom: 15px;
    }
    
    .btn-lg {
        padding: 10px 20px;
        font-size: 1rem;
    }
}
</style>
