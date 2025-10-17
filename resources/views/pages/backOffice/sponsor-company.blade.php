@extends('layouts.sponsor')

@section('title', 'Mon Entreprise - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-building text-primary"></i>
        Mon Entreprise
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Entreprise</li>
        </ol>
    </nav>
</div>

<div class="row">
    <!-- Company Information Card -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-building text-primary"></i> Informations de l'Entreprise
                </h5>
            </div>
            <div class="card-body">
                <form id="companyForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Nom de l'entreprise *</strong></label>
                                <input type="text" name="company_name" class="form-control" 
                                       value="{{ $sponsor->company_name ?? '' }}" 
                                       placeholder="Nom de votre entreprise" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Site web</strong></label>
                                <input type="url" name="website" class="form-control" 
                                       value="{{ $sponsor->website ?? '' }}" 
                                       placeholder="https://www.votre-entreprise.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Téléphone entreprise</strong></label>
                                <input type="text" name="phone" class="form-control" 
                                       value="{{ $sponsor->phone ?? '' }}" 
                                       placeholder="+216 XX XXX XXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Statut</strong></label>
                                <select name="status" class="form-control">
                                    <option value="active" {{ ($sponsor->status ?? 'active') === 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ ($sponsor->status ?? '') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    <option value="pending" {{ ($sponsor->status ?? '') === 'pending' ? 'selected' : '' }}>En attente</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label"><strong>Description de l'entreprise</strong></label>
                        <textarea name="description" class="form-control" rows="4" 
                                  placeholder="Décrivez votre entreprise, ses activités, ses valeurs...">{{ $sponsor->description ?? '' }}</textarea>
                    </div>
                    
                    <!-- Logo de l'entreprise -->
                    <div class="form-group mb-3">
                        <label class="form-label"><strong>Logo de l'entreprise</strong></label>
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($sponsor && $sponsor->logo)
                                    <img src="{{ asset('storage/' . $sponsor->logo) }}" alt="Company Logo" class="rounded border" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="rounded bg-light border d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="fas fa-building fa-2x text-muted"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <input type="file" name="logo" class="form-control" accept="image/*">
                                <small class="text-muted">JPG, PNG, GIF jusqu'à 2MB</small>
                                @if($sponsor && $sponsor->logo)
                                    <small class="text-success d-block mt-1">
                                        <i class="fas fa-check-circle"></i> Logo actuel
                                    </small>
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Sauvegarder les informations
                        </button>
                        <a href="{{ route('sponsor.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Company Stats Card -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-success"></i> Statistiques Entreprise
                </h5>
            </div>
            <div class="card-body">
                @if($sponsor)
                    <div class="stats-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Sponsorships totaux</span>
                            <strong class="text-primary">{{ $stats->sponsorships_count }}</strong>
                        </div>
                    </div>
                    <div class="stats-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Montant investi</span>
                            <strong class="text-success">{{ number_format($stats->total_amount, 0) }} TND</strong>
                        </div>
                    </div>
                    <div class="stats-item mb-3">
                        <div class="d-flex justify-content-between">
                            <span>Sponsorships actifs</span>
                            <strong class="text-warning">{{ $stats->active_sponsorships }}</strong>
                        </div>
                    </div>
                    <div class="stats-item">
                        <div class="d-flex justify-content-between">
                            <span>Statut</span>
                            <span class="badge badge-{{ $sponsor->status === 'active' ? 'success' : ($sponsor->status === 'pending' ? 'warning' : 'secondary') }}">
                                {{ ucfirst($sponsor->status) }}
                            </span>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-building fa-3x text-muted mb-3"></i>
                        <h6 class="text-muted">Aucune information entreprise</h6>
                        <p class="text-muted small">Remplissez le formulaire pour créer votre profil entreprise.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Appliquer le style gris aux champs remplis au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        if (input.value && input.value.trim() !== '') {
            input.style.backgroundColor = '#f8f9fa';
            input.style.borderColor = '#dee2e6';
        }
        
        // Appliquer le style quand l'utilisateur tape
        input.addEventListener('input', function() {
            if (this.value && this.value.trim() !== '') {
                this.style.backgroundColor = '#f8f9fa';
                this.style.borderColor = '#dee2e6';
            } else {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            }
        });
        
        // Appliquer le style quand l'utilisateur clique (focus)
        input.addEventListener('focus', function() {
            this.style.backgroundColor = 'white';
            this.style.borderColor = '#28a745';
        });
        
        // Remettre le style gris quand l'utilisateur quitte le champ
        input.addEventListener('blur', function() {
            if (this.value && this.value.trim() !== '') {
                this.style.backgroundColor = '#f8f9fa';
                this.style.borderColor = '#dee2e6';
            } else {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            }
        });
    });
});

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

// Company Form Submission
document.getElementById('companyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
    submitBtn.disabled = true;
    
    try {
        // Ajouter le token CSRF au FormData
        const csrfToken = document.querySelector('input[name="_token"]').value;
        formData.append('_method', 'PUT');
        
        const response = await fetch('{{ route("sponsor.company.update") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            
            // Mettre à jour les champs du formulaire avec les données retournées
            if (data.data) {
                document.querySelector('input[name="company_name"]').value = data.data.company_name || '';
                document.querySelector('input[name="website"]').value = data.data.website || '';
                document.querySelector('input[name="phone"]').value = data.data.phone || '';
                document.querySelector('textarea[name="description"]').value = data.data.description || '';
                
                // Mettre à jour l'image du logo si elle a été uploadée
                if (data.data.logo) {
                    const logoImg = document.querySelector('#companyForm img');
                    if (logoImg) {
                        logoImg.src = '/storage/' + data.data.logo;
                    }
                }
                
                // Appliquer le style gris aux champs maintenant remplis
                const inputs = document.querySelectorAll('.form-control');
                inputs.forEach(input => {
                    if (input.value && input.value.trim() !== '') {
                        input.style.backgroundColor = '#f8f9fa';
                        input.style.borderColor = '#dee2e6';
                    }
                });
            }
            
            // Ne pas recharger la page, juste mettre à jour le formulaire
        } else {
            if (data.errors) {
                let errorMessage = 'Erreurs de validation :\n';
                for (const field in data.errors) {
                    errorMessage += `• ${data.errors[field][0]}\n`;
                }
                showAlert('danger', errorMessage);
            } else {
                showAlert('danger', data.error || 'Erreur lors de la mise à jour');
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

// Image preview for logo
document.querySelector('input[name="logo"]').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('#companyForm img');
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Appliquer le style gris aux champs remplis au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        if (input.value && input.value.trim() !== '') {
            input.style.backgroundColor = '#f8f9fa';
            input.style.borderColor = '#dee2e6';
        }

        // Appliquer le style quand l'utilisateur tape
        input.addEventListener('input', function() {
            if (this.value && this.value.trim() !== '') {
                this.style.backgroundColor = '#f8f9fa';
                this.style.borderColor = '#dee2e6';
            } else {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            }
        });

        // Appliquer le style quand l'utilisateur clique (focus)
        input.addEventListener('focus', function() {
            this.style.backgroundColor = 'white';
            this.style.borderColor = '#28a745';
        });

        // Remettre le style gris quand l'utilisateur quitte le champ
        input.addEventListener('blur', function() {
            if (this.value && this.value.trim() !== '') {
                this.style.backgroundColor = '#f8f9fa';
                this.style.borderColor = '#dee2e6';
            } else {
                this.style.backgroundColor = '';
                this.style.borderColor = '';
            }
        });
    });
});
</script>
@endpush
@endsection
