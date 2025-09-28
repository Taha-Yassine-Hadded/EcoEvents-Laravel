@extends('layouts.app')

@section('title', 'Campagnes de Sensibilisation - Echofy')

@section('content')
    <!-- Overlay de chargement -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Container de notifications -->
    <div id="notification-container"></div>
<h1></h1>
    <section class="campaigns-area">
        <div class="container">
            <!-- En-tête de section -->
            <div class="section-header fade-in">
                <div class="section-subtitle">
                    <i class="bi bi-leaf"></i>

                </div>
                <h1 class="section-title">Campagnes de Sensibilisation Environnementale</h1>
                <p class="section-description">
                    Découvrez nos initiatives pour un avenir plus durable et rejoignez notre communauté engagée pour l'environnement.
                </p>
            </div>

            <!-- Filtres -->
            <div class="filters-container fade-in stagger-delay-1">
                <div class="filters">
                    <div class="filter-group">
                        <label class="filter-label">Rechercher une campagne</label>
                        <div class="search-container">
                            <i class="bi bi-search search-icon"></i>
                            <input type="text" class="search-input" name="search" placeholder="Tapez pour rechercher..." id="searchInput" value="{{ $search ?? '' }}">
                        </div>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Catégorie</label>
                        <div class="custom-select">
                            <select class="select-input" name="category" id="categorySelect">
                                <option value="all" {{ $category == 'all' ? 'selected' : '' }}>Toutes les catégories</option>
                                <option value="recyclage" {{ $category == 'recyclage' ? 'selected' : '' }}>Recyclage</option>
                                <option value="climat" {{ $category == 'climat' ? 'selected' : '' }}>Climat</option>
                                <option value="biodiversite" {{ $category == 'biodiversite' ? 'selected' : '' }}>Biodiversité</option>
                                <option value="eau" {{ $category == 'eau' ? 'selected' : '' }}>Eau</option>
                                <option value="energie" {{ $category == 'energie' ? 'selected' : '' }}>Énergie</option>
                                <option value="transport" {{ $category == 'transport' ? 'selected' : '' }}>Transport</option>
                                <option value="alimentation" {{ $category == 'alimentation' ? 'selected' : '' }}>Alimentation</option>
                                <option value="pollution" {{ $category == 'pollution' ? 'selected' : '' }}>Pollution</option>
                                <option value="sensibilisation" {{ $category == 'sensibilisation' ? 'selected' : '' }}>Sensibilisation</option>
                            </select>
                            <i class="bi bi-chevron-down select-arrow"></i>
                        </div>
                    </div>
                    <div class="filter-group">
                        <label class="filter-label">Statut</label>
                        <div class="custom-select">
                            <select class="select-input" name="status" id="statusSelect">
                                <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tous les statuts</option>
                                <option value="upcoming" {{ $status == 'upcoming' ? 'selected' : '' }}>À venir</option>
                                <option value="active" {{ $status == 'active' ? 'selected' : '' }}>En cours</option>
                                <option value="ended" {{ $status == 'ended' ? 'selected' : '' }}>Terminé</option>
                            </select>
                            <i class="bi bi-chevron-down select-arrow"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Grille des campagnes -->
            <div class="campaigns-grid" id="campaignsGrid">
                @forelse ($campaigns as $index => $campaign)
                    <div class="campaign-card single-campaign-box fade-in stagger-delay-{{ min($index + 1, 6) }}" data-category="{{ $campaign->category }}" onclick="goToCampaignDetail({{ $campaign->id }})">
                        <div class="campaign-image campaign-thumb">
                            <img src="{{ !empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($campaign->media_urls['images'][0]) ? \Illuminate\Support\Facades\Storage::url($campaign->media_urls['images'][0]) : asset('assets/images/home6/placeholder.jpg') }}" alt="{{ $campaign->title }}">
                            <div class="campaign-overlay"></div>
                            <div class="campaign-status">{{ ucfirst($campaign->status) }}</div>
                        </div>
                        <div class="campaign-content">
                            <div class="campaign-category campaign-text">{{ ucfirst($campaign->category) }}</div>
                            <h3 class="campaign-title">{{ \Illuminate\Support\Str::limit($campaign->title, 50) }}</h3>
                            <p class="campaign-description">{{ \Illuminate\Support\Str::limit(strip_tags($campaign->content), 150) }}</p>
                            <div class="campaign-dates">
                                <div class="date-item">
                                    <i class="bi bi-calendar2-event"></i>
                                    <span>{{ $campaign->start_date->format('d/m/Y') }}</span>
                                </div>
                                <div class="date-item">
                                    <i class="bi bi-calendar2-check"></i>
                                    <span>{{ $campaign->end_date->format('d/m/Y') }}</span>
                                </div>
                            </div>
                            <div class="campaign-footer campaign-actions">
                                <div class="campaign-stats">
                                    <div class="stat-item">
                                        <i class="bi bi-eye"></i>
                                        <span>{{ $campaign->views_count ?? 0 }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="bi bi-heart"></i>
                                        <span>{{ $campaign->likes_count ?? 0 }}</span>
                                    </div>
                                    <div class="stat-item">
                                        <i class="bi bi-chat"></i>
                                        <span>{{ $campaign->comments_count ?? 0 }}</span>
                                    </div>
                                </div>
                                <div class="campaign-actions action-buttons">
                                    <button class="action-btn {{ auth()->check() && auth()->user()->likedCampaigns()->where('campaign_id', $campaign->id)->exists() ? 'liked' : '' }}" onclick="event.stopPropagation(); toggleLike(this, {{ $campaign->id }})">
                                        <i class="bi bi-heart"></i>
                                    </button>
                                    <button class="action-btn" onclick="event.stopPropagation(); shareCampaign({{ $campaign->id }})">
                                        <i class="bi bi-share"></i>
                                    </button>
                                </div>
                            </div>
                            <a href="{{ url('/campaigns/' . $campaign->id) }}" class="cta-button echofy-button style-five" onclick="event.stopPropagation();">
                                En savoir plus
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-search"></i>
                        </div>
                        <h3 class="empty-title">Aucune campagne trouvée</h3>
                        <p class="empty-description">
                            Essayez de modifier vos critères de recherche ou de filtrage
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection

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

        .campaigns-area {
            padding: 80px 0;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
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
        .section-header {
            text-align: center;
            margin-bottom: 60px;
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
            font-size: 3.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 24px;
        }

        .section-description {
            font-size: 1.2rem;
            color: #64748b;
            max-width: 600px;
            margin: 0 auto;
        }

        /* Filtres modernes */
        .filters-container {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            padding: 32px;
            margin-bottom: 48px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
        }

        .filters {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: 20px;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 0.9rem;
            font-weight: 600;
            color: #475569;
            margin-bottom: 4px;
        }

        .search-container {
            position: relative;
        }

        .search-input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .search-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .search-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #10b981;
            font-size: 1.2rem;
        }

        .custom-select {
            position: relative;
        }

        .select-input {
            width: 100%;
            padding: 16px 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 1rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            appearance: none;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .select-input:focus {
            outline: none;
            border-color: #10b981;
            box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.1);
        }

        .select-arrow {
            position: absolute;
            right: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            pointer-events: none;
            transition: transform 0.3s ease;
        }

        .custom-select:hover .select-arrow {
            transform: translateY(-50%) rotate(180deg);
        }

        /* Grille des campagnes */
        .campaigns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(380px, 1fr));
            gap: 32px;
        }

        .campaign-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 24px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            position: relative;
        }

        .campaign-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .campaign-image {
            position: relative;
            height: 220px;
            overflow: hidden;
        }

        .campaign-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .campaign-card:hover .campaign-image img {
            transform: scale(1.1);
        }

        .campaign-status {
            position: absolute;
            top: 16px;
            right: 16px;
            padding: 8px 16px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .campaign-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.2));
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .campaign-card:hover .campaign-overlay {
            opacity: 1;
        }

        .campaign-content {
            padding: 28px;
        }

        .campaign-category {
            display: inline-block;
            padding: 6px 16px;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.1));
            color: #059669;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .campaign-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 12px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .campaign-description {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 20px;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .campaign-dates {
            display: flex;
            justify-content: space-between;
            margin-bottom: 24px;
        }

        .date-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .date-item i {
            color: #10b981;
        }

        .campaign-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid rgba(226, 232, 240, 0.5);
        }

        .campaign-stats {
            display: flex;
            gap: 20px;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-item i {
            color: #10b981;
            font-size: 1rem;
        }

        .campaign-actions {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .action-btn:hover {
            background: #10b981;
            color: white;
            transform: scale(1.1);
        }

        .action-btn.liked {
            background: rgba(239, 68, 68, 0.1);
            color: #ef4444;
        }

        .action-btn.liked:hover {
            background: #ef4444;
            color: white;
        }

        .action-btn.liked i {
            color: #ef4444;
        }

        .action-btn.liked:hover i {
            color: white;
        }

        .cta-button {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 16px;
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }

        .cta-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
            color: white;
            text-decoration: none;
        }

        /* État vide */
        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .empty-icon {
            font-size: 4rem;
            color: #cbd5e1;
            margin-bottom: 24px;
        }

        .empty-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 12px;
        }

        .empty-description {
            color: #64748b;
            font-size: 1.1rem;
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
            .campaigns-grid {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 24px;
            }

            .filters {
                grid-template-columns: 1fr;
                gap: 16px;
            }
        }

        @media (max-width: 768px) {
            .section-title {
                font-size: 2.5rem;
            }

            .filters-container {
                padding: 24px 20px;
                margin: 0 -20px 32px;
                border-radius: 0;
            }

            .campaigns-grid {
                grid-template-columns: 1fr;
                gap: 20px;
            }

            .campaign-card {
                margin: 0 -10px;
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
        .stagger-delay-6 { animation-delay: 0.6s; }
    </style>
@endpush

@push('scripts')
    <script>
        // Afficher une notification temporaire
        function showNotification(message, type = 'success') {
            console.log(`Affichage de la notification: ${message} (${type})`);
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

        // Recherche avec rechargement
        document.getElementById('searchInput').addEventListener('input', function() {
            const form = document.getElementById('filterForm');
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => {
                form.submit();
            }, 500); // Débouncer de 500ms
        });

        // Filtrage AJAX pour catégorie et statut
        function filterCampaigns() {
            const category = document.getElementById('categorySelect').value;
            const status = document.getElementById('statusSelect').value;
            console.log('Filtrage des campagnes:', { category, status });

            fetch('{{ route("api.campaigns.filter") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ category, status })
            })
                .then(response => {
                    console.log('Réponse HTTP (filtrage):', response.status, response.statusText);
                    if (!response.ok) {
                        throw new Error('Erreur réseau');
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues (filtrage):', data);
                    if (data.success) {
                        updateCampaignsGrid(data.campaigns);
                        showNotification('Campagnes filtrées avec succès !');
                    } else {
                        showNotification(data.error || 'Erreur lors du filtrage', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du filtrage:', error);
                    showNotification('Une erreur est survenue', 'danger');
                });
        }

        // Mettre à jour la grille des campagnes
        function updateCampaignsGrid(campaigns) {
            const grid = document.querySelector('.campaigns-grid');
            grid.innerHTML = '';

            if (campaigns.length === 0) {
                grid.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-search"></i>
                        </div>
                        <h3 class="empty-title">Aucune campagne trouvée</h3>
                        <p class="empty-description">
                            Essayez de modifier vos critères de recherche ou de filtrage
                        </p>
                    </div>
                `;
                return;
            }

            campaigns.forEach(campaign => {
                const isLiked = {{ auth()->check() ? '!!' . auth()->user()->likedCampaigns()->where('campaign_id', "' + campaign.id + '").exists() : 'false' }};
                const campaignBox = document.createElement('div');
                campaignBox.className = 'campaign-card single-campaign-box';
                campaignBox.setAttribute('data-category', campaign.category);
                campaignBox.onclick = () => goToCampaignDetail(campaign.id);
                campaignBox.innerHTML = `
                    <div class="campaign-image campaign-thumb">
                        <img src="${campaign.thumbnail}" alt="${campaign.title}">
                        <div class="campaign-overlay"></div>
                        <div class="campaign-status">${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}</div>
                    </div>
                    <div class="campaign-content">
                        <div class="campaign-category campaign-text">${campaign.category.charAt(0).toUpperCase() + campaign.category.slice(1)}</div>
                        <h3 class="campaign-title">${campaign.title.substring(0, 50)}${campaign.title.length > 50 ? '...' : ''}</h3>
                        <p class="campaign-description">${campaign.content.substring(0, 150)}${campaign.content.length > 150 ? '...' : ''}</p>
                        <div class="campaign-dates">
                            <div class="date-item">
                                <i class="bi bi-calendar2-event"></i>
                                <span>${campaign.start_date}</span>
                            </div>
                            <div class="date-item">
                                <i class="bi bi-calendar2-check"></i>
                                <span>${campaign.end_date}</span>
                            </div>
                        </div>
                        <div class="campaign-footer campaign-actions">
                            <div class="campaign-stats">
                                <div class="stat-item">
                                    <i class="bi bi-eye"></i>
                                    <span>${campaign.views_count}</span>
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-heart"></i>
                                    <span>${campaign.likes_count}</span>
                                </div>
                                <div class="stat-item">
                                    <i class="bi bi-chat"></i>
                                    <span>${campaign.comments_count}</span>
                                </div>
                            </div>
                            <div class="campaign-actions action-buttons">
                                <button class="action-btn ${isLiked ? 'liked' : ''}" onclick="event.stopPropagation(); toggleLike(this, ${campaign.id})">
                                    <i class="bi bi-heart"></i>
                                </button>
                                <button class="action-btn" onclick="event.stopPropagation(); shareCampaign(${campaign.id})">
                                    <i class="bi bi-share"></i>
                                </button>
                            </div>
                        </div>
                        <a href="/campaigns/${campaign.id}" class="cta-button echofy-button style-five" onclick="event.stopPropagation();">
                            En savoir plus
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                `;
                grid.appendChild(campaignBox);
            });
        }

        // Événements pour les filtres
        document.getElementById('categorySelect').addEventListener('change', filterCampaigns);
        document.getElementById('statusSelect').addEventListener('change', filterCampaigns);

        // Navigation vers le détail
        function goToCampaignDetail(campaignId) {
            window.location.href = '{{ url("/campaigns") }}/' + campaignId;
        }

        // Partage campagne
        function shareCampaign(campaignId) {
            if (navigator.share) {
                navigator.share({
                    title: 'Echofy - Campagne de Sensibilisation',
                    text: 'Découvrez cette campagne environnementale sur Echofy',
                    url: window.location.origin + '/campaigns/' + campaignId
                });
            } else {
                const url = window.location.origin + '/campaigns/' + campaignId;
                navigator.clipboard.writeText(url).then(() => {
                    showNotification('Lien copié dans le presse-papiers !');
                });
            }
        }

        // Toggle like
        function toggleLike(button, campaignId) {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                showNotification('Vous devez être connecté pour aimer une campagne.', 'danger');
                window.location.href = '{{ route("login") }}';
                return;
            }

            fetch('/api/campaigns/' + campaignId + '/like', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.classList.toggle('liked');
                        const heartIcon = button.querySelector('i');
                        if (button.classList.contains('liked')) {
                            heartIcon.style.color = '#ef4444';
                        } else {
                            heartIcon.style.color = '#10b981';
                        }
                        const stats = button.closest('.campaign-actions').querySelector('.stat-item:nth-child(2) span');
                        stats.textContent = data.likes_count;
                        showNotification(data.action === 'liked' ? 'Merci pour votre réactivité !' : 'Like retiré.');
                    } else {
                        showNotification(data.error || 'Erreur lors de l\'action', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Une erreur est survenue', 'danger');
                });
        }
    </script>
@endpush
