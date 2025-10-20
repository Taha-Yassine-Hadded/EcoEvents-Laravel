@extends('layouts.admin')

@section('title', 'Créer un Package - Echofy Admin')
@section('page-title', 'Créer un Package')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="stats-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="card-title mb-0">Nouveau Package</h5>
                <a href="{{ route('admin.packages.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Retour à la liste
                </a>
            </div>
            
            <form id="packageForm">
                @csrf
                
                <!-- Informations de base -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="name" class="form-label">Nom du Package <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" required>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="price" class="form-label">Prix (€) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" required>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label for="event_id" class="form-label">Événement <span class="text-danger">*</span></label>
                        <select class="form-control" id="event_id" name="event_id" required>
                            <option value="">Sélectionner un événement</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                                    {{ $event->title }} - {{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}
                                </option>
                            @endforeach
                        </select>
                        <div class="invalid-feedback"></div>
                    </div>
                    
                    <div class="col-md-6">
                        <label for="sort_order" class="form-label">Ordre d'affichage</label>
                        <input type="number" class="form-control" id="sort_order" name="sort_order" min="0" value="0">
                        <small class="form-text text-muted">Plus le nombre est petit, plus le package apparaîtra en premier</small>
                    </div>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="description" rows="3" maxlength="1000"></textarea>
                    <small class="form-text text-muted">Maximum 1000 caractères</small>
                </div>
                
                <!-- Bénéfices -->
                <div class="mb-4">
                    <label class="form-label">Bénéfices du Package</label>
                    <div id="benefitsContainer">
                        <div class="benefit-item mb-2">
                            <div class="input-group">
                                <input type="text" class="form-control benefit-input" name="benefits[]" placeholder="Ex: Logo sur les supports de communication">
                                <button type="button" class="btn btn-outline-danger" onclick="removeBenefit(this)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="addBenefit()">
                        <i class="fas fa-plus"></i> Ajouter un bénéfice
                    </button>
                </div>
                
                <!-- Options -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                Package actif
                            </label>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured">
                            <label class="form-check-label" for="is_featured">
                                Mettre en avant
                            </label>
                        </div>
                    </div>
                </div>
                
                <!-- Boutons d'action -->
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.packages.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Créer le Package
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="stats-card">
            <h6 class="card-title">Aide</h6>
            <div class="alert alert-info">
                <h6><i class="fas fa-info-circle"></i> Conseils pour créer un package :</h6>
                <ul class="mb-0">
                    <li>Choisissez un nom clair et attractif</li>
                    <li>Fixez un prix compétitif</li>
                    <li>Listez les bénéfices concrets</li>
                    <li>Utilisez l'ordre d'affichage pour organiser</li>
                    <li>Activez "Mis en avant" pour les packages premium</li>
                </ul>
            </div>
            
            <div class="alert alert-warning">
                <h6><i class="fas fa-exclamation-triangle"></i> Important :</h6>
                <p class="mb-0">Une fois créé, le package sera immédiatement disponible pour les sponsors.</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let benefitCount = 1;

// Ajouter un bénéfice
function addBenefit() {
    const container = document.getElementById('benefitsContainer');
    const newBenefit = document.createElement('div');
    newBenefit.className = 'benefit-item mb-2';
    newBenefit.innerHTML = `
        <div class="input-group">
            <input type="text" class="form-control benefit-input" name="benefits[]" placeholder="Ex: Logo sur les supports de communication">
            <button type="button" class="btn btn-outline-danger" onclick="removeBenefit(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(newBenefit);
    benefitCount++;
}

// Supprimer un bénéfice
function removeBenefit(button) {
    if (benefitCount > 1) {
        button.closest('.benefit-item').remove();
        benefitCount--;
    }
}

// Soumission du formulaire
document.getElementById('packageForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Convertir les checkboxes en booléens
    formData.set('is_active', document.getElementById('is_active').checked ? '1' : '0');
    formData.set('is_featured', document.getElementById('is_featured').checked ? '1' : '0');
    
    // Filtrer les bénéfices vides
    const benefits = Array.from(document.querySelectorAll('.benefit-input'))
        .map(input => input.value.trim())
        .filter(benefit => benefit !== '');
    
    // Créer un nouvel objet FormData avec les bénéfices filtrés
    const cleanFormData = new FormData();
    cleanFormData.append('_token', formData.get('_token'));
    cleanFormData.append('name', formData.get('name'));
    cleanFormData.append('description', formData.get('description'));
    cleanFormData.append('price', formData.get('price'));
    cleanFormData.append('event_id', formData.get('event_id'));
    cleanFormData.append('sort_order', formData.get('sort_order'));
    cleanFormData.append('is_active', formData.get('is_active'));
    cleanFormData.append('is_featured', formData.get('is_featured'));
    
    // Ajouter les bénéfices
    benefits.forEach((benefit, index) => {
        cleanFormData.append(`benefits[${index}]`, benefit);
    });
    
    try {
        const response = await fetch('{{ route("admin.packages.store") }}', {
            method: 'POST',
            body: cleanFormData,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            if (data.redirect) {
                setTimeout(() => window.location.href = data.redirect, 1500);
            }
        } else {
            if (data.errors) {
                // Afficher les erreurs de validation
                Object.keys(data.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const feedback = input.nextElementSibling;
                        if (feedback && feedback.classList.contains('invalid-feedback')) {
                            feedback.textContent = data.errors[field][0];
                        }
                    }
                });
            } else {
                showAlert('danger', data.error || 'Erreur lors de la création');
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur de connexion: ' + error.message);
    }
});

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
