@extends('layouts.admin')

@section('title', 'Gestion de la Campagne - Echofy')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Gestion de la Campagne</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.campaigns.index') }}">Campagnes</a></li>
                <li class="breadcrumb-item active">{{ Str::limit($campaign->title, 30) }}</li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="campaign-header">
            <div class="edit-mode-toggle">
                <button class="toggle-btn btn-edit" id="editBtn" onclick="toggleEditMode()">
                    <i class="fas fa-edit"></i>
                    Modifier
                </button>
                <button class="toggle-btn btn-save" id="saveBtn" onclick="saveChanges()">
                    <i class="fas fa-save"></i>
                    Enregistrer
                </button>
                <button class="toggle-btn btn-cancel" id="cancelBtn" onclick="cancelEdit()">
                    <i class="fas fa-times"></i>
                    Annuler
                </button>
            </div>

            <div class="campaign-meta">
                <div class="status-info">
                    <span class="status-badge status-{{ $campaign->status }}" id="statusBadge">
                        {{ $campaign->status == 'upcoming' ? 'À venir' : ($campaign->status == 'active' ? 'Active' : 'Terminée') }}
                    </span>
                    <div class="select-wrapper" id="categoryWrapper" style="display: none;">
                        <select class="form-control editable" id="categorySelect" data-field="category" disabled>
                            <option value="recyclage" {{ $campaign->category == 'recyclage' ? 'selected' : '' }}>Recyclage</option>
                            <option value="climat" {{ $campaign->category == 'climat' ? 'selected' : '' }}>Climat</option>
                            <option value="biodiversite" {{ $campaign->category == 'biodiversite' ? 'selected' : '' }}>Biodiversité</option>
                            <option value="eau" {{ $campaign->category == 'eau' ? 'selected' : '' }}>Ressources en eau</option>
                            <option value="energie" {{ $campaign->category == 'energie' ? 'selected' : '' }}>Énergie renouvelable</option>
                            <option value="transport" {{ $campaign->category == 'transport' ? 'selected' : '' }}>Transport durable</option>
                            <option value="alimentation" {{ $campaign->category == 'alimentation' ? 'selected' : '' }}>Alimentation durable</option>
                            <option value="pollution" {{ $campaign->category == 'pollution' ? 'selected' : '' }}>Lutte contre la pollution</option>
                            <option value="sensibilisation" {{ $campaign->category == 'sensibilisation' ? 'selected' : '' }}>Sensibilisation générale</option>
                        </select>
                    </div>
                    <span class="category-badge" id="categoryBadge">{{ ucfirst($campaign->category) }}</span>
                </div>
            </div>

            <div class="campaign-image-section">
                <div class="campaign-image">
                    <img src="{{ !empty($campaign->media_urls['images'][0]) && Storage::disk('public')->exists($campaign->media_urls['images'][0]) ? Storage::url($campaign->media_urls['images'][0]) : 'https://via.placeholder.com/800x300?text=Image' }}" alt="{{ $campaign->title }}" id="campaignImage">
                    <div class="image-upload-overlay" id="imageUploadOverlay">
                        <div>
                            <i class="fas fa-camera"></i>
                            <div>Cliquer pour changer l'image</div>
                        </div>
                    </div>
                    <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                </div>
            </div>

            <h1 class="editable" id="campaignTitle" data-field="title" contenteditable="false">
                {{ $campaign->title }}
                <div class="edit-indicator">
                    <i class="fas fa-edit"></i>
                </div>
            </h1>
        </div>

        <div class="content-grid">
            <div class="main-content">
                <div class="form-group">
                    <label for="description">Description courte</label>
                    <textarea class="form-control editable" id="description" data-field="description" disabled>{{ strip_tags($campaign->content, 300) }}</textarea>
                    <div class="character-count"><span id="descriptionCount">{{ strlen(strip_tags($campaign->content)) }}</span>/300 caractères</div>
                </div>

                <div class="form-group">
                    <label for="content">Contenu détaillé</label>
                    <textarea class="form-control editable" id="content" data-field="content" rows="10" disabled>{{ $campaign->content }}</textarea>
                    <div class="character-count"><span id="contentCount">{{ strlen($campaign->content) }}</span>/2000 caractères</div>
                </div>
            </div>

            <div class="sidebar-content">
                <!-- Statistiques -->
                <div class="info-card">
                    <div class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Statistiques d'engagement
                    </div>
                    <div class="stats-grid">
                        <div class="stat-item">
                            <div class="stat-number">{{ $campaign->views_count }}</div>
                            <div class="stat-label">Vues</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $campaign->likes_count ?? 0 }}</div>
                            <div class="stat-label">J'aime</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $campaign->comments_count ?? 0 }}</div>
                            <div class="stat-label">Commentaires</div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-number">{{ $campaign->shares_count ?? 0 }}</div>
                            <div class="stat-label">Partages</div>
                        </div>
                    </div>
                </div>

                <!-- Informations sur le créateur -->
                <div class="info-card">
                    <div class="card-title">
                        <i class="fas fa-user"></i>
                        Créateur de la campagne
                    </div>
                    <div class="creator-info">
                        <div class="creator-avatar">{{ $campaign->creator ? Str::upper(substr($campaign->creator->name, 0, 2)) : 'NA' }}</div>
                        <div class="creator-details">
                            <h4>{{ $campaign->creator ? $campaign->creator->name : 'Inconnu' }}</h4>
                            <p>{{ $campaign->creator ? $campaign->creator->email : 'N/A' }}</p>
                            <p>Membre depuis {{ $campaign->creator ? $campaign->creator->created_at->format('F Y') : 'N/A' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Dates de la campagne -->
                <div class="info-card">
                    <div class="card-title">
                        <i class="fas fa-calendar"></i>
                        Dates de la campagne
                    </div>
                    <div class="dates-info">
                        <div class="form-group">
                            <label for="startDate">Date de début</label>
                            <input type="date" class="form-control editable" id="startDate" data-field="start_date" value="{{ $campaign->start_date->format('Y-m-d') }}" disabled>
                        </div>
                        <div class="form-group">
                            <label for="endDate">Date de fin</label>
                            <input type="date" class="form-control editable" id="endDate" data-field="end_date" value="{{ $campaign->end_date->format('Y-m-d') }}" disabled>
                        </div>
                    </div>
                </div>

                <!-- Informations système -->
                <div class="info-card">
                    <div class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Informations système
                    </div>
                    <div class="date-item">
                        <span class="date-label">Créée le :</span>
                        <span class="date-value">{{ $campaign->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="date-item">
                        <span class="date-label">Dernière modification :</span>
                        <span class="date-value" id="lastModified">{{ $campaign->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="date-item">
                        <span class="date-label">ID :</span>
                        <span class="date-value">#CAMP-{{ $campaign->id }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions administratives -->
        <div class="actions-section">
            <div class="card-title" style="margin-bottom: 1.5rem;">
                <i class="fas fa-cogs"></i>
                Actions administratives
            </div>
            <div class="actions-grid">
                <a href="{{ route('admin.campaigns.show', $campaign->id) }}" class="action-btn primary" target="_blank">
                    <i class="fas fa-eye"></i>
                    <strong>Voir la page publique</strong>
                    <small>Prévisualiser comme un visiteur</small>
                </a>
                <a href="#" class="action-btn success" onclick="duplicateCampaign()">
                    <i class="fas fa-copy"></i>
                    <strong>Dupliquer la campagne</strong>
                    <small>Créer une copie modifiable</small>
                </a>
                <a href="#" class="action-btn primary" onclick="exportData()">
                    <i class="fas fa-download"></i>
                    <strong>Exporter les données</strong>
                    <small>CSV avec statistiques</small>
                </a>
                <a href="#" class="action-btn primary" onclick="viewComments()">
                    <i class="fas fa-comments"></i>
                    <strong>Gérer les commentaires</strong>
                    <small>Modérer et répondre</small>
                </a>
                <a href="#" class="action-btn success" onclick="sendNotification()">
                    <i class="fas fa-bell"></i>
                    <strong>Notifier les participants</strong>
                    <small>Envoyer une mise à jour</small>
                </a>
                <a href="#" class="action-btn danger" onclick="deleteCampaign()">
                    <i class="fas fa-trash"></i>
                    <strong>Supprimer la campagne</strong>
                    <small>Action irréversible</small>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal de suppression -->
    <div class="delete-modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="modal-title">Confirmer la suppression</h3>
            <p class="modal-text" id="deleteModalText">
                Êtes-vous sûr de vouloir supprimer cette campagne ? Cette action est irréversible.
            </p>
            <div class="modal-actions">
                <button class="btn btn-cancel" onclick="closeDeleteModal()">
                    Annuler
                </button>
                <button class="btn btn-danger" id="confirmDeleteBtn" onclick="deleteCampaign()">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 2rem 1rem;
            }

            .campaign-header {
                background: white;
                border-radius: 15px;
                padding: 2rem;
                margin-bottom: 2rem;
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
                position: relative;
            }

            .edit-mode-toggle {
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
                display: flex;
                gap: 0.5rem;
            }

            .toggle-btn {
                padding: 0.6rem 1.2rem;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .btn-edit {
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
                border: 2px solid #28a745;
            }

            .btn-edit:hover {
                background: #28a745;
                color: white;
            }

            .btn-save {
                background: #28a745;
                color: white;
                display: none;
            }

            .btn-save:hover {
                background: #218838;
            }

            .btn-cancel {
                background: #6c757d;
                color: white;
                display: none;
            }

            .btn-cancel:hover {
                background: #545b62;
            }

            .campaign-meta {
                display: flex;
                justify-content: space-between;
                align-items: flex-start;
                margin-bottom: 1.5rem;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .status-info {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .status-badge {
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-weight: 600;
                text-align: center;
                min-width: 80px;
            }

            .status-active {
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }

            .status-ended {
                background: rgba(108, 117, 125, 0.1);
                color: #6c757d;
            }

            .status-upcoming {
                background: rgba(255, 193, 7, 0.1);
                color: #ffc107;
            }

            .category-badge {
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-weight: 600;
            }

            .campaign-image-section {
                margin-bottom: 2rem;
            }

            .campaign-image {
                width: 100%;
                height: 300px;
                background: linear-gradient(135deg, #28a745, #20c997);
                border-radius: 12px;
                overflow: hidden;
                position: relative;
            }

            .campaign-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .image-upload-overlay {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0,0,0,0.7);
                display: none;
                align-items: center;
                justify-content: center;
                color: white;
                cursor: pointer;
                font-size: 1.2rem;
            }

            .content-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 2rem;
                margin-bottom: 2rem;
            }

            .main-content {
                background: white;
                border-radius: 15px;
                padding: 2rem;
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            }

            .sidebar-content {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .info-card {
                background: white;
                border-radius: 15px;
                padding: 1.5rem;
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            }

            .card-title {
                color: #28a745;
                font-weight: 600;
                font-size: 1.1rem;
                margin-bottom: 1rem;
                display: flex;
                align-items: center;
                gap: 0.5rem;
            }

            .form-group {
                margin-bottom: 1.5rem;
            }

            .form-group label {
                display: block;
                margin-bottom: 0.5rem;
                color: #333;
                font-weight: 600;
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

            .form-control:disabled {
                background: #f8f9fa;
                color: #6c757d;
            }

            .editable {
                border: 2px dashed transparent;
                border-radius: 4px;
                padding: 0.3rem;
                transition: all 0.3s ease;
            }

            .edit-mode .editable:hover {
                border-color: #28a745;
                background: rgba(40, 167, 69, 0.05);
                cursor: text;
            }

            textarea.form-control {
                resize: vertical;
                min-height: 120px;
            }

            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .stat-item {
                text-align: center;
                padding: 1rem;
                background: #f8f9fa;
                border-radius: 8px;
            }

            .stat-number {
                font-size: 1.5rem;
                font-weight: 700;
                color: #28a745;
                margin-bottom: 0.3rem;
            }

            .stat-label {
                font-size: 0.9rem;
                color: #666;
            }

            .creator-info {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .creator-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, #28a745, #20c997);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1.2rem;
            }

            .creator-details h4 {
                color: #333;
                margin-bottom: 0.2rem;
            }

            .creator-details p {
                color: #666;
                font-size: 0.9rem;
            }

            .dates-info {
                background: #f8f9fa;
                padding: 1rem;
                border-radius: 8px;
                border-left: 4px solid #28a745;
            }

            .date-item {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }

            .date-item:last-child {
                margin-bottom: 0;
            }

            .date-label {
                font-weight: 600;
                color: #555;
            }

            .date-value {
                color: #666;
            }

            .actions-section {
                background: white;
                border-radius: 15px;
                padding: 2rem;
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            }

            .actions-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .action-btn {
                padding: 1rem;
                border: 2px solid #e9ecef;
                border-radius: 12px;
                background: white;
                cursor: pointer;
                transition: all 0.3s ease;
                text-align: center;
                text-decoration: none;
                color: inherit;
            }

            .action-btn:hover {
                border-color: #28a745;
                background: rgba(40, 167, 69, 0.05);
            }

            .action-btn i {
                font-size: 2rem;
                margin-bottom: 0.5rem;
                display: block;
            }

            .action-btn.danger:hover {
                border-color: #dc3545;
                background: rgba(220, 53, 69, 0.05);
            }

            .action-btn.danger i {
                color: #dc3545;
            }

            .action-btn.primary i {
                color: #007bff;
            }

            .action-btn.success i {
                color: #28a745;
            }

            .character-count {
                text-align: right;
                color: #666;
                font-size: 0.875rem;
                margin-top: 0.25rem;
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

            .edit-indicator {
                position: absolute;
                top: -5px;
                right: -5px;
                width: 20px;
                height: 20px;
                background: #28a745;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 0.8rem;
            }

            .edit-mode .editable {
                position: relative;
            }

            .edit-mode .editable:hover .edit-indicator {
                display: flex;
            }

            .delete-modal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
                z-index: 1000;
                justify-content: center;
                align-items: center;
            }

            .delete-modal.active {
                display: flex;
            }

            .modal-content {
                background: white;
                padding: 2rem;
                border-radius: 12px;
                max-width: 500px;
                width: 90%;
                text-align: center;
            }

            .modal-icon {
                font-size: 3rem;
                color: #dc3545;
                margin-bottom: 1rem;
            }

            .modal-title {
                color: #333;
                margin-bottom: 1rem;
                font-size: 1.3rem;
            }

            .modal-text {
                color: #666;
                margin-bottom: 2rem;
                line-height: 1.5;
            }

            .modal-actions {
                display: flex;
                justify-content: center;
                gap: 1rem;
            }

            .btn {
                padding: 0.6rem 1.2rem;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 0.5rem;
                text-decoration: none;
                font-size: 0.95rem;
            }

            .btn-danger {
                background: #dc3545;
                color: white;
            }

            .btn-danger:hover {
                background: #c82333;
                transform: translateY(-1px);
            }

            .btn-cancel {
                background: #6c757d;
                color: white;
            }

            .btn-cancel:hover {
                background: #545b62;
            }

            @media (max-width: 768px) {
                .content-grid {
                    grid-template-columns: 1fr;
                }

                .campaign-meta {
                    flex-direction: column;
                    align-items: flex-start;
                }

                .edit-mode-toggle {
                    position: static;
                    margin-bottom: 1rem;
                }

                .actions-grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let isEditMode = false;
            let originalData = {};
            const campaignId = {{ $campaign->id }};
            const csrfToken = '{{ csrf_token() }}';
            const jwtToken = localStorage.getItem('jwt_token');

            function toggleEditMode() {
                isEditMode = true;
                document.body.classList.add('edit-mode');

                // Masquer le bouton modifier
                document.getElementById('editBtn').style.display = 'none';
                document.getElementById('saveBtn').style.display = 'inline-flex';
                document.getElementById('cancelBtn').style.display = 'inline-flex';

                // Activer les champs éditables
                const editableElements = document.querySelectorAll('.editable');
                editableElements.forEach(element => {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                        element.disabled = false;
                    } else {
                        element.contentEditable = true;
                    }

                    // Sauvegarder la valeur originale
                    const field = element.dataset.field;
                    if (field) {
                        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                            originalData[field] = element.value;
                        } else {
                            originalData[field] = element.textContent;
                        }
                    }
                });

                // Afficher le select pour la catégorie
                document.getElementById('categoryWrapper').style.display = 'block';
                document.getElementById('categoryBadge').style.display = 'none';

                // Afficher l'overlay d'upload d'image
                document.getElementById('imageUploadOverlay').style.display = 'flex';
            }

            function cancelEdit() {
                isEditMode = false;
                document.body.classList.remove('edit-mode');

                // Restaurer les boutons
                document.getElementById('editBtn').style.display = 'inline-flex';
                document.getElementById('saveBtn').style.display = 'none';
                document.getElementById('cancelBtn').style.display = 'none';

                // Restaurer les valeurs originales
                const editableElements = document.querySelectorAll('.editable');
                editableElements.forEach(element => {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                        element.disabled = true;
                        const field = element.dataset.field;
                        if (field && originalData[field] !== undefined) {
                            element.value = originalData[field];
                        }
                    } else {
                        element.contentEditable = false;
                        const field = element.dataset.field;
                        if (field && originalData[field] !== undefined) {
                            element.textContent = originalData[field];
                        }
                    }
                });

                // Masquer le select de catégorie
                document.getElementById('categoryWrapper').style.display = 'none';
                document.getElementById('categoryBadge').style.display = 'inline-block';

                // Masquer l'overlay d'image
                document.getElementById('imageUploadOverlay').style.display = 'none';

                originalData = {};
            }

            function saveChanges() {
                if (!jwtToken) {
                    alert('Vous devez être connecté pour effectuer cette action.');
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                // Validation des données
                if (!validateForm()) {
                    return;
                }

                const saveBtn = document.getElementById('saveBtn');
                const originalText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde...';
                saveBtn.disabled = true;

                // Collecter les données
                const formData = new FormData();
                const editableElements = document.querySelectorAll('.editable');
                editableElements.forEach(element => {
                    const field = element.dataset.field;
                    if (field) {
                        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                            formData.append(field, element.value);
                        } else {
                            formData.append(field, element.textContent.trim());
                        }
                    }
                });

                // Ajouter l'image si modifiée
                const fileInput = document.getElementById('imageUpload');
                if (fileInput.files[0]) {
                    formData.append('media[]', fileInput.files[0]);
                }

                fetch(`/admin/campaigns/${campaignId}`, {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${jwtToken}`,
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: formData
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`Erreur HTTP: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            // Mettre à jour le badge de statut
                            updateStatusBadge(data.campaign.status);
                            document.getElementById('categoryBadge').textContent = data.campaign.category.charAt(0).toUpperCase() + data.campaign.category.slice(1);
                            document.getElementById('lastModified').textContent = data.campaign.updated_at;

                            // Sortir du mode édition
                            isEditMode = false;
                            document.body.classList.remove('edit-mode');
                            document.getElementById('editBtn').style.display = 'inline-flex';
                            saveBtn.style.display = 'none';
                            document.getElementById('cancelBtn').style.display = 'none';

                            editableElements.forEach(element => {
                                if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                                    element.disabled = true;
                                } else {
                                    element.contentEditable = false;
                                }
                            });

                            document.getElementById('categoryWrapper').style.display = 'none';
                            document.getElementById('categoryBadge').style.display = 'inline-block';
                            document.getElementById('imageUploadOverlay').style.display = 'none';

                            saveBtn.innerHTML = originalText;
                            saveBtn.disabled = false;

                            alert('Modifications sauvegardées avec succès !');
                        } else {
                            alert(data.error || 'Erreur lors de la sauvegarde');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau:', error.message);
                        alert('Une erreur est survenue: ' + error.message);
                    });
            }

            function validateForm() {
                let isValid = true;
                const title = document.getElementById('campaignTitle').textContent.trim();
                const description = document.getElementById('description').value.trim();
                const content = document.getElementById('content').value.trim();
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;

                // Validation du titre
                if (!title || title.length < 10) {
                    alert('Le titre doit contenir au moins 10 caractères');
                    isValid = false;
                }

                // Validation de la description
                if (!description || description.length < 50) {
                    alert('La description doit contenir au moins 50 caractères');
                    isValid = false;
                }

                // Validation du contenu
                if (!content || content.length < 100) {
                    alert('Le contenu doit contenir au moins 100 caractères');
                    isValid = false;
                }

                // Validation des dates
                if (new Date(endDate) <= new Date(startDate)) {
                    alert('La date de fin doit être postérieure à la date de début');
                    isValid = false;
                }

                return isValid;
            }

            function updateStatusBadge(status) {
                const statusBadge = document.getElementById('statusBadge');
                statusBadge.classList.remove('status-active', 'status-ended', 'status-upcoming');
                switch(status) {
                    case 'active':
                        statusBadge.classList.add('status-active');
                        statusBadge.textContent = 'Active';
                        break;
                    case 'ended':
                        statusBadge.classList.add('status-ended');
                        statusBadge.textContent = 'Terminée';
                        break;
                    case 'upcoming':
                        statusBadge.classList.add('status-upcoming');
                        statusBadge.textContent = 'À venir';
                        break;
                }
            }

            function setupImageUpload() {
                const overlay = document.getElementById('imageUploadOverlay');
                const fileInput = document.getElementById('imageUpload');
                const image = document.getElementById('campaignImage');

                overlay.addEventListener('click', () => {
                    if (isEditMode) {
                        fileInput.click();
                    }
                });

                fileInput.addEventListener('change', (e) => {
                    const file = e.target.files[0];
                    if (file) {
                        const reader = new FileReader();
                        reader.onload = (e) => {
                            image.src = e.target.result;
                        };
                        reader.readAsDataURL(file);
                    }
                });
            }

            function setupCharacterCounters() {
                const description = document.getElementById('description');
                const content = document.getElementById('content');
                const descriptionCount = document.getElementById('descriptionCount');
                const contentCount = document.getElementById('contentCount');

                description.addEventListener('input', () => {
                    descriptionCount.textContent = description.value.length;
                });

                content.addEventListener('input', () => {
                    contentCount.textContent = content.value.length;
                });
            }

            function duplicateCampaign() {
                if (confirm('Voulez-vous créer une copie de cette campagne ?')) {
                    fetch(`/admin/campaigns/${campaignId}/duplicate`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${jwtToken}`,
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Campagne dupliquée ! Redirection vers la nouvelle campagne...');
                                window.location.href = `/admin/campaigns/${data.campaign_id}`;
                            } else {
                                alert(data.error || 'Erreur lors de la duplication');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur réseau:', error.message);
                            alert('Une erreur est survenue: ' + error.message);
                        });
                }
            }

            function exportData() {
                fetch(`/admin/campaigns/${campaignId}/export`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${jwtToken}`,
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.blob())
                    .then(blob => {
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `campaign-${campaignId}-data.csv`;
                        a.click();
                        window.URL.revokeObjectURL(url);
                    })
                    .catch(error => {
                        console.error('Erreur réseau:', error.message);
                        alert('Une erreur est survenue: ' + error.message);
                    });
            }

            function viewComments() {
                alert('Ouverture du module de gestion des commentaires...');
                window.location.href = `/admin/campaigns/${campaignId}/comments`;
            }

            function sendNotification() {
                const message = prompt('Message à envoyer aux participants :');
                if (message) {
                    fetch(`/admin/campaigns/${campaignId}/notify`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${jwtToken}`,
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ message })
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Notification envoyée à tous les participants !');
                            } else {
                                alert(data.error || 'Erreur lors de l\'envoi de la notification');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur réseau:', error.message);
                            alert('Une erreur est survenue: ' + error.message);
                        });
                }
            }

            function deleteCampaign() {
                if (confirm('ATTENTION : Êtes-vous sûr de vouloir supprimer définitivement cette campagne ?\n\nCette action est irréversible et supprimera également tous les commentaires et statistiques associés.')) {
                    document.getElementById('deleteModal').classList.add('active');
                    document.getElementById('confirmDeleteBtn').onclick = () => {
                        fetch(`/admin/campaigns/${campaignId}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${jwtToken}`,
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error(`Erreur HTTP: ${response.status}`);
                                }
                                return response.json();
                            })
                            .then(data => {
                                if (data.success) {
                                    alert('Campagne supprimée. Redirection vers la liste...');
                                    window.location.href = '{{ route("admin.campaigns.index") }}';
                                } else {
                                    alert(data.error || 'Erreur lors de la suppression');
                                }
                            })
                            .catch(error => {
                                console.error('Erreur réseau:', error.message);
                                alert('Une erreur est survenue: ' + error.message);
                            });
                    };
                }
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.remove('active');
            }

            function setupKeyboardShortcuts() {
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.key === 'e') {
                        e.preventDefault();
                        if (!isEditMode) {
                            toggleEditMode();
                        }
                    }
                    if (e.ctrlKey && e.key === 's') {
                        e.preventDefault();
                        if (isEditMode) {
                            saveChanges();
                        }
                    }
                    if (e.key === 'Escape') {
                        if (isEditMode) {
                            cancelEdit();
                        }
                    }
                });
            }

            function setupAutoSave() {
                let autoSaveTimer;
                document.addEventListener('input', (e) => {
                    if (isEditMode && e.target.classList.contains('editable')) {
                        clearTimeout(autoSaveTimer);
                        autoSaveTimer = setTimeout(() => {
                            console.log('Auto-sauvegarde des modifications...');
                        }, 30000);
                    }
                });
            }

            document.addEventListener('DOMContentLoaded', function() {
                setupImageUpload();
                setupCharacterCounters();
                setupKeyboardShortcuts();
                setupAutoSave();

                // Initialiser les compteurs
                document.getElementById('descriptionCount').textContent = document.getElementById('description').value.length;
                document.getElementById('contentCount').textContent = document.getElementById('content').value.length;
            });

            window.addEventListener('beforeunload', function(e) {
                if (isEditMode) {
                    e.preventDefault();
                    e.returnValue = 'Vous avez des modifications non sauvegardées. Êtes-vous sûr de vouloir quitter ?';
                    return e.returnValue;
                }
            });
        </script>
    @endpush
@endsection
