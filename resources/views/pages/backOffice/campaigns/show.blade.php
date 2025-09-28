@extends('layouts.admin')

@section('title', 'Gestion de la Campagne - Echofy')

@section('content')
    <!-- Overlay de chargement -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Container de notifications -->
    <div id="notification-container"></div>

    <!-- Content Header -->
    <div class="content-header">
        <div class="section-header fade-in">
            <div class="section-subtitle">
                <i class="bi bi-leaf"></i>
                Gestion de Campagne
            </div>
            <h1 class="section-title">{{ Str::limit($campaign->title, 50) }}</h1>
            <nav class="breadcrumb-nav">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.campaigns.index') }}">Campagnes</a></li>
                    <li class="breadcrumb-item active">{{ Str::limit($campaign->title, 30) }}</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="campaign-header fade-in stagger-delay-1">
            <div class="edit-mode-toggle">
                <button class="toggle-btn btn-edit" id="editBtn" onclick="toggleEditMode()">
                    <i class="bi bi-pencil-square"></i>
                    Modifier
                </button>
                <button class="toggle-btn btn-save" id="saveBtn" onclick="saveChanges()">
                    <i class="bi bi-save"></i>
                    Enregistrer
                </button>
                <button class="toggle-btn btn-cancel" id="cancelBtn" onclick="cancelEdit()">
                    <i class="bi bi-x-circle"></i>
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
                        <i class="bi bi-chevron-down select-arrow"></i>
                    </div>
                    <span class="category-badge" id="categoryBadge">{{ ucfirst($campaign->category) }}</span>
                </div>
            </div>

            <div class="campaign-image-section">
                <div class="campaign-image-gallery">
                    @forelse ($campaign->media_urls['images'] ?? [] as $index => $image)
                        <div class="campaign-image" data-image-index="{{ $index }}">
                            <img src="{{ Storage::disk('public')->exists($image) ? Storage::url($image) : 'https://via.placeholder.com/800x300?text=Image' }}" alt="{{ $campaign->title }}" id="campaignImage{{ $index }}">
                            <div class="image-upload-overlay" id="imageUploadOverlay{{ $index }}">
                                <div>
                                    <i class="bi bi-camera"></i>
                                    <div>Cliquer pour changer l'image</div>
                                </div>
                            </div>
                            <input type="file" id="imageUpload{{ $index }}" accept="image/*" style="display: none;" data-image-index="{{ $index }}">
                            <button class="remove-image-btn" onclick="removeImage({{ $index }})" style="display: none;">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    @empty
                        <div class="campaign-image" data-image-index="0">
                            <img src="https://via.placeholder.com/800x300?text=Image" alt="{{ $campaign->title }}" id="campaignImage0">
                            <div class="image-upload-overlay" id="imageUploadOverlay0">
                                <div>
                                    <i class="bi bi-camera"></i>
                                    <div>Cliquer pour ajouter une image</div>
                                </div>
                            </div>
                            <input type="file" id="imageUpload0" accept="image/*" style="display: none;" data-image-index="0">
                        </div>
                    @endforelse
                </div>
                <button class="add-image-btn" id="addImageBtn" onclick="addImageSlot()" style="display: none;">+ Ajouter une image</button>
            </div>

            <h1 class="editable" id="campaignTitle" data-field="title" contenteditable="false">
                {{ $campaign->title }}
                <div class="edit-indicator">
                    <i class="bi bi-pencil"></i>
                </div>
            </h1>
        </div>

        <div class="content-grid">
            <div class="main-content fade-in stagger-delay-2">
                <div class="form-group">
                    <label for="content">Contenu détaillé</label>
                    <textarea class="form-control editable" id="content" data-field="content" rows="10" disabled>{{ $campaign->content }}</textarea>
                    <div class="character-count"><span id="contentCount">{{ strlen($campaign->content) }}</span>/2000 caractères</div>
                </div>

                <!-- Objectifs -->
                <div class="form-group">
                    <label>Objectifs</label>
                    <div class="dynamic-list" id="objectivesList">
                        @foreach ($campaign->objectives ?? [] as $index => $objective)
                            <div class="list-item">
                                <textarea class="form-control editable" data-field="objectives[{{$index}}]" disabled>{{ $objective }}</textarea>
                                <button class="remove-btn" onclick="removeListItem(this)" style="display: none;"><i class="bi bi-trash"></i></button>
                            </div>
                        @endforeach
                    </div>
                    <button class="add-btn" id="addObjectiveBtn" onclick="addListItem('objectivesList')" style="display: none;">+ Ajouter un objectif</button>
                </div>

                <!-- Actions -->
                <div class="form-group">
                    <label>Actions</label>
                    <div class="dynamic-list" id="actionsList">
                        @foreach ($campaign->actions ?? [] as $index => $action)
                            <div class="list-item">
                                <textarea class="form-control editable" data-field="actions[{{$index}}]" disabled>{{ $action }}</textarea>
                                <button class="remove-btn" onclick="removeListItem(this)" style="display: none;"><i class="bi bi-trash"></i></button>
                            </div>
                        @endforeach
                    </div>
                    <button class="add-btn" id="addActionBtn" onclick="addListItem('actionsList')" style="display: none;">+ Ajouter une action</button>
                </div>

                <!-- Informations de contact -->
                <div class="form-group">
                    <label for="contactInfo">Informations de contact</label>
                    <textarea class="form-control editable" id="contactInfo" data-field="contact_info" disabled>{{ $campaign->contact_info }}</textarea>
                    <div class="character-count"><span id="contactInfoCount">{{ strlen($campaign->contact_info ?? '') }}</span>/1000 caractères</div>
                </div>

                <!-- URL de la vidéo -->
                <div class="form-group">
                    <label for="videoUrl">URL de la vidéo</label>
                    <input type="url" class="form-control editable" id="videoUrl" data-field="video_url" value="{{ $campaign->media_urls['videos'][0] ?? '' }}" disabled>
                </div>

                <!-- URL du site web -->
                <div class="form-group">
                    <label for="websiteUrl">URL du site web</label>
                    <input type="url" class="form-control editable" id="websiteUrl" data-field="website_url" value="{{ $campaign->media_urls['website'] ?? '' }}" disabled>
                </div>
            </div>

            <div class="sidebar-content fade-in stagger-delay-3">
                <!-- Statistiques -->
                <div class="info-card">
                    <div class="card-title">
                        <i class="bi bi-bar-chart"></i>
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
                        <i class="bi bi-person"></i>
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
                        <i class="bi bi-calendar"></i>
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
                        <i class="bi bi-info-circle"></i>
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
        <div class="actions-section fade-in stagger-delay-4">
            <div class="card-title">
                <i class="bi bi-gear"></i>
                Actions administratives
            </div>
            <div class="actions-grid">
                <a href="{{ route('front.campaigns.show', $campaign->id) }}" class="action-btn primary" target="_blank">
                    <i class="bi bi-eye"></i>
                    <strong>Voir la page publique</strong>
                    <small>Prévisualiser comme un visiteur</small>
                </a>
                <a href="#" class="action-btn success" onclick="duplicateCampaign()">
                    <i class="bi bi-copy"></i>
                    <strong>Dupliquer la campagne</strong>
                    <small>Créer une copie modifiable</small>
                </a>
                <a href="#" class="action-btn primary" onclick="exportData()">
                    <i class="bi bi-download"></i>
                    <strong>Exporter les données</strong>
                    <small>CSV avec statistiques</small>
                </a>
                <a href="#" class="action-btn primary" onclick="scrollToComments()">
                    <i class="bi bi-chat"></i>
                    <strong>Gérer les commentaires</strong>
                    <small>Modérer et répondre</small>
                </a>
                <a href="#" class="action-btn danger" onclick="deleteCampaign()">
                    <i class="bi bi-trash"></i>
                    <strong>Supprimer la campagne</strong>
                    <small>Action irréversible</small>
                </a>
            </div>
        </div>

        <!-- Section des commentaires -->
        <div class="comments-section fade-in stagger-delay-5" id="commentsSection" style="display: none;">
            <div class="card-title">
                <i class="bi bi-chat-text"></i>
                Commentaires de la campagne
            </div>
            <div class="comments-list" id="commentsList">
                @forelse ($comments as $comment)
                    <div class="comment-item" data-comment-id="{{ $comment->id }}">
                        <div class="comment-header">
                            <span class="comment-author">{{ $comment->user ? $comment->user->name : 'Anonyme' }}</span>
                            <span class="comment-date">{{ $comment->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="comment-content">{{ $comment->content }}</div>
                        <div class="comment-actions">
                            <button class="action-btn danger" onclick="deleteComment({{ $comment->id }})">
                                <i class="bi bi-trash"></i>
                                Supprimer
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-chat"></i>
                        </div>
                        <h3 class="empty-title">Aucun commentaire</h3>
                        <p class="empty-description">Aucun commentaire n'a été publié pour cette campagne.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Modal de suppression -->
        <div class="delete-modal" id="deleteModal">
            <div class="modal-content">
                <div class="modal-icon">
                    <i class="bi bi-exclamation-triangle"></i>
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
                        <i class="bi bi-trash"></i>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
                color: #334155;
                line-height: 1.6;
            }

            .container {
                max-width: 1400px;
                margin: 0 auto;
                padding: 2rem 1rem;
            }

            /* Notification fixe en haut à droite */
            #notification-container {
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                max-width: 400px;
            }

            .alert {
                padding: 16px 20px;
                margin-bottom: 12px;
                border-radius: 12px;
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255,255,255,0.2);
                font-weight: 500;
                transform: translateX(100%);
                opacity: 0;
                transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            }

            .alert.show {
                opacity: 1;
                transform: translateX(0);
            }

            .alert-success {
                background: rgba(34, 197, 94, 0.15);
                color: #166534;
                border-color: rgba(34, 197, 94, 0.3);
            }

            .alert-danger {
                background: rgba(239, 68, 68, 0.15);
                color: #dc2626;
                border-color: rgba(239, 68, 68, 0.3);
            }

            /* En-tête de section */
            .content-header {
                text-align: center;
                margin-bottom: 3rem;
            }

            .section-header {
                text-align: center;
            }

            .section-subtitle {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                background: linear-gradient(135deg, #10b981, #059669);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 16px;
            }

            .section-title {
                font-size: 3rem;
                font-weight: 800;
                background: linear-gradient(135deg, #1e293b, #475569);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                line-height: 1.2;
                margin-bottom: 16px;
            }

            .breadcrumb-nav {
                display: flex;
                justify-content: center;
            }

            .breadcrumb {
                display: flex;
                gap: 8px;
                list-style: none;
                padding: 0;
                margin: 0;
            }

            .breadcrumb-item a {
                color: #10b981;
                text-decoration: none;
                font-weight: 500;
            }

            .breadcrumb-item a:hover {
                text-decoration: underline;
            }

            .breadcrumb-item.active {
                color: #64748b;
                font-weight: 600;
            }

            .breadcrumb-item + .breadcrumb-item::before {
                content: '/';
                color: #64748b;
                margin-right: 8px;
            }

            /* Campaign Header */
            .campaign-header {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 24px;
                padding: 2rem;
                margin-bottom: 2rem;
                box-shadow: 0 8px 25px rgba(0,0,0,0.08);
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
                border-radius: 12px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                align-items: center;
                gap: 0.5rem;
                font-size: 0.95rem;
            }

            .btn-edit {
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
                border: 2px solid #10b981;
            }

            .btn-edit:hover {
                background: #10b981;
                color: white;
            }

            .btn-save {
                background: #10b981;
                color: white;
                display: none;
            }

            .btn-save:hover {
                background: #059669;
            }

            .btn-cancel {
                background: #6c757d;
                color: white;
                display: none;
            }

            .btn-cancel:hover {
                background: #5a6268;
            }

            .campaign-meta {
                display: flex;
                align-items: center;
                gap: 1rem;
                margin-bottom: 1.5rem;
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
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
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
                background: rgba(16, 185, 129, 0.1);
                color: #10b981;
                padding: 0.5rem 1rem;
                border-radius: 20px;
                font-weight: 600;
            }

            .select-wrapper {
                position: relative;
            }

            .select-wrapper .select-arrow {
                position: absolute;
                right: 16px;
                top: 50%;
                transform: translateY(-50%);
                color: #64748b;
                pointer-events: none;
                transition: transform 0.3s ease;
            }

            .select-wrapper:hover .select-arrow {
                transform: translateY(-50%) rotate(180deg);
            }

            .campaign-image-section {
                margin-bottom: 2rem;
            }

            .campaign-image-gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .campaign-image {
                width: 300px;
                height: 200px;
                background: linear-gradient(135deg, #10b981, #059669);
                border-radius: 16px;
                overflow: hidden;
                position: relative;
            }

            .campaign-image img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .campaign-image:hover img {
                transform: scale(1.1);
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
                transition: all 0.3s ease;
            }

            .edit-mode .image-upload-overlay {
                display: flex;
            }

            .remove-image-btn {
                position: absolute;
                top: 10px;
                right: 10px;
                background: #dc3545;
                color: white;
                border: none;
                border-radius: 50%;
                width: 30px;
                height: 30px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                transition: all 0.3s ease;
            }

            .remove-image-btn:hover {
                background: #c82333;
            }

            .add-image-btn {
                padding: 0.6rem 1.2rem;
                border: none;
                border-radius: 12px;
                cursor: pointer;
                font-weight: 600;
                background: #10b981;
                color: white;
                margin-top: 1rem;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .add-image-btn:hover {
                background: #059669;
            }

            .campaign-header h1 {
                font-size: 1.8rem;
                font-weight: 700;
                color: #1e293b;
                position: relative;
                display: inline-block;
            }

            .edit-indicator {
                position: absolute;
                top: -5px;
                right: -5px;
                width: 20px;
                height: 20px;
                background: #10b981;
                border-radius: 50%;
                display: none;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 0.8rem;
            }

            .edit-mode .editable:hover .edit-indicator {
                display: flex;
            }

            /* Content Grid */
            .content-grid {
                display: grid;
                grid-template-columns: 2fr 1fr;
                gap: 2rem;
                margin-bottom: 2rem;
            }

            .main-content {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 24px;
                padding: 2rem;
                box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            }

            .sidebar-content {
                display: flex;
                flex-direction: column;
                gap: 1.5rem;
            }

            .info-card {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 24px;
                padding: 1.5rem;
                box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            }

            .card-title {
                color: #10b981;
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
                color: #1e293b;
                font-weight: 600;
            }

            .form-control {
                width: 100%;
                padding: 0.8rem;
                border: 2px solid #e2e8f0;
                border-radius: 12px;
                font-size: 1rem;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                font-family: inherit;
                background: white;
            }

            .form-control:focus {
                outline: none;
                border-color: #10b981;
                box-shadow: 0 0 0 0.2rem rgba(16, 185, 129, 0.1);
            }

            .form-control:disabled {
                background: #f8f9fa;
                color: #6c757d;
            }

            .editable {
                border: 2px dashed transparent;
                border-radius: 12px;
                padding: 0.3rem;
                transition: all 0.3s ease;
            }

            .edit-mode .editable:hover {
                border-color: #10b981;
                background: rgba(16, 185, 129, 0.05);
                cursor: text;
            }

            textarea.form-control {
                resize: vertical;
                min-height: 120px;
            }

            .character-count {
                text-align: right;
                color: #64748b;
                font-size: 0.875rem;
                margin-top: 0.25rem;
            }

            /* Dynamic Lists */
            .dynamic-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
                margin-bottom: 1rem;
            }

            .list-item {
                display: flex;
                gap: 0.5rem;
                align-items: center;
            }

            .list-item textarea {
                flex: 1;
            }

            .add-btn, .remove-btn {
                padding: 0.6rem 1.2rem;
                border: none;
                border-radius: 12px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            }

            .add-btn {
                background: #10b981;
                color: white;
            }

            .add-btn:hover {
                background: #059669;
            }

            .remove-btn {
                background: #dc3545;
                color: white;
            }

            .remove-btn:hover {
                background: #c82333;
            }

            /* Stats Grid */
            .stats-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                gap: 1rem;
                margin-bottom: 1.5rem;
            }

            .stat-item {
                text-align: center;
                padding: 1rem;
                background: rgba(255, 255, 255, 0.9);
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            }

            .stat-number {
                font-size: 1.5rem;
                font-weight: 700;
                color: #10b981;
                margin-bottom: 0.3rem;
            }

            .stat-label {
                font-size: 0.9rem;
                color: #64748b;
            }

            /* Creator Info */
            .creator-info {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .creator-avatar {
                width: 50px;
                height: 50px;
                border-radius: 50%;
                background: linear-gradient(135deg, #10b981, #059669);
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-weight: 600;
                font-size: 1.2rem;
            }

            .creator-details h4 {
                color: #1e293b;
                margin-bottom: 0.2rem;
            }

            .creator-details p {
                color: #64748b;
                font-size: 0.9rem;
            }

            /* Dates Info */
            .dates-info {
                background: rgba(255, 255, 255, 0.9);
                padding: 1rem;
                border-radius: 12px;
                border-left: 4px solid #10b981;
            }

            .date-item {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }

            .date-label {
                font-weight: 600;
                color: #1e293b;
            }

            .date-value {
                color: #64748b;
            }

            /* Actions Section */
            .actions-section {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 24px;
                padding: 2rem;
                box-shadow: 0 8px 25px rgba(0,0,0,0.08);
                margin-bottom: 2rem;
            }

            .actions-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }

            .action-btn {
                padding: 1rem;
                border: 2px solid #e2e8f0;
                border-radius: 16px;
                background: rgba(255, 255, 255, 0.9);
                cursor: pointer;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                text-align: center;
                text-decoration: none;
                color: #1e293b;
            }

            .action-btn:hover {
                border-color: #10b981;
                background: rgba(16, 185, 129, 0.05);
                transform: translateY(-2px);
            }

            .action-btn i {
                font-size: 2rem;
                margin-bottom: 0.5rem;
                display: block;
            }

            .action-btn.primary i {
                color: #007bff;
            }

            .action-btn.success i {
                color: #10b981;
            }

            .action-btn.danger i {
                color: #dc3545;
            }

            /* Comments Section */
            .comments-section {
                background: rgba(255, 255, 255, 0.9);
                backdrop-filter: blur(20px);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 24px;
                padding: 2rem;
                box-shadow: 0 8px 25px rgba(0,0,0,0.08);
                margin-bottom: 2rem;
            }

            .comments-list {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }

            .comment-item {
                background: white;
                border-radius: 12px;
                padding: 1rem;
                box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            }

            .comment-header {
                display: flex;
                justify-content: space-between;
                margin-bottom: 0.5rem;
            }

            .comment-author {
                font-weight: 600;
                color: #1e293b;
            }

            .comment-date {
                color: #64748b;
                font-size: 0.9rem;
            }

            .comment-content {
                color: #334155;
                margin-bottom: 0.5rem;
            }

            .comment-actions {
                display: flex;
                justify-content: flex-end;
            }

            .comment-actions .action-btn {
                padding: 0.5rem 1rem;
                font-size: 0.9rem;
            }

            .empty-state {
                text-align: center;
                padding: 2rem;
            }

            .empty-icon {
                font-size: 3rem;
                color: #cbd5e1;
                margin-bottom: 1rem;
            }

            .empty-title {
                font-size: 1.3rem;
                font-weight: 600;
                color: #475569;
                margin-bottom: 0.5rem;
            }

            .empty-description {
                color: #64748b;
                font-size: 1rem;
            }

            /* Modal */
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
                border-radius: 16px;
                max-width: 500px;
                width: 90%;
                text-align: center;
                box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            }

            .modal-icon {
                font-size: 3rem;
                color: #dc3545;
                margin-bottom: 1rem;
            }

            .modal-title {
                color: #1e293b;
                margin-bottom: 1rem;
                font-size: 1.3rem;
            }

            .modal-text {
                color: #64748b;
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
                border-radius: 12px;
                cursor: pointer;
                font-weight: 600;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                align-items: center;
                gap: 0.5rem;
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
                background: #5a6268;
            }

            /* Animations de chargement */
            .loading-overlay {
                position: fixed;
                inset: 0;
                background: rgba(248, 250, 252, 0.8);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 9999;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .loading-overlay.show {
                opacity: 1;
                visibility: visible;
            }

            .loading-spinner {
                width: 50px;
                height: 50px;
                border: 4px solid #e2e8f0;
                border-top: 4px solid #10b981;
                border-radius: 50%;
                animation: spin 1s linear infinite;
            }

            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }

            /* Responsive Design */
            @media (max-width: 1024px) {
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
                    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
                }
            }

            @media (max-width: 768px) {
                .section-title {
                    font-size: 2rem;
                }

                .campaign-image {
                    height: 150px;
                    width: 100%;
                }

                .actions-grid {
                    grid-template-columns: 1fr;
                }

                .campaign-header {
                    margin: 0 -1rem 2rem;
                    border-radius: 0;
                }

                .main-content, .actions-section, .comments-section {
                    margin: 0 -1rem 2rem;
                    border-radius: 0;
                }
            }

            /* Animations d'entrée */
            .fade-in {
                opacity: 0;
                transform: translateY(30px);
                animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            }

            @keyframes fadeInUp {
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .stagger-delay-1 { animation-delay: 0.1s; }
            .stagger-delay-2 { animation-delay: 0.2s; }
            .stagger-delay-3 { animation-delay: 0.3s; }
            .stagger-delay-4 { animation-delay: 0.4s; }
            .stagger-delay-5 { animation-delay: 0.5s; }
        </style>
    @endpush

    @push('scripts')
        <script>
            let isEditMode = false;
            let originalData = {};
            const campaignId = {{ $campaign->id }};
            const csrfToken = '{{ csrf_token() }}';
            const jwtToken = localStorage.getItem('jwt_token');
            let imageCount = {{ count($campaign->media_urls['images'] ?? []) }};

            // Afficher une notification
            function showNotification(message, type = 'success') {
                const container = document.getElementById('notification-container');
                const alert = document.createElement('div');
                alert.className = `alert alert-${type} show`;
                alert.textContent = message;
                container.appendChild(alert);
                setTimeout(() => {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 500);
                }, 4000);
            }

            // Activer le mode édition
            function toggleEditMode() {
                isEditMode = true;
                document.body.classList.add('edit-mode');

                document.getElementById('editBtn').style.display = 'none';
                document.getElementById('saveBtn').style.display = 'inline-flex';
                document.getElementById('cancelBtn').style.display = 'inline-flex';

                const editableElements = document.querySelectorAll('.editable');
                editableElements.forEach(element => {
                    if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                        element.disabled = false;
                    } else {
                        element.contentEditable = true;
                    }
                    const field = element.dataset.field;
                    if (field) {
                        if (element.tagName === 'INPUT' || element.tagName === 'TEXTAREA' || element.tagName === 'SELECT') {
                            originalData[field] = element.value;
                        } else {
                            originalData[field] = element.textContent;
                        }
                    }
                });

                document.getElementById('categoryWrapper').style.display = 'block';
                document.getElementById('categoryBadge').style.display = 'none';
                document.querySelectorAll('.image-upload-overlay').forEach(overlay => overlay.style.display = 'flex');
                document.getElementById('addObjectiveBtn').style.display = 'inline-flex';
                document.getElementById('addActionBtn').style.display = 'inline-flex';
                document.getElementById('addImageBtn').style.display = 'inline-flex';
                document.querySelectorAll('.remove-btn').forEach(btn => btn.style.display = 'inline-flex');
                document.querySelectorAll('.remove-image-btn').forEach(btn => btn.style.display = 'inline-flex');
            }

            // Annuler les modifications
            function cancelEdit() {
                isEditMode = false;
                document.body.classList.remove('edit-mode');

                document.getElementById('editBtn').style.display = 'inline-flex';
                document.getElementById('saveBtn').style.display = 'none';
                document.getElementById('cancelBtn').style.display = 'none';

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

                document.getElementById('categoryWrapper').style.display = 'none';
                document.getElementById('categoryBadge').style.display = 'inline-block';
                document.querySelectorAll('.image-upload-overlay').forEach(overlay => overlay.style.display = 'none');
                document.getElementById('addObjectiveBtn').style.display = 'none';
                document.getElementById('addActionBtn').style.display = 'none';
                document.getElementById('addImageBtn').style.display = 'none';
                document.querySelectorAll('.remove-btn').forEach(btn => btn.style.display = 'none');
                document.querySelectorAll('.remove-image-btn').forEach(btn => btn.style.display = 'none');

                // Restaurer les images
                document.querySelector('.campaign-image-gallery').innerHTML = `{!! json_encode($campaign->media_urls['images'] ?? []) !!}`.length > 0 ?
                    `{!! json_encode($campaign->media_urls['images']) !!}`.map((image, index) => `
                    <div class="campaign-image" data-image-index="${index}">
                        <img src="${image ? '{{ Storage::url("' + image + '") }}' : 'https://via.placeholder.com/800x300?text=Image'}" alt="{{ $campaign->title }}" id="campaignImage${index}">
                        <div class="image-upload-overlay" id="imageUploadOverlay${index}">
                            <div>
                                <i class="bi bi-camera"></i>
                                <div>Cliquer pour changer l'image</div>
                            </div>
                        </div>
                        <input type="file" id="imageUpload${index}" accept="image/*" style="display: none;" data-image-index="${index}">
                        <button class="remove-image-btn" onclick="removeImage(${index})" style="display: none;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                `).join('') :
                    `<div class="campaign-image" data-image-index="0">
                    <img src="https://via.placeholder.com/800x300?text=Image" alt="{{ $campaign->title }}" id="campaignImage0">
                    <div class="image-upload-overlay" id="imageUploadOverlay0">
                        <div>
                            <i class="bi bi-camera"></i>
                            <div>Cliquer pour ajouter une image</div>
                        </div>
                    </div>
                    <input type="file" id="imageUpload0" accept="image/*" style="display: none;" data-image-index="0">
                </div>`;

                imageCount = {{ count($campaign->media_urls['images'] ?? []) }};
                setupImageUpload();

                originalData = {};
            }

            // Sauvegarder les modifications
            function saveChanges() {
                if (!jwtToken) {
                    showNotification('Vous devez être connecté pour effectuer cette action.', 'danger');
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                if (!validateForm()) {
                    return;
                }

                const saveBtn = document.getElementById('saveBtn');
                const originalText = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="bi bi-spinner"></i> Sauvegarde...';
                saveBtn.disabled = true;

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

                const fileInputs = document.querySelectorAll('input[type="file"]');
                fileInputs.forEach(input => {
                    if (input.files[0]) {
                        formData.append(`media[${input.dataset.imageIndex}]`, input.files[0]);
                    }
                });

                // Ajouter les indices des images à supprimer
                const deletedImages = JSON.parse(localStorage.getItem('deletedImages') || '[]');
                formData.append('deleted_images', JSON.stringify(deletedImages));

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
                            updateStatusBadge(data.campaign.status);
                            document.getElementById('categoryBadge').textContent = data.campaign.category.charAt(0).toUpperCase() + data.campaign.category.slice(1);
                            document.getElementById('lastModified').textContent = data.campaign.updated_at;

                            // Mettre à jour la galerie d'images
                            const gallery = document.querySelector('.campaign-image-gallery');
                            gallery.innerHTML = data.campaign.media_urls.images.length > 0 ?
                                data.campaign.media_urls.images.map((image, index) => `
                                <div class="campaign-image" data-image-index="${index}">
                                    <img src="${image ? '{{ Storage::url("' + image + '") }}' : 'https://via.placeholder.com/800x300?text=Image'}" alt="${data.campaign.title}" id="campaignImage${index}">
                                    <div class="image-upload-overlay" id="imageUploadOverlay${index}">
                                        <div>
                                            <i class="bi bi-camera"></i>
                                            <div>Cliquer pour changer l'image</div>
                                        </div>
                                    </div>
                                    <input type="file" id="imageUpload${index}" accept="image/*" style="display: none;" data-image-index="${index}">
                                    <button class="remove-image-btn" onclick="removeImage(${index})" style="display: none;">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            `).join('') :
                                `<div class="campaign-image" data-image-index="0">
                                <img src="https://via.placeholder.com/800x300?text=Image" alt="${data.campaign.title}" id="campaignImage0">
                                <div class="image-upload-overlay" id="imageUploadOverlay0">
                                    <div>
                                        <i class="bi bi-camera"></i>
                                        <div>Cliquer pour ajouter une image</div>
                                    </div>
                                </div>
                                <input type="file" id="imageUpload0" accept="image/*" style="display: none;" data-image-index="0">
                            </div>`;

                            imageCount = data.campaign.media_urls.images.length;
                            localStorage.removeItem('deletedImages');

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
                            document.querySelectorAll('.image-upload-overlay').forEach(overlay => overlay.style.display = 'none');
                            document.getElementById('addObjectiveBtn').style.display = 'none';
                            document.getElementById('addActionBtn').style.display = 'none';
                            document.getElementById('addImageBtn').style.display = 'none';
                            document.querySelectorAll('.remove-btn').forEach(btn => btn.style.display = 'none');
                            document.querySelectorAll('.remove-image-btn').forEach(btn => btn.style.display = 'none');

                            saveBtn.innerHTML = originalText;
                            saveBtn.disabled = false;

                            showNotification('Modifications sauvegardées avec succès !');
                        } else {
                            showNotification(data.error || 'Erreur lors de la sauvegarde', 'danger');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau:', error.message);
                        showNotification('Une erreur est survenue: ' + error.message, 'danger');
                    });
            }

            // Valider le formulaire
            function validateForm() {
                let isValid = true;
                const title = document.getElementById('campaignTitle').textContent.trim();
                const content = document.getElementById('content').value.trim();
                const startDate = document.getElementById('startDate').value;
                const endDate = document.getElementById('endDate').value;
                const contactInfo = document.getElementById('contactInfo').value.trim();
                const videoUrl = document.getElementById('videoUrl').value.trim();
                const websiteUrl = document.getElementById('websiteUrl').value.trim();

                if (!title || title.length < 10) {
                    showNotification('Le titre doit contenir au moins 10 caractères', 'danger');
                    isValid = false;
                }

                if (!content || content.length < 100) {
                    showNotification('Le contenu doit contenir au moins 100 caractères', 'danger');
                    isValid = false;
                }

                if (new Date(endDate) <= new Date(startDate)) {
                    showNotification('La date de fin doit être postérieure à la date de début', 'danger');
                    isValid = false;
                }

                if (contactInfo && contactInfo.length > 1000) {
                    showNotification('Les informations de contact ne doivent pas dépasser 1000 caractères', 'danger');
                    isValid = false;
                }

                if (videoUrl && !/^(https?:\/\/)/i.test(videoUrl)) {
                    showNotification('L\'URL de la vidéo doit être valide', 'danger');
                    isValid = false;
                }

                if (websiteUrl && !/^(https?:\/\/)/i.test(websiteUrl)) {
                    showNotification('L\'URL du site web doit être valide', 'danger');
                    isValid = false;
                }

                return isValid;
            }

            // Mettre à jour le badge de statut
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

            // Gestion de l'upload d'image
            function setupImageUpload() {
                document.querySelectorAll('.image-upload-overlay').forEach(overlay => {
                    overlay.addEventListener('click', () => {
                        if (isEditMode) {
                            const index = overlay.parentElement.dataset.imageIndex;
                            document.getElementById(`imageUpload${index}`).click();
                        }
                    });
                });

                document.querySelectorAll('input[type="file"]').forEach(input => {
                    input.addEventListener('change', (e) => {
                        const file = e.target.files[0];
                        const index = e.target.dataset.imageIndex;
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                document.getElementById(`campaignImage${index}`).src = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                    });
                });
            }

            // Ajouter un emplacement pour une nouvelle image
            function addImageSlot() {
                const gallery = document.querySelector('.campaign-image-gallery');
                const newIndex = imageCount++;
                const newImage = document.createElement('div');
                newImage.className = 'campaign-image';
                newImage.dataset.imageIndex = newIndex;
                newImage.innerHTML = `
                <img src="https://via.placeholder.com/800x300?text=Image" alt="{{ $campaign->title }}" id="campaignImage${newIndex}">
                <div class="image-upload-overlay" id="imageUploadOverlay${newIndex}">
                    <div>
                        <i class="bi bi-camera"></i>
                        <div>Cliquer pour ajouter une image</div>
                    </div>
                </div>
                <input type="file" id="imageUpload${newIndex}" accept="image/*" style="display: none;" data-image-index="${newIndex}">
                <button class="remove-image-btn" onclick="removeImage(${newIndex})" style="display: ${isEditMode ? 'inline-flex' : 'none'};">
                    <i class="bi bi-trash"></i>
                </button>
            `;
                gallery.appendChild(newImage);
                setupImageUpload();
            }

            // Supprimer une image
            function removeImage(index) {
                const imageDiv = document.querySelector(`.campaign-image[data-image-index="${index}"]`);
                if (imageDiv) {
                    const deletedImages = JSON.parse(localStorage.getItem('deletedImages') || '[]');
                    const imgSrc = document.getElementById(`campaignImage${index}`).src;
                    if (!imgSrc.includes('placeholder')) {
                        deletedImages.push(imgSrc.replace('{{ url('/') }}/storage/', ''));
                    }
                    localStorage.setItem('deletedImages', JSON.stringify(deletedImages));
                    imageDiv.remove();
                    showNotification('Image supprimée. Enregistrez pour confirmer.', 'success');
                }
            }

            // Gestion des compteurs de caractères
            function setupCharacterCounters() {
                const content = document.getElementById('content');
                const contactInfo = document.getElementById('contactInfo');
                const contentCount = document.getElementById('contentCount');
                const contactInfoCount = document.getElementById('contactInfoCount');

                content.addEventListener('input', () => {
                    contentCount.textContent = content.value.length;
                });

                contactInfo.addEventListener('input', () => {
                    contactInfoCount.textContent = contactInfo.value.length;
                });
            }

            // Gestion des listes dynamiques
            function addListItem(listId) {
                const list = document.getElementById(listId);
                const index = list.querySelectorAll('.list-item').length;
                const fieldName = listId === 'objectivesList' ? `objectives[${index}]` : `actions[${index}]`;
                const item = document.createElement('div');
                item.className = 'list-item';
                item.innerHTML = `
                <textarea class="form-control editable" data-field="${fieldName}"></textarea>
                <button class="remove-btn" onclick="removeListItem(this)" style="display: ${isEditMode ? 'inline-flex' : 'none'};"><i class="bi bi-trash"></i></button>
            `;
                list.appendChild(item);
                if (isEditMode) {
                    item.querySelector('.editable').disabled = false;
                }
            }

            function removeListItem(button) {
                button.parentElement.remove();
            }

            // Dupliquer la campagne
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
                                showNotification('Campagne dupliquée ! Redirection vers la nouvelle campagne...');
                                window.location.href = `/admin/campaigns/${data.campaign_id}`;
                            } else {
                                showNotification(data.error || 'Erreur lors de la duplication', 'danger');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur réseau:', error.message);
                            showNotification('Une erreur est survenue: ' + error.message, 'danger');
                        });
                }
            }

            // Exporter les données
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
                        showNotification('Données exportées avec succès !');
                    })
                    .catch(error => {
                        console.error('Erreur réseau:', error.message);
                        showNotification('Une erreur est survenue: ' + error.message, 'danger');
                    });
            }

            // Défilement vers les commentaires
            function scrollToComments() {
                const commentsSection = document.getElementById('commentsSection');
                commentsSection.style.display = 'block';
                commentsSection.scrollIntoView({ behavior: 'smooth' });
                loadComments();
            }

            // Charger les commentaires via AJAX
            function loadComments() {
                document.getElementById('loadingOverlay').classList.add('show');
                fetch(`/admin/campaigns/${campaignId}/comments`, {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${jwtToken}`,
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('loadingOverlay').classList.remove('show');
                        if (data.success) {
                            const commentsList = document.getElementById('commentsList');
                            commentsList.innerHTML = '';
                            if (data.comments.length === 0) {
                                commentsList.innerHTML = `
                                <div class="empty-state">
                                    <div class="empty-icon">
                                        <i class="bi bi-chat"></i>
                                    </div>
                                    <h3 class="empty-title">Aucun commentaire</h3>
                                    <p class="empty-description">Aucun commentaire n'a été publié pour cette campagne.</p>
                                </div>
                            `;
                            } else {
                                data.comments.forEach(comment => {
                                    const commentItem = document.createElement('div');
                                    commentItem.className = 'comment-item';
                                    commentItem.dataset.commentId = comment.id;
                                    commentItem.innerHTML = `
                                    <div class="comment-header">
                                        <span class="comment-author">${comment.user}</span>
                                        <span class="comment-date">${comment.created_at}</span>
                                    </div>
                                    <div class="comment-content">${comment.content}</div>
                                    <div class="comment-actions">
                                        <button class="action-btn danger" onclick="deleteComment(${comment.id})">
                                            <i class="bi bi-trash"></i>
                                            Supprimer
                                        </button>
                                    </div>
                                `;
                                    commentsList.appendChild(commentItem);
                                });
                            }
                        } else {
                            showNotification(data.error || 'Erreur lors du chargement des commentaires', 'danger');
                        }
                    })
                    .catch(error => {
                        document.getElementById('loadingOverlay').classList.remove('show');
                        console.error('Erreur réseau:', error.message);
                        showNotification('Une erreur est survenue: ' + error.message, 'danger');
                    });
            }

            // Supprimer un commentaire
            function deleteComment(commentId) {
                if (confirm('Voulez-vous supprimer ce commentaire ? Cette action est irréversible.')) {
                    document.getElementById('loadingOverlay').classList.add('show');
                    fetch(`/admin/campaigns/${campaignId}/comments/${commentId}`, {
                        method: 'DELETE',
                        headers: {
                            'Authorization': `Bearer ${jwtToken}`,
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => response.json())
                        .then(data => {
                            document.getElementById('loadingOverlay').classList.remove('show');
                            if (data.success) {
                                const commentItem = document.querySelector(`.comment-item[data-comment-id="${commentId}"]`);
                                if (commentItem) {
                                    commentItem.remove();
                                    showNotification('Commentaire supprimé avec succès !');
                                }
                                if (!document.querySelector('.comment-item')) {
                                    document.getElementById('commentsList').innerHTML = `
                                    <div class="empty-state">
                                        <div class="empty-icon">
                                            <i class="bi bi-chat"></i>
                                        </div>
                                        <h3 class="empty-title">Aucun commentaire</h3>
                                        <p class="empty-description">Aucun commentaire n'a été publié pour cette campagne.</p>
                                    </div>
                                `;
                                }
                            } else {
                                showNotification(data.error || 'Erreur lors de la suppression du commentaire', 'danger');
                            }
                        })
                        .catch(error => {
                            document.getElementById('loadingOverlay').classList.remove('show');
                            console.error('Erreur réseau:', error.message);
                            showNotification('Une erreur est survenue: ' + error.message, 'danger');
                        });
                }
            }

            // Supprimer la campagne
            function deleteCampaign() {
                if (confirm('ATTENTION : Êtes-vous sûr de vouloir supprimer définitivement cette campagne ?\n\nCette action est irréversible et supprimera également tous les commentaires et statistiques associés.')) {
                    document.getElementById('deleteModal').classList.add('active');
                    document.getElementById('confirmDeleteBtn').onclick = () => {
                        document.getElementById('loadingOverlay').classList.add('show');
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
                                document.getElementById('loadingOverlay').classList.remove('show');
                                if (data.success) {
                                    showNotification('Campagne supprimée. Redirection vers la liste...');
                                    setTimeout(() => {
                                        window.location.href = '{{ route("admin.campaigns.index") }}';
                                    }, 1000);
                                } else {
                                    showNotification(data.error || 'Erreur lors de la suppression', 'danger');
                                }
                            })
                            .catch(error => {
                                document.getElementById('loadingOverlay').classList.remove('show');
                                console.error('Erreur réseau:', error.message);
                                showNotification('Une erreur est survenue: ' + error.message, 'danger');
                            });
                    };
                }
            }

            // Fermer le modal de suppression
            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.remove('active');
            }

            // Configurer les raccourcis clavier
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

            // Configurer l'auto-sauvegarde
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

            // Initialisation au chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                setupImageUpload();
                setupCharacterCounters();
                setupKeyboardShortcuts();
                setupAutoSave();

                // Initialiser les compteurs
                document.getElementById('contentCount').textContent = document.getElementById('content').value.length;
                document.getElementById('contactInfoCount').textContent = document.getElementById('contactInfo').value.length;

                // Réinitialiser les images supprimées
                localStorage.removeItem('deletedImages');
            });

            // Alerte avant de quitter la page en mode édition
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
