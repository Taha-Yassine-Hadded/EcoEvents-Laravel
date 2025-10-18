@extends('layouts.admin')

@section('title', 'Créer une Campagne - Echofy')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Créer une Nouvelle Campagne</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.campaigns.index') }}">Campagnes</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Formulaire de Création de Campagne</h6>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="form-header">
                        <h2>Créer une nouvelle campagne</h2>
                        <p>Partagez votre initiative environnementale avec la communauté</p>
                    </div>

                    <div class="step-indicator">
                        <div class="step active">
                            <div class="step-number">1</div>
                            <span>Informations de base</span>
                        </div>
                        <div class="step">
                            <div class="step-number">2</div>
                            <span>Contenu détaillé</span>
                        </div>
                        <div class="step">
                            <div class="step-number">3</div>
                            <span>Médias et publication</span>
                        </div>
                    </div>

                    <div class="progress-bar">
                        <div class="progress-fill" id="progressFill"></div>
                    </div>

                    <form id="campaignForm" method="POST" action="{{ route('admin.campaigns.store') }}" enctype="multipart/form-data">
                        @csrf
                        <!-- Section 1: Informations de base -->
                        <div class="form-section" id="section1">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informations de base
                            </div>

                            <div class="form-group">
                                <label for="title">Titre de la campagne <span class="required">*</span></label>
                                <input type="text" id="title" name="title" class="form-control" required>
                                <div class="character-count"><span id="titleCount">0</span>/100 caractères</div>
                                <div class="error-message" id="titleError"></div>
                            </div>







                            <div class="form-row">
                                <div class="form-group">
                                    <label for="start_date">Date de début <span class="required">*</span></label>
                                    <input type="date" id="start_date" name="start_date" class="form-control" required>
                                    <div class="error-message" id="startDateError"></div>
                                </div>
                                <div class="form-group">
                                    <label for="end_date">Date de fin <span class="required">*</span></label>
                                    <input type="date" id="end_date" name="end_date" class="form-control" required>
                                    <div class="error-message" id="endDateError"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Contenu détaillé -->
                        <div class="form-section" id="section2" style="display: none;">
                            <div class="section-title">
                                <i class="fas fa-edit"></i>
                                Contenu détaillé
                            </div>

                            <div class="form-group">
                                <label for="content">Contenu complet de la campagne <span class="required">*</span></label>
                                <textarea id="content" name="content" class="form-control" rows="10" placeholder="Décrivez en détail votre campagne : contexte, enjeux, impact attendu..." required></textarea>
                                <div class="character-count"><span id="contentCount">0</span>/2000 caractères</div>
                                <div class="error-message" id="contentError"></div>
                            </div>

                            <div class="form-group">
                                <label>Objectifs principaux</label>
                                <div id="objectivesList">
                                    <div class="objective-item">
                                        <input type="text" name="objectives[]" class="form-control" placeholder="Objectif 1...">
                                        <button type="button" class="remove-objective" onclick="removeObjective(this)" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline" onclick="addObjective()" style="margin-top: 0.5rem;">
                                    <i class="fas fa-plus"></i> Ajouter un objectif
                                </button>
                            </div>

                            <div class="form-group">
                                <label>Actions concrètes</label>
                                <div id="actionsList">
                                    <div class="action-item">
                                        <input type="text" name="actions[]" class="form-control" placeholder="Action 1...">
                                        <button type="button" class="remove-action" onclick="removeAction(this)" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline" onclick="addAction()" style="margin-top: 0.5rem;">
                                    <i class="fas fa-plus"></i> Ajouter une action
                                </button>
                            </div>
                        </div>

                        <!-- Section 3: Médias et publication -->
                        <div class="form-section" id="section3" style="display: none;">
                            <div class="section-title">
                                <i class="fas fa-images"></i>
                                Médias et publication
                            </div>

                            <div class="form-group">
                                <label for="media">Images de la campagne</label>
                                <div class="file-upload">
                                    <input type="file" id="media" name="media[]" multiple accept="image/*">
                                    <label for="media" class="file-upload-label">
                                        <div class="file-upload-icon">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                        </div>
                                        <div class="file-upload-text">
                                            Cliquez pour sélectionner des images ou glissez-déposez ici
                                        </div>
                                        <div class="file-upload-hint">
                                            Formats acceptés: JPG, PNG, GIF (max. 5MB par image)
                                        </div>
                                    </label>
                                </div>
                                <div class="uploaded-files" id="uploadedFiles"></div>
                            </div>

                            <div class="form-group">
                                <label>Liens vidéo (optionnel)</label>
                                <div id="videosList">
                                    <div class="video-item">
                                        <input type="url" name="video_urls[]" class="form-control" placeholder="https://www.youtube.com/watch?v=...">
                                        <button type="button" class="remove-video" onclick="removeVideo(this)" style="display: none;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline" onclick="addVideo()" style="margin-top: 0.5rem;">
                                    <i class="fas fa-plus"></i> Ajouter un lien vidéo
                                </button>
                                <div class="form-text">Ajoutez des liens YouTube, Vimeo ou autres pour enrichir votre campagne</div>
                                <div class="error-message" id="videoUrlsError"></div>
                            </div>

                            <div class="form-group">
                                <label for="website_url">Site web ou page dédiée (optionnel)</label>
                                <input type="url" id="website_url" name="website_url" class="form-control" placeholder="https://votresite.com/campagne">
                                <div class="error-message" id="websiteUrlError"></div>
                            </div>

                            <div class="form-group">
                                <label for="contact_info">Informations de contact</label>
                                <textarea id="contact_info" name="contact_info" class="form-control" rows="3" placeholder="Email, téléphone, réseaux sociaux pour que les participants puissent vous contacter..."></textarea>
                                <div class="character-count"><span id="contactInfoCount">0</span>/1000 caractères</div>
                                <div class="error-message" id="contactInfoError"></div>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" id="terms" name="terms" required style="margin-right: 0.5rem;">
                                    J'accepte les <a href="#" target="_blank">conditions d'utilisation</a> et confirme que cette campagne respecte les valeurs environnementales d'Echofy <span class="required">*</span>
                                </label>
                                <div class="error-message" id="termsError"></div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-secondary" id="prevBtn" onclick="prevStep()" style="display: none;">
                                <i class="fas fa-arrow-left"></i> Précédent
                            </button>
                            <div style="flex: 1;"></div>
                            <button type="button" class="btn btn-outline" onclick="cancelForm()">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                            <button type="button" class="btn btn-primary" id="nextBtn" onclick="nextStep()">
                                Suivant <i class="fas fa-arrow-right"></i>
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn" style="display: none;">
                                <i class="fas fa-rocket"></i> Publier la campagne
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .form-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 1rem;
        }

        .form-group .required {
            color: #dc3545;
        }

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .form-control.error {
            border-color: #dc3545;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        textarea.form-control {
            resize: vertical;
            min-height: 120px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .select-wrapper {
            position: relative;
        }

        .select-wrapper::after {
            content: '\f107';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            top: 50%;
            right: 1rem;
            transform: translateY(-50%);
            color: #28a745;
            pointer-events: none;
        }

        select.form-control {
            appearance: none;
            background: white;
            cursor: pointer;
            padding-right: 2.5rem;
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .file-upload-label {
            display: block;
            width: 100%;
            padding: 2rem;
            border: 2px dashed #28a745;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(40, 167, 69, 0.05);
        }

        .file-upload-label:hover {
            background: rgba(40, 167, 69, 0.1);
            border-color: #218838;
        }

        .file-upload-icon {
            font-size: 2rem;
            color: #28a745;
            margin-bottom: 0.5rem;
        }

        .file-upload-text {
            color: #666;
            font-size: 1rem;
        }

        .file-upload-hint {
            color: #888;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }

        .uploaded-files, .videosList, .objectivesList, .actionsList {
            margin-top: 1rem;
        }

        .file-item, .video-item, .objective-item, .action-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.5rem;
            background: #f8f9fa;
            border-radius: 5px;
            margin-bottom: 0.5rem;
            gap: 0.5rem;
        }

        .file-info {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .remove-file, .remove-video, .remove-objective, .remove-action {
            background: none;
            border: none;
            color: #dc3545;
            cursor: pointer;
            font-size: 1.1rem;
        }

        .form-section {
            margin-bottom: 2rem;
            padding-bottom: 2rem;
            border-bottom: 1px solid #eee;
        }

        .form-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .section-title {
            color: #28a745;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-outline {
            background: white;
            color: #28a745;
            border: 2px solid #28a745;
        }

        .btn-outline:hover {
            background: #28a745;
            color: white;
        }

        .progress-bar {
            width: 100%;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            margin: 1rem 0;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #28a745, #20c997);
            width: 33.33%;
            transition: all 0.3s ease;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
        }

        .step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            color: #666;
            font-weight: 500;
        }

        .step.active {
            color: #28a745;
        }

        .step-number {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .step.active .step-number {
            background: #28a745;
            color: white;
        }

        .character-count {
            text-align: right;
            color: #666;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .character-count.limit {
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let currentStep = 1;
        const totalSteps = 3;
        const uploadedFiles = [];

        // Gestion des étapes
        function nextStep() {
            if (validateCurrentStep()) {
                if (currentStep < totalSteps) {
                    currentStep++;
                    updateStepDisplay();
                }
            }
        }

        function prevStep() {
            if (currentStep > 1) {
                currentStep--;
                updateStepDisplay();
            }
        }

        function updateStepDisplay() {
            // Masquer toutes les sections
            for (let i = 1; i <= totalSteps; i++) {
                document.getElementById(`section${i}`).style.display = 'none';
            }

            // Afficher la section actuelle
            document.getElementById(`section${currentStep}`).style.display = 'block';

            // Mettre à jour les indicateurs d'étapes
            document.querySelectorAll('.step').forEach((step, index) => {
                if (index + 1 <= currentStep) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });

            // Mettre à jour la barre de progression
            const progressPercent = (currentStep / totalSteps) * 100;
            document.getElementById('progressFill').style.width = `${progressPercent}%`;

            // Gérer les boutons
            document.getElementById('prevBtn').style.display = currentStep === 1 ? 'none' : 'block';
            document.getElementById('nextBtn').style.display = currentStep === totalSteps ? 'none' : 'block';
            document.getElementById('submitBtn').style.display = currentStep === totalSteps ? 'block' : 'none';
        }

        // Validation des étapes
        function validateCurrentStep() {
            let isValid = true;

            if (currentStep === 1) {
                isValid = validateStep1();
            } else if (currentStep === 2) {
                isValid = validateStep2();
            } else if (currentStep === 3) {
                isValid = validateStep3();
            }

            return isValid;
        }





function validateStep1() {
    let isValid = true;

    // Validation du titre
    const title = document.getElementById('title');
    if (!title.value.trim()) {
        showError('titleError', 'Le titre est requis');
        isValid = false;
    } else if (title.value.length > 100) {
        showError('titleError', 'Le titre ne peut pas dépasser 100 caractères');
        isValid = false;
    } else {
        hideError('titleError');
    }

    // Validation des dates
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    const today = new Date().toISOString().split('T')[0];

    if (!startDate.value) {
        showError('startDateError', 'La date de début est requise');
        isValid = false;
    } else if (startDate.value < today) {
        showError('startDateError', 'La date de début ne peut pas être dans le passé');
        isValid = false;
    } else {
        hideError('startDateError');
    }

    if (!endDate.value) {
        showError('endDateError', 'La date de fin est requise');
        isValid = false;
    } else if (endDate.value <= startDate.value) {
        showError('endDateError', 'La date de fin doit être postérieure à la date de début');
        isValid = false;
    } else {
        hideError('endDateError');
    }

    return isValid;
}






        function validateStep2() {
            const content = document.getElementById('content');
            if (!content.value.trim()) {
                showError('contentError', 'Le contenu détaillé est requis');
                return false;
            } else if (content.value.length > 2000) {
                showError('contentError', 'Le contenu ne peut pas dépasser 2000 caractères');
                return false;
            } else {
                hideError('contentError');
                return true;
            }
        }

        function validateStep3() {
            let isValid = true;

            // Validation des URLs vidéo
            const videoInputs = document.querySelectorAll('#videosList input[name="video_urls[]"]');
            for (let input of videoInputs) {
                if (input.value && !isValidUrl(input.value)) {
                    showError('videoUrlsError', 'Une ou plusieurs URLs vidéo ne sont pas valides');
                    isValid = false;
                    break;
                }
            }
            if (isValid) {
                hideError('videoUrlsError');
            }

            // Validation de l'URL du site web
            const websiteUrl = document.getElementById('website_url');
            if (websiteUrl.value && !isValidUrl(websiteUrl.value)) {
                showError('websiteUrlError', 'L\'URL du site web n\'est pas valide');
                isValid = false;
            } else {
                hideError('websiteUrlError');
            }

            // Validation des informations de contact
            const contactInfo = document.getElementById('contact_info');
            if (contactInfo.value.length > 1000) {
                showError('contactInfoError', 'Les informations de contact ne peuvent pas dépasser 1000 caractères');
                isValid = false;
            } else {
                hideError('contactInfoError');
            }

            // Validation des conditions d'utilisation
            const terms = document.getElementById('terms');
            if (!terms.checked) {
                showError('termsError', 'Vous devez accepter les conditions d\'utilisation');
                isValid = false;
            } else {
                hideError('termsError');
            }

            return isValid;
        }

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            errorElement.style.display = 'none';
        }

        // Compteurs de caractères
        function setupCharacterCounters() {
            const counters = [
                { input: 'title', counter: 'titleCount', max: 100 },
                { input: 'content', counter: 'contentCount', max: 2000 },
                { input: 'contact_info', counter: 'contactInfoCount', max: 1000 }
            ];

            counters.forEach(({ input, counter, max }) => {
                const inputElement = document.getElementById(input);
                const counterElement = document.getElementById(counter);

                inputElement.addEventListener('input', function() {
                    const count = this.value.length;
                    counterElement.textContent = count;
                    counterElement.parentElement.classList.toggle('limit', count > max);
                });
            });
        }

        // Gestion des objectifs
        function addObjective() {
            const list = document.getElementById('objectivesList');
            const div = document.createElement('div');
            div.className = 'objective-item';
            div.innerHTML = `
                <input type="text" name="objectives[]" class="form-control" placeholder="Objectif ${list.children.length + 1}...">
                <button type="button" class="remove-objective" onclick="removeObjective(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(div);
            updateRemoveButtons('objectivesList', '.remove-objective');
        }

        function removeObjective(button) {
            button.parentElement.remove();
            updateRemoveButtons('objectivesList', '.remove-objective');
        }

        // Gestion des actions
        function addAction() {
            const list = document.getElementById('actionsList');
            const div = document.createElement('div');
            div.className = 'action-item';
            div.innerHTML = `
                <input type="text" name="actions[]" class="form-control" placeholder="Action ${list.children.length + 1}...">
                <button type="button" class="remove-action" onclick="removeAction(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(div);
            updateRemoveButtons('actionsList', '.remove-action');
        }

        function removeAction(button) {
            button.parentElement.remove();
            updateRemoveButtons('actionsList', '.remove-action');
        }

        // Gestion des URLs vidéo
        function addVideo() {
            const list = document.getElementById('videosList');
            const div = document.createElement('div');
            div.className = 'video-item';
            div.innerHTML = `
                <input type="url" name="video_urls[]" class="form-control" placeholder="Lien vidéo ${list.children.length + 1}...">
                <button type="button" class="remove-video" onclick="removeVideo(this)">
                    <i class="fas fa-times"></i>
                </button>
            `;
            list.appendChild(div);
            updateRemoveButtons('videosList', '.remove-video');
        }

        function removeVideo(button) {
            button.parentElement.remove();
            updateRemoveButtons('videosList', '.remove-video');
        }

        function updateRemoveButtons(listId, buttonSelector) {
            const list = document.getElementById(listId);
            const buttons = list.querySelectorAll(buttonSelector);
            buttons.forEach((button, index) => {
                button.style.display = list.children.length > 1 ? 'block' : 'none';
            });
        }

        // Gestion des fichiers
        function setupFileUpload() {
            const fileInput = document.getElementById('media');
            const uploadedFilesDiv = document.getElementById('uploadedFiles');

            fileInput.addEventListener('change', function(e) {
                Array.from(e.target.files).forEach(file => {
                    if (file.size <= 5 * 1024 * 1024) { // 5MB max
                        uploadedFiles.push(file);
                        addFileToDisplay(file);
                    } else {
                        alert(`Le fichier ${file.name} est trop volumineux (max. 5MB)`);
                    }
                });
                fileInput.value = ''; // Reset input
            });
        }

        function addFileToDisplay(file) {
            const uploadedFilesDiv = document.getElementById('uploadedFiles');
            const fileDiv = document.createElement('div');
            fileDiv.className = 'file-item';
            fileDiv.innerHTML = `
                <div class="file-info">
                    <i class="fas fa-image"></i>
                    <span>${file.name}</span>
                    <small>(${formatFileSize(file.size)})</small>
                </div>
                <button type="button" class="remove-file" onclick="removeFile(this, '${file.name}')">
                    <i class="fas fa-trash"></i>
                </button>
            `;
            uploadedFilesDiv.appendChild(fileDiv);
        }

        function removeFile(button, fileName) {
            const index = uploadedFiles.findIndex(file => file.name === fileName);
            if (index > -1) {
                uploadedFiles.splice(index, 1);
            }
            button.parentElement.remove();
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        // Annuler le formulaire
        function cancelForm() {
            if (confirm('Êtes-vous sûr de vouloir annuler ? Vos modifications seront perdues.')) {
                window.location.href = '{{ route("admin.campaigns.index") }}';
            }
        }

        // Soumission du formulaire
        document.getElementById('campaignForm').addEventListener('submit', function(e) {
            e.preventDefault();

            if (!validateCurrentStep()) {
                return;
            }

            const formData = new FormData(this);

            // Ajouter les fichiers uploadés
            uploadedFiles.forEach(file => {
                formData.append('media[]', file);
            });

            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Publication en cours...';
            submitBtn.disabled = true;

            const token = localStorage.getItem('jwt_token');
            if (!token) {
                alert('Vous devez être connecté pour publier une campagne.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                window.location.href = '{{ route("login") }}';
                return;
            }

            fetch('{{ route("admin.campaigns.store") }}', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Campagne publiée avec succès ! Elle sera visible après validation par nos modérateurs.');
                        window.location.href = '{{ route("admin.campaigns.index") }}';
                    } else {
                        alert(data.error || 'Erreur lors de la publication de la campagne');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la publication');
                })
                .finally(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            setupCharacterCounters();
            setupFileUpload();
            updateStepDisplay();

            // Définir les dates minimales
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('start_date').min = today;
            document.getElementById('end_date').min = today;
        });

        // Mettre à jour la date de fin minimale quand la date de début change
        document.getElementById('start_date').addEventListener('change', function() {
            document.getElementById('end_date').min = this.value;
        });
    </script>
@endpush
