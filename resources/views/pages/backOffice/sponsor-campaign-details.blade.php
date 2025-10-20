@extends('layouts.sponsor')

@section('title', 'Détails Campagne - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-calendar-alt text-primary"></i>
        {{ $event->title }}
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item"><a href="{{ route('sponsor.campaigns') }}">Campagnes</a></li>
            <li class="breadcrumb-item active">Détails</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Campaign Details -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary"></i> Informations de la Campagne
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="fas fa-calendar text-success"></i> Date de début :</strong>
                            <span class="ms-2">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="fas fa-calendar text-danger"></i> Date de fin :</strong>
                            <span class="ms-2">{{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="fas fa-tag text-info"></i> Catégorie :</strong>
                            <span class="ms-2">{{ $event->category->name ?? 'Non spécifiée' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="fas fa-eye text-warning"></i> Vues :</strong>
                            <span class="ms-2">{{ $event->views_count ?? 0 }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="info-item mb-3">
                    <strong><i class="fas fa-share text-primary"></i> Partages :</strong>
                    <span class="ms-2">{{ $event->shares_count ?? 0 }}</span>
                </div>
                
                <div class="info-item mb-4">
                    <strong><i class="fas fa-align-left text-secondary"></i> Description :</strong>
                    <div class="mt-2 p-3 bg-light rounded">
                        {{ $event->description }}
                    </div>
                </div>
                
                @if($event->content)
                <div class="info-item">
                    <strong><i class="fas fa-file-text text-info"></i> Contenu :</strong>
                    <div class="mt-2 p-3 bg-light rounded">
                        {{ $event->content }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Sponsorship Packages -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-gift text-success"></i> Packages de Sponsoring
                </h5>
            </div>
            <div class="card-body">
                @if($existingSponsorship)
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle"></i>
                        <strong>Sponsorship déjà proposé !</strong><br>
                        Vous avez déjà proposé un sponsorship pour cette campagne le {{ \Carbon\Carbon::parse($existingSponsorship->created_at)->format('d/m/Y à H:i') }}.
                        <br><small>Package: {{ $existingSponsorship->package_name }} - Montant: {{ number_format($existingSponsorship->amount, 0) }} TND</small>
                    </div>
                @endif
                
                @if($packages->count() > 0)
                    @foreach($packages as $package)
                        <div class="package-card mb-3 p-3 border rounded">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="mb-0 text-primary">{{ $package->name }}</h6>
                                <span class="badge badge-success">{{ number_format($package->price, 0) }} TND</span>
                            </div>
                            
                            @if($package->description)
                                <p class="text-muted small mb-2">{{ $package->description }}</p>
                            @endif
                            
                            @if($package->benefits && is_array($package->benefits))
                                <ul class="benefits-list small mb-3">
                                    @foreach($package->benefits as $benefit)
                                        <li><i class="fas fa-check text-success me-1"></i> {{ $benefit }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            @if($existingSponsorship)
                                <button class="btn btn-secondary btn-sm w-100" disabled>
                                    <i class="fas fa-ban"></i> Déjà proposé
                                </button>
                            @else
                                <button class="btn btn-outline-primary btn-sm w-100" 
                                        onclick="openSponsorshipModal({{ $package->id }}, '{{ $package->name }}', {{ $package->price }})">
                                    <i class="fas fa-handshake"></i> Proposer un Sponsorship
                                </button>
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-gift fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucun package disponible</h6>
                        <p class="text-muted small">Contactez l'organisateur pour plus d'informations.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Sponsorship Modal -->
<div class="modal fade" id="sponsorshipModal" tabindex="-1" aria-labelledby="sponsorshipModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sponsorshipModalLabel">
                    <i class="fas fa-handshake text-primary"></i> Proposer un Sponsorship
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="sponsorshipForm">
                @csrf
                <div class="modal-body">
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    <input type="hidden" name="package_id" id="package_id">
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Package sélectionné</strong></label>
                        <div class="p-2 bg-light rounded" id="selected_package">
                            <!-- Package info will be inserted here -->
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Montant proposé (TND) *</strong></label>
                        <input type="number" name="amount" id="amount" class="form-control" 
                               min="0" step="0.01" required>
                        <small class="text-muted">Montant que vous souhaitez investir</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label"><strong>Notes (optionnel)</strong></label>
                        <textarea name="notes" class="form-control" rows="3" 
                                  placeholder="Ajoutez des commentaires ou des conditions particulières..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Proposer le Sponsorship
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function openSponsorshipModal(packageId, packageName, packagePrice) {
    document.getElementById('package_id').value = packageId;
    document.getElementById('amount').value = packagePrice;
    
    document.getElementById('selected_package').innerHTML = `
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong class="text-primary">${packageName}</strong>
                <br>
                <small class="text-muted">Prix suggéré: ${packagePrice} TND</small>
            </div>
            <span class="badge badge-success">${packagePrice} TND</span>
        </div>
    `;
    
    const modal = new bootstrap.Modal(document.getElementById('sponsorshipModal'));
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

// Sponsorship Form Submission
document.getElementById('sponsorshipForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Envoi...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("sponsor.sponsorships.create") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            const modal = bootstrap.Modal.getInstance(document.getElementById('sponsorshipModal'));
            modal.hide();
            setTimeout(() => {
                window.location.href = '{{ route("sponsor.sponsorships") }}';
            }, 1500);
        } else {
            if (data.errors) {
                let errorMessage = 'Erreurs de validation :\n';
                for (const field in data.errors) {
                    errorMessage += `• ${data.errors[field][0]}\n`;
                }
                showAlert('danger', errorMessage);
            } else {
                showAlert('danger', data.error || 'Erreur lors de la création du sponsorship');
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});
</script>
@endpush
@endsection
