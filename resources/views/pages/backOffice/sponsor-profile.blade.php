@extends('layouts.sponsor')

@section('title', 'Mon Profil - Echofy Sponsor')

@section('content')
<!-- Profile Content -->
<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-body text-center">
                <div class="profile-avatar mb-3">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">
                    @else
                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mx-auto" style="width: 120px; height: 120px;">
                            <i class="fas fa-user fa-3x text-white"></i>
                        </div>
                    @endif
                </div>
                <h4>{{ $user->name }}</h4>
                <span class="badge badge-primary badge-lg">Sponsor</span>
                <p class="text-muted mt-2">{{ $user->email }}</p>
                
                <!-- Upload Image Button -->
                <button class="btn btn-outline-primary btn-sm mt-2" onclick="document.getElementById('profile_image').click()">
                    <i class="fas fa-camera"></i> Changer la photo
                </button>
                <small class="text-muted d-block mt-1">JPG, PNG, GIF jusqu'à 2MB</small>
                @if($user->profile_image)
                    <small class="text-success d-block mt-1">
                        <i class="fas fa-check-circle"></i> Photo actuelle
                    </small>
                @endif
            </div>
        </div>
        
        <!-- Statistics Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary"></i> Mes Statistiques
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h5 class="text-primary mb-1">0</h5>
                        <small class="text-muted">Sponsorships</small>
                    </div>
                    <div class="col-6">
                        <h5 class="text-success mb-1">0 TND</h5>
                        <small class="text-muted">Investi</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Profile Information Form -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-user-edit text-primary"></i> Informations du Profil
                </h5>
            </div>
            <div class="card-body">
                <form id="profileForm" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Nom complet</strong></label>
                                <input type="text" name="name" class="form-control" value="{{ $user->name }}" placeholder="Votre nom complet">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Email</strong></label>
                                <input type="email" name="email" class="form-control" value="{{ $user->email }}" placeholder="votre@email.com">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Téléphone</strong></label>
                                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" placeholder="+216 XX XXX XXX">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Ville</strong></label>
                                <input type="text" name="city" class="form-control" value="{{ $user->city }}" placeholder="Tunis, Sfax, Sousse...">
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label"><strong>Adresse</strong></label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Votre adresse complète">{{ $user->address }}</textarea>
                    </div>
                    
                    <div class="form-group mb-3">
                        <label class="form-label"><strong>Bio</strong></label>
                        <textarea name="bio" class="form-control" rows="3" placeholder="Parlez-nous de votre entreprise, vos valeurs, votre mission...">{{ $user->bio }}</textarea>
                    </div>
                    
                    <!-- Champ caché pour l'image -->
                    <input type="file" id="profile_image" name="profile_image" accept="image/*" style="display: none;">
                    
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Sauvegarder les modifications
                        </button>
                        <a href="{{ route('sponsor.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left"></i> Retour au Dashboard
                        </a>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Change Password Form -->
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-lock text-warning"></i> Changer le mot de passe
                </h5>
            </div>
            <div class="card-body">
                <form id="passwordForm">
                    @csrf
                    <div class="form-group mb-3">
                        <label class="form-label"><strong>Mot de passe actuel *</strong></label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Nouveau mot de passe *</strong></label>
                                <input type="password" name="password" class="form-control" required minlength="8">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label"><strong>Confirmer le mot de passe *</strong></label>
                                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key"></i> Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card shadow mt-4 border-danger">
            <div class="card-header bg-danger text-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-exclamation-triangle"></i> Zone de Danger
                </h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="fas fa-warning"></i>
                    <strong>Attention !</strong> La suppression de votre profil est irréversible.
                    <ul class="mb-0 mt-2">
                        <li>Toutes vos données personnelles seront supprimées</li>
                        <li>Tous vos sponsorships seront annulés</li>
                        <li>Votre image de profil sera supprimée</li>
                        <li>Vous ne pourrez plus accéder à votre compte</li>
                    </ul>
                </div>
                
                <button type="button" class="btn btn-danger" onclick="confirmDeleteProfile()">
                    <i class="fas fa-trash-alt"></i> Supprimer mon Profil
                </button>
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

// Image preview functionality
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.querySelector('.profile-avatar img');
            if (img) {
                img.src = e.target.result;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Profile Form Submission
document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
    submitBtn.disabled = true;
    
    try {
        // Préparer les données à envoyer (seulement les champs modifiés)
        const dataToSend = new FormData();
        const csrfToken = document.querySelector('input[name="_token"]').value;
        dataToSend.append('_token', csrfToken);
        dataToSend.append('_method', 'PUT');
        
        // Ajouter seulement les champs qui ont été modifiés
        const nameField = formData.get('name');
        const emailField = formData.get('email');
        const phoneField = formData.get('phone');
        const addressField = formData.get('address');
        const cityField = formData.get('city');
        const bioField = formData.get('bio');
        const imageField = formData.get('profile_image');
        
        if (nameField && nameField.trim() !== '') dataToSend.append('name', nameField);
        if (emailField && emailField.trim() !== '') dataToSend.append('email', emailField);
        if (phoneField) dataToSend.append('phone', phoneField);
        if (addressField) dataToSend.append('address', addressField);
        if (cityField) dataToSend.append('city', cityField);
        if (bioField) dataToSend.append('bio', bioField);
        if (imageField && imageField.size > 0) dataToSend.append('profile_image', imageField);

        const response = await fetch('{{ route("sponsor.profile.update") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: dataToSend
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            
            // Mettre à jour l'image dans le header si une nouvelle image a été uploadée
            const imageField = formData.get('profile_image');
            if (imageField && imageField.size > 0) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const headerImage = document.querySelector('.sponsor-header .profile-image');
                    if (headerImage && headerImage.tagName === 'IMG') {
                        headerImage.src = e.target.result;
                    }
                };
                reader.readAsDataURL(imageField);
            }
            
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            if (data.errors) {
                let errorMessage = 'Erreurs de validation :\n';
                for (const field in data.errors) {
                    errorMessage += `• ${data.errors[field][0]}\n`;
                }
                showAlert('error', errorMessage);
            } else {
                showAlert('error', data.error || 'Erreur lors de la mise à jour');
            }
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('error', 'Erreur de connexion');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Password Form Submission
document.getElementById('passwordForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Changement...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("sponsor.profile.password") }}', {
            method: 'PUT',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(Object.fromEntries(formData))
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            this.reset();
        } else {
            showAlert('error', data.error || 'Erreur lors du changement de mot de passe');
        }
    } catch (error) {
        showAlert('error', 'Erreur de connexion');
    } finally {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Image Upload Preview
document.getElementById('profile_image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const avatar = document.querySelector('.profile-avatar img');
            if (avatar) {
                avatar.src = e.target.result;
            } else {
                // Create img element if it doesn't exist
                const avatarDiv = document.querySelector('.profile-avatar');
                avatarDiv.innerHTML = `<img src="${e.target.result}" alt="Profile" class="rounded-circle" style="width: 120px; height: 120px; object-fit: cover;">`;
            }
        };
        reader.readAsDataURL(file);
    }
});

// Alert Function
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert at the top of the content
    const content = document.querySelector('.sponsor-dashboard .container');
    content.insertBefore(alertDiv, content.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Fonction pour confirmer la suppression du profil
function confirmDeleteProfile() {
    const confirmMessage = `
        ⚠️ ATTENTION - SUPPRESSION DÉFINITIVE ⚠️
        
        Êtes-vous ABSOLUMENT SÛR de vouloir supprimer votre profil ?
        
        Cette action supprimera :
        • Votre compte utilisateur
        • Tous vos sponsorships
        • Votre image de profil
        • Toutes vos données personnelles
        
        Cette action est IRRÉVERSIBLE !
        
        Tapez "SUPPRIMER" pour confirmer :
    `;
    
    const userInput = prompt(confirmMessage);
    
    if (userInput === "SUPPRIMER") {
        // Double confirmation
        const finalConfirm = confirm(`
            DERNIÈRE CONFIRMATION
            
            Vous êtes sur le point de supprimer définitivement votre profil.
            
            Êtes-vous vraiment sûr ?
        `);
        
        if (finalConfirm) {
            deleteProfile();
        }
    } else if (userInput !== null) {
        alert("Suppression annulée. Vous devez taper exactement 'SUPPRIMER' pour confirmer.");
    }
}

// Fonction pour supprimer le profil
async function deleteProfile() {
    const deleteBtn = document.querySelector('button[onclick="confirmDeleteProfile()"]');
    const originalText = deleteBtn.innerHTML;
    
    deleteBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
    deleteBtn.disabled = true;
    
    try {
        const response = await fetch('{{ route("sponsor.profile.delete") }}', {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            showAlert('success', data.message);
            
            // Redirection après 2 secondes
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 2000);
        } else {
            showAlert('error', data.error || 'Erreur lors de la suppression du profil');
            deleteBtn.innerHTML = originalText;
            deleteBtn.disabled = false;
        }
    } catch (error) {
        console.error('Erreur:', error);
        showAlert('error', 'Erreur de connexion lors de la suppression');
        deleteBtn.innerHTML = originalText;
        deleteBtn.disabled = false;
    }
}
</script>
@endpush
@endsection