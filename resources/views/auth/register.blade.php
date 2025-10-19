@extends('layouts.app')

@section('title', 'Inscription - EcoEvents')

@section('content')
<!--==================================================-->
<!-- Start EcoEvents Registration Area -->
<!--==================================================-->
<div class="registration-area">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="registration-form-wrapper">
                    <div class="section-title center">
                        <h4><img src="{{asset('assets/images/home6/section-title-shape.png')}}" alt="">Rejoignez EcoEvents</h4>
                        <h1>Créer votre compte</h1>
                        <p>Rejoignez notre communauté d'éco-citoyens engagés pour l'environnement</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}" enctype="multipart/form-data" class="registration-form">
                        @csrf
                        
                        <!-- Personal Information -->
                        <div class="form-section">
                            <h3>Informations personnelles</h3>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">Nom complet *</label>
                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" 
                                               value="{{ old('name') }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">Email *</label>
                                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" 
                                               value="{{ old('email') }}" required>
                                        @error('email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password">Mot de passe *</label>
                                        <input type="password" id="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmer le mot de passe *</label>
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">Téléphone</label>
                                        <input type="tel" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" 
                                               value="{{ old('phone') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="city">Ville</label>
                                        <input type="text" id="city" name="city" class="form-control @error('city') is-invalid @enderror" 
                                               value="{{ old('city') }}">
                                        @error('city')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="address">Adresse</label>
                                <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" 
                                          rows="2">{{ old('address') }}</textarea>
                                @error('address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Role Selection -->
                        <div class="form-section">
                            <h3>Type de compte</h3>
                            <div class="role-selection">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="role-option">
                                            <input type="radio" id="role_user" name="role" value="user" 
                                                   {{ old('role', 'user') == 'user' ? 'checked' : '' }}>
                                            <label for="role_user" class="role-card">
                                                <div class="role-icon">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                                <h4>Participant</h4>
                                                <p>Je souhaite participer aux événements écologiques</p>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="role-option">
                                            <input type="radio" id="role_organizer" name="role" value="organizer" 
                                                   {{ old('role') == 'organizer' ? 'checked' : '' }}>
                                            <label for="role_organizer" class="role-card">
                                                <div class="role-icon">
                                                    <i class="bi bi-calendar-event"></i>
                                                </div>
                                                <h4>Organisateur</h4>
                                                <p>Je souhaite créer et organiser des événements</p>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @error('role')
                                <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Interests -->
                        <div class="form-section">
                            <h3>Centres d'intérêt écologiques</h3>
                            <div class="interests-grid">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="interest-item">
                                            <input type="checkbox" id="recyclage" name="interests[]" value="recyclage" 
                                                   {{ in_array('recyclage', old('interests', [])) ? 'checked' : '' }}>
                                            <label for="recyclage">
                                                <i class="bi bi-recycle"></i>
                                                Recyclage
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="interest-item">
                                            <input type="checkbox" id="energie_renouvelable" name="interests[]" value="energie_renouvelable" 
                                                   {{ in_array('energie_renouvelable', old('interests', [])) ? 'checked' : '' }}>
                                            <label for="energie_renouvelable">
                                                <i class="bi bi-lightning"></i>
                                                Énergie renouvelable
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="interest-item">
                                            <input type="checkbox" id="biodiversite" name="interests[]" value="biodiversite" 
                                                   {{ in_array('biodiversite', old('interests', [])) ? 'checked' : '' }}>
                                            <label for="biodiversite">
                                                <i class="bi bi-tree"></i>
                                                Biodiversité
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="interest-item">
                                            <input type="checkbox" id="agriculture_bio" name="interests[]" value="agriculture_bio" 
                                                   {{ in_array('agriculture_bio', old('interests', [])) ? 'checked' : '' }}>
                                            <label for="agriculture_bio">
                                                <i class="bi bi-flower1"></i>
                                                Agriculture bio
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="interest-item">
                                            <input type="checkbox" id="transport_vert" name="interests[]" value="transport_vert" 
                                                   {{ in_array('transport_vert', old('interests', [])) ? 'checked' : '' }}>
                                            <label for="transport_vert">
                                                <i class="bi bi-bicycle"></i>
                                                Transport vert
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="interest-item">
                                            <input type="checkbox" id="zero_dechet" name="interests[]" value="zero_dechet" 
                                                   {{ in_array('zero_dechet', old('interests', [])) ? 'checked' : '' }}>
                                            <label for="zero_dechet">
                                                <i class="bi bi-trash3"></i>
                                                Zéro déchet
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bio and Profile Image -->
                        <div class="form-section">
                            <h3>Profil</h3>
                            <div class="form-group">
                                <label for="bio">Présentation (optionnel)</label>
                                <textarea id="bio" name="bio" class="form-control @error('bio') is-invalid @enderror" 
                                          rows="4" placeholder="Parlez-nous de votre engagement écologique...">{{ old('bio') }}</textarea>
                                @error('bio')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="profile_image">Photo de profil (optionnel)</label>
                                <input type="file" id="profile_image" name="profile_image" 
                                       class="form-control @error('profile_image') is-invalid @enderror" 
                                       accept="image/*">
                                @error('profile_image')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group text-center">
                            <div class="echofy-button style-five">
                                <button type="submit">Créer mon compte<i class="bi bi-arrow-right-short"></i></button>
                            </div>
                        </div>

                        <div class="text-center mt-3">
                            <p>Rejoignez la communauté EcoEvents dès maintenant !</p>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End EcoEvents Registration Area -->
<!--==================================================-->
@endsection

@push('styles')
<style>
.registration-area {
    padding: 100px 0;
    background: #f8f9fa;
}

.registration-form-wrapper {
    background: white;
    padding: 50px;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
}

.form-section h3 {
    color: #2c5530;
    margin-bottom: 20px;
    font-size: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
}

.form-control {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 12px 15px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.form-control:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.role-selection .row {
    gap: 20px;
}

.role-option {
    position: relative;
}

.role-option input[type="radio"] {
    display: none;
}

.role-card {
    display: block;
    padding: 30px 20px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.role-card:hover {
    border-color: #28a745;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.role-option input[type="radio"]:checked + .role-card {
    border-color: #28a745;
    background: #f8fff9;
}

.role-icon {
    font-size: 40px;
    color: #28a745;
    margin-bottom: 15px;
}

.role-card h4 {
    color: #333;
    margin-bottom: 10px;
}

.role-card p {
    color: #666;
    font-size: 14px;
    margin: 0;
}

.interests-grid .interest-item {
    margin-bottom: 15px;
}

.interest-item input[type="checkbox"] {
    display: none;
}

.interest-item label {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.interest-item label:hover {
    border-color: #28a745;
    background: #f8fff9;
}

.interest-item input[type="checkbox"]:checked + label {
    border-color: #28a745;
    background: #f8fff9;
    color: #28a745;
}

.interest-item label i {
    margin-right: 10px;
    font-size: 18px;
}

.echofy-button button {
    background: #28a745;
    color: white;
    border: none;
    padding: 15px 40px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 16px;
    transition: all 0.3s ease;
}

.echofy-button button:hover {
    background: #218838;
    transform: translateY(-2px);
}

.invalid-feedback {
    display: block;
    color: #dc3545;
    font-size: 14px;
    margin-top: 5px;
}

.is-invalid {
    border-color: #dc3545;
}
</style>
@endpush
<<<<<<< HEAD
=======

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.registration-form');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passwordInput = document.getElementById('password');
    const passwordConfirmInput = document.getElementById('password_confirmation');
    const phoneInput = document.getElementById('phone');
    const cityInput = document.getElementById('city');
    const addressInput = document.getElementById('address');
    const bioInput = document.getElementById('bio');

    // Validation en temps réel
    function validateField(input, validationFn, errorMessage) {
        const isValid = validationFn(input.value);
        const feedback = input.parentNode.querySelector('.invalid-feedback') || createFeedback(input);
        
        if (isValid) {
            input.classList.remove('is-invalid');
            input.classList.add('is-valid');
            feedback.style.display = 'none';
        } else {
            input.classList.remove('is-valid');
            input.classList.add('is-invalid');
            feedback.textContent = errorMessage;
            feedback.style.display = 'block';
        }
        return isValid;
    }

    function createFeedback(input) {
        const feedback = document.createElement('div');
        feedback.className = 'invalid-feedback';
        input.parentNode.appendChild(feedback);
        return feedback;
    }

    // Validations spécifiques
    const validations = {
        name: (value) => value.length >= 2 && /^[a-zA-ZÀ-ÿ\s\-']+$/.test(value),
        email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
        password: (value) => {
            return value.length >= 8 && 
                   /[a-z]/.test(value) && 
                   /[A-Z]/.test(value) && 
                   /[0-9]/.test(value) && 
                   /[^a-zA-Z0-9]/.test(value);
        },
        passwordConfirm: (value) => value === passwordInput.value,
        phone: (value) => !value || /^[+]?[0-9\s\-\(\)]{8,20}$/.test(value),
        city: (value) => !value || (value.length >= 2 && /^[a-zA-ZÀ-ÿ\s\-']+$/.test(value)),
        address: (value) => !value || value.length >= 5,
        bio: (value) => !value || (value.length >= 10 && value.length <= 1000)
    };

    const errorMessages = {
        name: 'Le nom doit contenir au moins 2 caractères et uniquement des lettres.',
        email: 'Veuillez saisir une adresse email valide.',
        password: 'Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un symbole.',
        passwordConfirm: 'La confirmation ne correspond pas au mot de passe.',
        phone: 'Le numéro de téléphone n\'est pas valide.',
        city: 'La ville doit contenir au moins 2 caractères et uniquement des lettres.',
        address: 'L\'adresse doit contenir au moins 5 caractères.',
        bio: 'La biographie doit contenir entre 10 et 1000 caractères.'
    };

    // Ajouter les écouteurs d'événements
    if (nameInput) {
        nameInput.addEventListener('blur', () => validateField(nameInput, validations.name, errorMessages.name));
        nameInput.addEventListener('input', () => {
            if (nameInput.classList.contains('is-invalid')) {
                validateField(nameInput, validations.name, errorMessages.name);
            }
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', () => validateField(emailInput, validations.email, errorMessages.email));
        emailInput.addEventListener('input', () => {
            if (emailInput.classList.contains('is-invalid')) {
                validateField(emailInput, validations.email, errorMessages.email);
            }
        });
    }

    if (passwordInput) {
        passwordInput.addEventListener('blur', () => validateField(passwordInput, validations.password, errorMessages.password));
        passwordInput.addEventListener('input', () => {
            if (passwordInput.classList.contains('is-invalid')) {
                validateField(passwordInput, validations.password, errorMessages.password);
            }
            // Revalider la confirmation si elle existe
            if (passwordConfirmInput && passwordConfirmInput.value) {
                validateField(passwordConfirmInput, validations.passwordConfirm, errorMessages.passwordConfirm);
            }
        });
    }

    if (passwordConfirmInput) {
        passwordConfirmInput.addEventListener('blur', () => validateField(passwordConfirmInput, validations.passwordConfirm, errorMessages.passwordConfirm));
        passwordConfirmInput.addEventListener('input', () => {
            if (passwordConfirmInput.classList.contains('is-invalid')) {
                validateField(passwordConfirmInput, validations.passwordConfirm, errorMessages.passwordConfirm);
            }
        });
    }

    if (phoneInput) {
        phoneInput.addEventListener('blur', () => validateField(phoneInput, validations.phone, errorMessages.phone));
    }

    if (cityInput) {
        cityInput.addEventListener('blur', () => validateField(cityInput, validations.city, errorMessages.city));
    }

    if (addressInput) {
        addressInput.addEventListener('blur', () => validateField(addressInput, validations.address, errorMessages.address));
    }

    if (bioInput) {
        bioInput.addEventListener('blur', () => validateField(bioInput, validations.bio, errorMessages.bio));
        bioInput.addEventListener('input', () => {
            const charCount = bioInput.value.length;
            let counter = bioInput.parentNode.querySelector('.char-counter');
            if (!counter) {
                counter = document.createElement('small');
                counter.className = 'char-counter text-muted';
                bioInput.parentNode.appendChild(counter);
            }
            counter.textContent = `${charCount}/1000 caractères`;
            counter.style.color = charCount > 1000 ? '#dc3545' : '#6c757d';
        });
    }

    // Validation avant soumission
    form.addEventListener('submit', function(e) {
        let isFormValid = true;
        
        // Valider tous les champs requis
        if (nameInput && !validateField(nameInput, validations.name, errorMessages.name)) {
            isFormValid = false;
        }
        if (emailInput && !validateField(emailInput, validations.email, errorMessages.email)) {
            isFormValid = false;
        }
        if (passwordInput && !validateField(passwordInput, validations.password, errorMessages.password)) {
            isFormValid = false;
        }
        if (passwordConfirmInput && !validateField(passwordConfirmInput, validations.passwordConfirm, errorMessages.passwordConfirm)) {
            isFormValid = false;
        }
        
        // Valider les champs optionnels s'ils sont remplis
        if (phoneInput && phoneInput.value && !validateField(phoneInput, validations.phone, errorMessages.phone)) {
            isFormValid = false;
        }
        if (cityInput && cityInput.value && !validateField(cityInput, validations.city, errorMessages.city)) {
            isFormValid = false;
        }
        if (addressInput && addressInput.value && !validateField(addressInput, validations.address, errorMessages.address)) {
            isFormValid = false;
        }
        if (bioInput && bioInput.value && !validateField(bioInput, validations.bio, errorMessages.bio)) {
            isFormValid = false;
        }

        if (!isFormValid) {
            e.preventDefault();
            // Faire défiler vers le premier champ invalide
            const firstInvalid = form.querySelector('.is-invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }
    });

    // Indicateur de force du mot de passe
    if (passwordInput) {
        const strengthIndicator = document.createElement('div');
        strengthIndicator.className = 'password-strength mt-2';
        passwordInput.parentNode.appendChild(strengthIndicator);

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            let feedback = [];

            if (password.length >= 8) strength++;
            else feedback.push('Au moins 8 caractères');

            if (/[a-z]/.test(password)) strength++;
            else feedback.push('Une minuscule');

            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('Une majuscule');

            if (/[0-9]/.test(password)) strength++;
            else feedback.push('Un chiffre');

            if (/[^a-zA-Z0-9]/.test(password)) strength++;
            else feedback.push('Un symbole');

            const colors = ['#dc3545', '#fd7e14', '#ffc107', '#20c997', '#28a745'];
            const labels = ['Très faible', 'Faible', 'Moyen', 'Fort', 'Très fort'];

            strengthIndicator.innerHTML = `
                <div class="progress" style="height: 5px;">
                    <div class="progress-bar" style="width: ${strength * 20}%; background-color: ${colors[strength - 1] || '#dc3545'};"></div>
                </div>
                <small style="color: ${colors[strength - 1] || '#dc3545'};">
                    ${labels[strength - 1] || 'Très faible'} ${feedback.length ? '- Manque: ' + feedback.join(', ') : ''}
                </small>
            `;
        });
    }
});
</script>
@endpush
>>>>>>> main
