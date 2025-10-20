@extends('layouts.sponsor')

@section('title', 'Mon Profil - Echofy Sponsor')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 15px;
    }

    .profile-card {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        margin-bottom: 2rem;
    }

    .profile-image-container {
        text-align: center;
        margin-bottom: 2rem;
    }

    .profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #f8f9fa;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .profile-image-placeholder {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        color: white;
        font-size: 3rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 0.5rem;
    }

    .form-control {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 2rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .budget-info {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
    }

    .sector-info {
        background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
    }

    .alert {
        border-radius: 10px;
        border: none;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        padding: 1.5rem;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        text-align: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: bold;
        color: #667eea;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- En-tête du profil -->
    <div class="profile-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-0">
                        <i class="fas fa-user-circle me-3"></i>
                        Mon Profil Sponsor
                    </h1>
                    <p class="mb-0 mt-2 opacity-75">
                        Gérez vos informations personnelles et vos préférences de sponsoring
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    @if($user->budget)
                        <div class="budget-info">
                            <i class="fas fa-euro-sign me-2"></i>
                            <strong>Budget: {{ number_format($user->budget, 0, ',', ' ') }}€</strong>
                        </div>
                    @endif
                    @if($user->sector)
                        <div class="sector-info">
                            <i class="fas fa-industry me-2"></i>
                            <strong>Secteur: {{ ucfirst($user->sector) }}</strong>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques rapides -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $user->sponsorshipsTemp()->count() }}</div>
            <div class="stat-label">Sponsorships</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $user->sponsorshipsTemp()->where('status', 'approved')->count() }}</div>
            <div class="stat-label">Approuvés</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ number_format($user->sponsorshipsTemp()->sum('amount'), 0, ',', ' ') }}€</div>
            <div class="stat-label">Investi Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $user->sponsorshipsTemp()->where('status', 'completed')->count() }}</div>
            <div class="stat-label">Terminés</div>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-8">
            <div class="profile-card">
                <h3 class="mb-4">
                    <i class="fas fa-user me-2"></i>
                    Informations Personnelles
                </h3>

                <!-- Image de profil -->
                <div class="profile-image-container">
                    @if($user->profile_image)
                        <img src="{{ asset('storage/' . $user->profile_image) }}" 
                             alt="{{ $user->name }}" 
                             class="profile-image">
                    @else
                        <div class="profile-image-placeholder">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif
                    <div class="mt-3">
                        <input type="file" id="profile_image" name="profile_image" 
                               class="form-control" accept="image/*">
                    </div>
                </div>

                <form id="profileForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name" class="form-label">Nom complet *</label>
                                <input type="text" class="form-control" id="name" name="name" 
                                       value="{{ $user->name }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" 
                                       value="{{ $user->email }}" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="company_name" class="form-label">Nom de l'entreprise</label>
                                <input type="text" class="form-control" id="company_name" name="company_name" 
                                       value="{{ $user->company_name }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="phone" class="form-label">Téléphone</label>
                                <input type="text" class="form-control" id="phone" name="phone" 
                                       value="{{ $user->phone }}">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="city" class="form-label">Ville</label>
                                <input type="text" class="form-control" id="city" name="city" 
                                       value="{{ $user->city }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="address" class="form-label">Adresse</label>
                                <input type="text" class="form-control" id="address" name="address" 
                                       value="{{ $user->address }}">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio" class="form-label">Biographie</label>
                        <textarea class="form-control" id="bio" name="bio" rows="4" 
                                  placeholder="Décrivez votre entreprise et vos objectifs de sponsoring...">{{ $user->bio }}</textarea>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Sauvegarder les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Préférences de sponsoring -->
        <div class="col-lg-4">
            <div class="profile-card">
                <h3 class="mb-4">
                    <i class="fas fa-cogs me-2"></i>
                    Préférences de Sponsoring
                </h3>

                <form id="sponsoringForm">
                    @csrf
                    <div class="form-group">
                        <label for="budget" class="form-label">Budget annuel (€)</label>
                        <input type="number" class="form-control" id="budget" name="budget" 
                               value="{{ $user->budget }}" min="0" step="100" 
                               placeholder="Ex: 50000">
                        <small class="form-text text-muted">
                            Votre budget annuel pour le sponsoring d'événements
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="sector" class="form-label">Secteur d'activité</label>
                        <select class="form-control" id="sector" name="sector">
                            <option value="">Sélectionnez un secteur</option>
                            <option value="technology" {{ $user->sector == 'technology' ? 'selected' : '' }}>Technologie</option>
                            <option value="healthcare" {{ $user->sector == 'healthcare' ? 'selected' : '' }}>Santé</option>
                            <option value="finance" {{ $user->sector == 'finance' ? 'selected' : '' }}>Finance</option>
                            <option value="education" {{ $user->sector == 'education' ? 'selected' : '' }}>Éducation</option>
                            <option value="environment" {{ $user->sector == 'environment' ? 'selected' : '' }}>Environnement</option>
                            <option value="entertainment" {{ $user->sector == 'entertainment' ? 'selected' : '' }}>Divertissement</option>
                            <option value="sports" {{ $user->sector == 'sports' ? 'selected' : '' }}>Sports</option>
                            <option value="food" {{ $user->sector == 'food' ? 'selected' : '' }}>Alimentation</option>
                            <option value="fashion" {{ $user->sector == 'fashion' ? 'selected' : '' }}>Mode</option>
                            <option value="automotive" {{ $user->sector == 'automotive' ? 'selected' : '' }}>Automobile</option>
                        </select>
                        <small class="form-text text-muted">
                            Votre secteur d'activité pour des recommandations personnalisées
                        </small>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>
                            Sauvegarder
                        </button>
                    </div>
                </form>
            </div>

            <!-- Changement de mot de passe -->
            <div class="profile-card">
                <h3 class="mb-4">
                    <i class="fas fa-lock me-2"></i>
                    Sécurité
                </h3>

                <form id="passwordForm">
                    @csrf
                    <div class="form-group">
                        <label for="current_password" class="form-label">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key me-2"></i>
                            Changer le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Alertes -->
<div id="alertContainer"></div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const token = localStorage.getItem('jwt_token');
    
    // Gestion du formulaire de profil
    document.getElementById('profileForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('name', document.getElementById('name').value);
        formData.append('email', document.getElementById('email').value);
        formData.append('company_name', document.getElementById('company_name').value);
        formData.append('phone', document.getElementById('phone').value);
        formData.append('city', document.getElementById('city').value);
        formData.append('address', document.getElementById('address').value);
        formData.append('bio', document.getElementById('bio').value);
        
        const profileImage = document.getElementById('profile_image').files[0];
        if (profileImage) {
            formData.append('profile_image', profileImage);
        }
        
        try {
            const response = await fetch('/api/sponsor/profile', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                showAlert('Profil mis à jour avec succès !', 'success');
                // Rafraîchir la page après 2 secondes
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showAlert(data.error || 'Erreur lors de la mise à jour.', 'danger');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showAlert('Erreur réseau ou serveur.', 'danger');
        }
    });
    
    // Gestion du formulaire de sponsoring
    document.getElementById('sponsoringForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('budget', document.getElementById('budget').value);
        formData.append('sector', document.getElementById('sector').value);
        
        try {
            const response = await fetch('/api/sponsor/profile', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                showAlert('Préférences de sponsoring mises à jour !', 'success');
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showAlert(data.error || 'Erreur lors de la mise à jour.', 'danger');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showAlert('Erreur réseau ou serveur.', 'danger');
        }
    });
    
    // Gestion du formulaire de mot de passe
    document.getElementById('passwordForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData();
        formData.append('current_password', document.getElementById('current_password').value);
        formData.append('password', document.getElementById('password').value);
        formData.append('password_confirmation', document.getElementById('password_confirmation').value);
        
        try {
            const response = await fetch('/api/sponsor/profile/password', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (response.ok) {
                showAlert('Mot de passe mis à jour avec succès !', 'success');
                document.getElementById('passwordForm').reset();
            } else {
                showAlert(data.error || 'Erreur lors de la mise à jour.', 'danger');
            }
        } catch (error) {
            console.error('Erreur:', error);
            showAlert('Erreur réseau ou serveur.', 'danger');
        }
    });
    
    function showAlert(message, type) {
        const alertContainer = document.getElementById('alertContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alertDiv);
        
        // Supprimer l'alerte après 5 secondes
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }
});
</script>
@endpush