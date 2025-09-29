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
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
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
                                    <div class="col-md-4">
                                        <div class="role-option">
                                            <input type="radio" id="role_sponsor" name="role" value="sponsor" 
                                                   {{ old('role') == 'sponsor' ? 'checked' : '' }}>
                                            <label for="role_sponsor" class="role-card">
                                                <div class="role-icon">
                                                    <i class="bi bi-heart"></i>
                                                </div>
                                                <h4>Sponsor</h4>
                                                <p>Je souhaite soutenir financièrement les événements</p>
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
