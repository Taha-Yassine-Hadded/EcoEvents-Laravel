@extends('layouts.admin')

@section('title', 'Détails du Contrat - Echofy Admin')
@section('page-title', 'Détails du Contrat')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Informations du Contrat</h5>
                <div class="btn-group">
                    <a href="{{ route('admin.contracts.download', $sponsorship->id) }}" class="btn btn-primary">
                        <i class="fas fa-download"></i> Télécharger
                    </a>
                    <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
            
            <div class="row">
                <!-- Informations du Sponsor -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-user"></i> Informations du Sponsor</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-4"><strong>Nom:</strong></div>
                                <div class="col-8">{{ $sponsorship->user->name }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Entreprise:</strong></div>
                                <div class="col-8">{{ $sponsorship->user->company_name ?? 'Non spécifié' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Email:</strong></div>
                                <div class="col-8">{{ $sponsorship->user->email }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Téléphone:</strong></div>
                                <div class="col-8">{{ $sponsorship->user->phone ?? 'Non spécifié' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Site web:</strong></div>
                                <div class="col-8">
                                    @if($sponsorship->user->website)
                                        <a href="{{ $sponsorship->user->website }}" target="_blank">{{ $sponsorship->user->website }}</a>
                                    @else
                                        Non spécifié
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informations de l'Événement -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-calendar-alt"></i> Informations de l'Événement</h6>
                        </div>
                        <div class="card-body">
                            @php
                                // Logique améliorée pour l'affichage du nom de l'événement
                                $eventName = 'Événement non spécifié';
                                $eventDate = null;
                                $eventLocation = null;
                                
                                // Priorité 1: Relation event chargée
                                if ($sponsorship->event && !empty($sponsorship->event->title)) {
                                    $eventName = $sponsorship->event->title;
                                    $eventDate = $sponsorship->event->date;
                                    $eventLocation = $sponsorship->event->location;
                                }
                                // Priorité 2: Champ event_title dans la table
                                elseif (!empty($sponsorship->event_title)) {
                                    $eventName = $sponsorship->event_title;
                                    $eventDate = $sponsorship->event_date;
                                    $eventLocation = $sponsorship->event_location;
                                }
                                // Priorité 3: Essayer de charger l'événement si event_id existe
                                elseif (!empty($sponsorship->event_id)) {
                                    try {
                                        $event = \App\Models\Event::find($sponsorship->event_id);
                                        if ($event) {
                                            $eventName = $event->title;
                                            $eventDate = $event->date;
                                            $eventLocation = $event->location;
                                        }
                                    } catch (Exception $e) {
                                        // En cas d'erreur, garder le nom par défaut
                                    }
                                }
                            @endphp
                            <div class="row mb-2">
                                <div class="col-4"><strong>Titre:</strong></div>
                                <div class="col-8">{{ $eventName }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Date:</strong></div>
                                <div class="col-8">
                                    @if($eventDate)
                                        {{ \Carbon\Carbon::parse($eventDate)->format('d/m/Y H:i') }}
                                    @else
                                        Non spécifiée
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Lieu:</strong></div>
                                <div class="col-8">{{ $eventLocation ?? 'Non spécifié' }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Package:</strong></div>
                                <div class="col-8">
                                    <span class="badge bg-info">{{ $sponsorship->package_name }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Informations Financières -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-euro-sign"></i> Informations Financières</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-4"><strong>Montant:</strong></div>
                                <div class="col-8">
                                    <span class="h5 text-success">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Statut:</strong></div>
                                <div class="col-8">
                                    <span class="badge bg-success">Approuvé</span>
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Date d'approbation:</strong></div>
                                <div class="col-8">{{ \Carbon\Carbon::parse($sponsorship->created_at)->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Informations du Contrat -->
                <div class="col-md-6 mb-4">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-file-contract"></i> Informations du Contrat</h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-2">
                                <div class="col-4"><strong>Numéro:</strong></div>
                                <div class="col-8">SPONS-{{ str_pad($sponsorship->id, 6, '0', STR_PAD_LEFT) }}-{{ date('Y') }}</div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Fichier:</strong></div>
                                <div class="col-8">
                                    @if($sponsorship->contract_pdf)
                                        <span class="badge bg-success">Généré</span>
                                    @else
                                        <span class="badge bg-warning">Non généré</span>
                                    @endif
                                </div>
                            </div>
                            <div class="row mb-2">
                                <div class="col-4"><strong>Notes:</strong></div>
                                <div class="col-8">{{ $sponsorship->notes ?? 'Aucune note' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Actions -->
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="fas fa-cogs"></i> Actions</h6>
                        </div>
                        <div class="card-body">
                            <div class="btn-group" role="group">
                                <button class="btn btn-outline-primary" onclick="viewContract()">
                                    <i class="fas fa-eye"></i> Voir le Contrat
                                </button>
                                
                                <a href="{{ route('admin.contracts.download', $sponsorship->id) }}" class="btn btn-outline-success">
                                    <i class="fas fa-download"></i> Télécharger
                                </a>
                                
                                <button class="btn btn-outline-warning" onclick="regenerateContract()">
                                    <i class="fas fa-sync"></i> Régénérer
                                </button>
                                
                                <button class="btn btn-outline-danger" onclick="deleteContract()">
                                    <i class="fas fa-trash"></i> Supprimer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                <a href="{{ route('admin.contracts.download', $sponsorship->id) }}" class="btn btn-primary">
                    <i class="fas fa-download"></i> Télécharger
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Voir le contrat
async function viewContract() {
    try {
        const response = await fetch(`/admin/contracts/{{ $sponsorship->id }}/view`);
        const content = await response.text();
        
        document.getElementById('contractContent').innerHTML = content;
        const modal = new bootstrap.Modal(document.getElementById('contractModal'));
        modal.show();
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors du chargement du contrat');
    }
}

// Régénérer le contrat
async function regenerateContract() {
    if (!confirm('Êtes-vous sûr de vouloir régénérer ce contrat ?')) return;
    
    try {
        const response = await fetch(`/admin/contracts/{{ $sponsorship->id }}/regenerate`, {
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

// Supprimer le contrat
async function deleteContract() {
    if (!confirm('Êtes-vous sûr de vouloir supprimer ce contrat ? Cette action est irréversible.')) return;
    
    try {
        const response = await fetch(`/admin/contracts/{{ $sponsorship->id }}/delete`, {
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
            setTimeout(() => window.location.href = '{{ route("admin.contracts.index") }}', 1500);
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
