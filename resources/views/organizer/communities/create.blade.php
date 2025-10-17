@extends('layouts.organizer')

@section('title', 'Créer une Communauté - EcoEvents')
@section('page-title', 'Créer une Communauté')
@section('page-subtitle', 'Lancez votre propre communauté écologique')

@section('header-actions')
    <a href="{{ route('organizer.communities.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-2"></i>Retour
    </a>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-plus-circle me-2"></i>Nouvelle Communauté Écologique
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('organizer.communities.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="row">
                        <!-- Nom de la communauté -->
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-users me-1"></i>Nom de la communauté *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name') }}" 
                                   placeholder="Ex: Jardinage Bio Tunis">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Catégorie -->
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">
                                <i class="fas fa-tag me-1"></i>Catégorie *
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                                <option value="">Choisir une catégorie</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}" {{ old('category') == $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Localisation -->
                        <div class="col-md-6 mb-3">
                            <label for="location" class="form-label">
                                <i class="fas fa-map-marker-alt me-1"></i>Localisation
                            </label>
                            <input type="text" class="form-control @error('location') is-invalid @enderror" 
                                   id="location" name="location" value="{{ old('location') }}" 
                                   placeholder="Ex: Tunis, Ariana, Sfax...">
                            @error('location')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="col-md-12 mb-3">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left me-1"></i>Description *
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Décrivez les objectifs et activités de votre communauté...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum 10 caractères</div>
                        </div>

                        <!-- Nombre maximum de membres -->
                        <div class="col-md-6 mb-3">
                            <label for="max_members" class="form-label">
                                <i class="fas fa-user-friends me-1"></i>Nombre maximum de membres *
                            </label>
                            <input type="number" class="form-control @error('max_members') is-invalid @enderror" 
                                   id="max_members" name="max_members" value="{{ old('max_members', 50) }}" 
                                   min="5" max="1000">
                            @error('max_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Entre 5 et 1000 membres</div>
                        </div>

                        <!-- Image de la communauté -->
                        <div class="col-md-6 mb-3">
                            <label for="image" class="form-label">
                                <i class="fas fa-image me-1"></i>Image de la communauté
                            </label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPG, PNG, GIF - Max 2MB</div>
                        </div>
                    </div>

                    <!-- Aperçu de l'image -->
                    <div class="mb-3" id="image-preview" style="display: none;">
                        <label class="form-label">Aperçu de l'image :</label>
                        <div>
                            <img id="preview-img" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>

                    <!-- Conseils -->
                    <div class="alert alert-info">
                        <h6><i class="fas fa-lightbulb me-2"></i>Conseils pour créer une communauté réussie :</h6>
                        <ul class="mb-0">
                            <li>Choisissez un nom clair et attractif</li>
                            <li>Décrivez précisément vos objectifs écologiques</li>
                            <li>Spécifiez votre zone géographique pour attirer les bonnes personnes</li>
                            <li>Commencez avec un nombre de membres raisonnable</li>
                        </ul>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('organizer.communities.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-eco">
                            <i class="fas fa-save me-2"></i>Créer la Communauté
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('image').addEventListener('change', function(e) {
    const file = e.target.files[0];
    const preview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
});

// Compteur de caractères pour la description
document.getElementById('description').addEventListener('input', function(e) {
    const length = e.target.value.length;
    const formText = e.target.nextElementSibling.nextElementSibling;
    formText.textContent = `${length} caractères (minimum 10)`;
    
    if (length < 10) {
        formText.className = 'form-text text-danger';
    } else {
        formText.className = 'form-text text-success';
    }
});
</script>
@endsection
