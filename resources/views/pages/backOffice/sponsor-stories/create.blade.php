@extends('layouts.sponsor')

@section('title', 'Créer une Story - Echofy Sponsor')

@section('content')
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title">
                        <i class="fas fa-plus-circle text-primary"></i> Créer une Story
                    </h1>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('sponsor.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('sponsor.stories.my-stories') }}">Mes Stories</a></li>
                            <li class="breadcrumb-item active">Créer une Story</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <a href="{{ route('sponsor.stories.my-stories') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Retour
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Formulaire de création simplifié -->
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-camera"></i> Nouvelle Story
                    </h6>
                </div>
                <div class="card-body">
                    <form id="storyForm" method="POST" action="{{ route('sponsor.stories.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Type de média -->
                        <div class="form-group">
                            <label for="media_type" class="font-weight-bold">Type de contenu *</label>
                            <select class="form-control" id="media_type" name="media_type" required>
                                <option value="text">Texte uniquement</option>
                                <option value="image">Image</option>
                                <option value="video">Vidéo</option>
                            </select>
                            <small class="form-text text-muted">Choisissez le type de contenu principal de votre story</small>
                        </div>

                        <!-- Upload de fichier -->
                        <div class="form-group" id="media_file_group" style="display: none;">
                            <label for="media_file" class="font-weight-bold">Fichier média</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="media_file" name="media_file" accept="image/*,video/*">
                                <label class="custom-file-label" for="media_file">Choisir un fichier...</label>
                            </div>
                            <small class="form-text text-muted">
                                Formats acceptés: JPG, PNG, GIF, MP4, MOV, AVI (max 10MB)
                            </small>
                        </div>

                        <!-- Titre -->
                        <div class="form-group">
                            <label for="title" class="font-weight-bold">Titre (optionnel)</label>
                            <input type="text" class="form-control" id="title" name="title" maxlength="100" placeholder="Titre de votre story...">
                            <small class="form-text text-muted">Maximum 100 caractères</small>
                        </div>

                        <!-- Contenu -->
                        <div class="form-group">
                            <label for="content" class="font-weight-bold">Contenu *</label>
                            <textarea class="form-control" id="content" name="content" rows="4" maxlength="500" placeholder="Racontez votre histoire..." required></textarea>
                            <small class="form-text text-muted">
                                <span id="content_counter">0</span>/500 caractères
                            </small>
                        </div>

                        <!-- Événement associé -->
                        <div class="form-group">
                            <label for="event_id" class="font-weight-bold">Événement associé (optionnel)</label>
                            <select class="form-control" id="event_id" name="event_id">
                                <option value="">Aucun événement spécifique</option>
                                @foreach($sponsoredEvents as $event)
                                    <option value="{{ $event->id }}">{{ $event->title }} - {{ \Carbon\Carbon::parse($event->date)->format('d/m/Y') }}</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">Associez votre story à un événement sponsorisé</small>
                        </div>

                        <!-- Options de style -->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="background_color" class="font-weight-bold">Couleur de fond</label>
                                    <input type="color" class="form-control" id="background_color" name="background_color" value="#3498db">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="text_color" class="font-weight-bold">Couleur du texte</label>
                                    <input type="color" class="form-control" id="text_color" name="text_color" value="#ffffff">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="font_size" class="font-weight-bold">Taille du texte</label>
                                    <select class="form-control" id="font_size" name="font_size">
                                        <option value="small">Petit</option>
                                        <option value="medium" selected>Moyen</option>
                                        <option value="large">Grand</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="form-group text-right">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-save"></i> Créer la Story
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Aperçu en temps réel -->
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-eye"></i> Aperçu
                    </h6>
                </div>
                <div class="card-body">
                    <div id="story_preview" class="story-preview">
                        <div class="preview-placeholder text-center py-5">
                            <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Votre story apparaîtra ici</p>
                        </div>
                    </div>
                    
                    <!-- Informations sur la story -->
                    <div class="mt-3">
                        <h6 class="font-weight-bold">Informations</h6>
                        <ul class="list-unstyled small text-muted">
                            <li><i class="fas fa-clock"></i> Durée: 24 heures</li>
                            <li><i class="fas fa-eye"></i> Visible par tous</li>
                            <li><i class="fas fa-heart"></i> Possibilité de likes</li>
                            <li><i class="fas fa-star"></i> Peut être mise en vedette</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Variables globales
let previewTimeout = null;

// Initialisation
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Initialisation du formulaire de création de story');
    
    // Écouter les changements pour l'aperçu en temps réel
    const form = document.getElementById('storyForm');
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });

    // Compteur de caractères pour le contenu
    const contentTextarea = document.getElementById('content');
    const contentCounter = document.getElementById('content_counter');
    
    contentTextarea.addEventListener('input', function() {
        contentCounter.textContent = this.value.length;
    });

    // Gestion du type de média
    const mediaTypeSelect = document.getElementById('media_type');
    const mediaFileGroup = document.getElementById('media_file_group');
    
    mediaTypeSelect.addEventListener('change', function() {
        console.log('📝 Type de média changé:', this.value);
        if (this.value === 'image' || this.value === 'video') {
            mediaFileGroup.style.display = 'block';
            document.getElementById('media_file').required = true;
        } else {
            mediaFileGroup.style.display = 'none';
            document.getElementById('media_file').required = false;
        }
        updatePreview();
    });

    // Gestion de l'upload de fichier
    const mediaFileInput = document.getElementById('media_file');
    mediaFileInput.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            console.log('📁 Fichier sélectionné:', file.name, 'Taille:', file.size);
            const label = this.nextElementSibling;
            label.textContent = file.name;
            
            // Vérifier la taille du fichier
            if (file.size > 10 * 1024 * 1024) { // 10MB
                showAlert('danger', 'Le fichier est trop volumineux. Taille maximum: 10MB');
                this.value = '';
                label.textContent = 'Choisir un fichier...';
                return;
            }
            
            updatePreview();
        }
    });
    
    console.log('✅ Formulaire initialisé avec succès');
});

// Fonction pour mettre à jour l'aperçu
function updatePreview() {
    clearTimeout(previewTimeout);
    previewTimeout = setTimeout(() => {
        generatePreview();
    }, 300);
}

// Fonction pour générer l'aperçu
function generatePreview() {
    const mediaType = document.getElementById('media_type').value;
    const title = document.getElementById('title').value;
    const content = document.getElementById('content').value;
    const backgroundColor = document.getElementById('background_color').value;
    const textColor = document.getElementById('text_color').value;
    const fontSize = document.getElementById('font_size').value;
    const mediaFile = document.getElementById('media_file').files[0];

    const preview = document.getElementById('story_preview');
    
    if (!content.trim()) {
        preview.innerHTML = `
            <div class="preview-placeholder text-center py-5">
                <i class="fas fa-camera fa-3x text-muted mb-3"></i>
                <p class="text-muted">Votre story apparaîtra ici</p>
            </div>
        `;
        return;
    }

    let mediaHtml = '';
    
    if (mediaType === 'image' && mediaFile) {
        const reader = new FileReader();
        reader.onload = function(e) {
            mediaHtml = `<img src="${e.target.result}" class="preview-image" alt="Preview">`;
            updatePreviewContent(mediaHtml, title, content, backgroundColor, textColor, fontSize);
        };
        reader.readAsDataURL(mediaFile);
    } else if (mediaType === 'video' && mediaFile) {
        const reader = new FileReader();
        reader.onload = function(e) {
            mediaHtml = `<video class="preview-video" controls><source src="${e.target.result}" type="video/mp4"></video>`;
            updatePreviewContent(mediaHtml, title, content, backgroundColor, textColor, fontSize);
        };
        reader.readAsDataURL(mediaFile);
    } else {
        updatePreviewContent(mediaHtml, title, content, backgroundColor, textColor, fontSize);
    }
}

// Fonction pour mettre à jour le contenu de l'aperçu
function updatePreviewContent(mediaHtml, title, content, backgroundColor, textColor, fontSize) {
    const preview = document.getElementById('story_preview');
    
    const fontSizeClass = fontSize === 'large' ? '1.5rem' : (fontSize === 'small' ? '1rem' : '1.25rem');
    const contentSizeClass = fontSize === 'large' ? '1.1rem' : (fontSize === 'small' ? '0.9rem' : '1rem');
    
    preview.innerHTML = `
        <div class="story-preview-content" style="background: linear-gradient(135deg, ${backgroundColor}, ${backgroundColor}dd); color: ${textColor};">
            ${mediaHtml}
            <div class="preview-text-content p-3">
                ${title ? `<h4 class="mb-3" style="font-size: ${fontSizeClass};">${title}</h4>` : ''}
                <p class="mb-0" style="font-size: ${contentSizeClass};">${content}</p>
            </div>
        </div>
    `;
}

// Soumission du formulaire avec gestion d'erreur améliorée
document.getElementById('storyForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    console.log('📤 Soumission du formulaire de story');
    
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;
    
    // Désactiver le bouton et afficher le loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
    
    try {
        const formData = new FormData(this);
        
        // Log des données envoyées
        console.log('📋 Données du formulaire:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}:`, value);
        }
        
        const response = await fetch('{{ route("sponsor.stories.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        });
        
        console.log('📡 Réponse reçue:', response.status, response.statusText);
        
        const data = await response.json();
        console.log('📄 Données de réponse:', data);
        
        if (data.success) {
            showAlert('success', data.message);
            console.log('✅ Story créée avec succès !');
            console.log('🔄 Redirection vers:', data.data.redirect_url);
            setTimeout(() => {
                window.location.href = data.data.redirect_url;
            }, 1000);
        } else {
            console.error('❌ Erreur de création:', data);
            showAlert('danger', data.message || 'Erreur lors de la création');
            
            // Afficher les erreurs de validation
            if (data.errors) {
                console.error('🔍 Erreurs de validation:', data.errors);
                Object.keys(data.errors).forEach(field => {
                    const input = document.querySelector(`[name="${field}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const errorDiv = document.createElement('div');
                        errorDiv.className = 'invalid-feedback';
                        errorDiv.textContent = data.errors[field][0];
                        input.parentNode.appendChild(errorDiv);
                    }
                });
            }
        }
    } catch (error) {
        console.error('💥 Erreur lors de la soumission:', error);
        showAlert('danger', 'Erreur lors de la création de la story: ' + error.message);
    } finally {
        // Réactiver le bouton
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    }
});

// Fonction pour afficher les alertes
function showAlert(type, message) {
    console.log(`📢 Alerte ${type}:`, message);
    
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    `;
    
    const container = document.querySelector('.container-fluid');
    container.insertAdjacentHTML('afterbegin', alertHtml);
    
    setTimeout(() => {
        const alert = container.querySelector('.alert');
        if (alert) {
            alert.remove();
        }
    }, 5000);
}
</script>

<style>
.story-preview {
    height: 300px;
    border-radius: 15px;
    overflow: hidden;
    border: 2px solid #e3e6f0;
}

.story-preview-content {
    height: 100%;
    position: relative;
    display: flex;
    flex-direction: column;
}

.preview-image, .preview-video {
    width: 100%;
    height: 60%;
    object-fit: cover;
}

.preview-text-content {
    height: 40%;
    display: flex;
    flex-direction: column;
    justify-content: center;
}

.preview-placeholder {
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
}

.custom-file-label::after {
    content: "Parcourir";
}

.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875rem;
    color: #dc3545;
}

@media (max-width: 768px) {
    .story-preview {
        height: 250px;
    }
}
</style>
@endsection