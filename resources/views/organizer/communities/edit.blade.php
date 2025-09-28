@extends('layouts.organizer')

@section('title', 'Modifier ' . $community->name . ' - EcoEvents')
@section('page-title', 'Modifier la Communauté')
@section('page-subtitle', $community->name)

@section('header-actions')
    <div class="btn-group">
        <a href="{{ route('organizer.communities.show', $community) }}" class="btn btn-outline-primary">
            <i class="fas fa-eye me-2"></i>Voir détails
        </a>
        <a href="{{ route('organizer.communities.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>Modifier la Communauté
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('organizer.communities.update', $community) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <!-- Nom de la communauté -->
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">
                                <i class="fas fa-users me-1"></i>Nom de la communauté *
                            </label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $community->name) }}" 
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
                                    <option value="{{ $key }}" {{ old('category', $community->category) == $key ? 'selected' : '' }}>
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
                                   id="location" name="location" value="{{ old('location', $community->location) }}" 
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
                                      placeholder="Décrivez les objectifs et activités de votre communauté...">{{ old('description', $community->description) }}</textarea>
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
                                   id="max_members" name="max_members" value="{{ old('max_members', $community->max_members) }}" 
                                   min="{{ $community->active_members_count }}" max="1000">
                            @error('max_members')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Minimum {{ $community->active_members_count }} (membres actuels) - Maximum 1000
                            </div>
                        </div>

                        <!-- Statut -->
                        <div class="col-md-6 mb-3">
                            <label for="is_active" class="form-label">
                                <i class="fas fa-toggle-on me-1"></i>Statut de la communauté
                            </label>
                            <select class="form-select @error('is_active') is-invalid @enderror" id="is_active" name="is_active">
                                <option value="1" {{ old('is_active', $community->is_active) == '1' ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="0" {{ old('is_active', $community->is_active) == '0' ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                            @error('is_active')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Une communauté inactive n'accepte plus de nouveaux membres
                            </div>
                        </div>

                        <!-- Image actuelle -->
                        @if($community->image)
                            <div class="col-md-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-image me-1"></i>Image actuelle
                                </label>
                                <div class="mb-2">
                                    <img src="{{ $community->image_url }}" alt="{{ $community->name }}" 
                                         class="img-thumbnail" style="max-width: 200px;">
                                </div>
                            </div>
                        @endif

                        <!-- Nouvelle image -->
                        <div class="col-md-12 mb-3">
                            <label for="image" class="form-label">
                                <i class="fas fa-image me-1"></i>{{ $community->image ? 'Changer l\'image' : 'Ajouter une image' }}
                            </label>
                            <input type="file" class="form-control @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">JPG, PNG, GIF - Max 2MB</div>
                        </div>
                    </div>

                    <!-- Aperçu de la nouvelle image -->
                    <div class="mb-3" id="image-preview" style="display: none;">
                        <label class="form-label">Aperçu de la nouvelle image :</label>
                        <div>
                            <img id="preview-img" src="" alt="Aperçu" class="img-thumbnail" style="max-width: 200px;">
                        </div>
                    </div>

                    <!-- Informations importantes -->
                    <div class="alert alert-warning">
                        <h6><i class="fas fa-exclamation-triangle me-2"></i>Informations importantes :</h6>
                        <ul class="mb-0">
                            <li>Vous ne pouvez pas réduire le nombre maximum de membres en dessous du nombre actuel ({{ $community->active_members_count }})</li>
                            <li>Désactiver la communauté empêchera de nouveaux membres de la rejoindre</li>
                            <li>Les modifications seront visibles immédiatement pour tous les membres</li>
                        </ul>
                    </div>

                    <!-- Boutons -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('organizer.communities.show', $community) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-save me-2"></i>Enregistrer les modifications
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Zone de danger -->
        @if($community->active_members_count == 0)
            <div class="card mt-4 border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Zone de danger
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        Cette communauté n'a aucun membre actif. Vous pouvez la supprimer définitivement si vous le souhaitez.
                    </p>
                    <form action="{{ route('organizer.communities.destroy', $community) }}" method="POST" 
                          onsubmit="return confirm('Êtes-vous absolument sûr de vouloir supprimer cette communauté ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-2"></i>Supprimer définitivement
                        </button>
                    </form>
                </div>
            </div>
        @endif
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

// Validation du nombre de membres
document.getElementById('max_members').addEventListener('input', function(e) {
    const currentMembers = {{ $community->active_members_count }};
    const newMax = parseInt(e.target.value);
    const formText = e.target.nextElementSibling.nextElementSibling;
    
    if (newMax < currentMembers) {
        e.target.classList.add('is-invalid');
        formText.className = 'form-text text-danger';
        formText.textContent = `Impossible ! Vous avez déjà ${currentMembers} membres actifs.`;
    } else {
        e.target.classList.remove('is-invalid');
        formText.className = 'form-text text-muted';
        formText.textContent = `Minimum ${currentMembers} (membres actuels) - Maximum 1000`;
    }
});
</script>
@endsection
