
@extends('layouts.app')

@section('title', 'Campagnes de Sensibilisation - Echofy')

@section('content')
    <!-- Overlay de chargement -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>

    <!-- Container de notifications -->
    <div id="notification-container"></div>

    <section class="campaigns-area">
        <div class="container">
            <!-- En-tête de section -->
            <div class="section-header fade-in">
                <div class="section-subtitle">
                    <i class="bi bi-leaf"></i>
                </div>
                <h1 class="section-title">Campagnes de Sensibilisation Environnementale</h1>
                <p class="section-description">
                    Découvrez toutes nos initiatives pour un avenir plus durable et rejoignez notre communauté engagée pour l'environnement.
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

            <!-- Formulaire caché pour maintenir les filtres -->
            <form id="filterForm" method="GET" action="{{ route('front.campaigns.index') }}" style="display: none;">
                <input type="hidden" name="search" id="searchHidden" value="{{ $search ?? '' }}">
                <input type="hidden" name="category" id="categoryHidden" value="{{ $category ?? 'all' }}">
                <input type="hidden" name="status" id="statusHidden" value="{{ $status ?? 'all' }}">
            </form>

            <!-- Grille des campagnes -->
            <div class="campaigns-grid" id="campaignsGrid">
                @forelse ($campaigns as $index => $campaign)
                    <div class="campaign-card single-campaign-box fade-in stagger-delay-{{ min($index + 1, 6) }}"
                         data-category="{{ $campaign->category }}"
                         onclick="goToCampaignDetail({{ $campaign->id }})">
                        <div class="campaign-image campaign-thumb">
                            @php
                                $imageUrl = $campaign->media_urls['images'][0] ?? null;
                                $imagePath = $imageUrl ? Storage::url($imageUrl) : asset('assets/images/home6/placeholder.jpg');
                            @endphp
                            <img src="{{ $imagePath }}" alt="{{ $campaign->title }}">
                            <div class="campaign-overlay"></div>
                            <div class="campaign-status">{{ ucfirst($campaign->status) }}</div>
                        </div>

                        <div class="campaign-content">
                            <div class="campaign-category campaign-text">{{ ucfirst($campaign->category) }}</div>
                            <h3 class="campaign-title">{{ Str::limit($campaign->title, 50) }}</h3>
                            <p class="campaign-description">{{ Str::limit(strip_tags($campaign->content), 150) }}</p>
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
                                    @auth
                                        @php
                                            $isLiked = auth()->user()->likedCampaigns()->where('campaign_id', $campaign->id)->exists();
                                        @endphp
                                        <button class="action-btn {{ $isLiked ? 'liked' : '' }}"
                                                onclick="event.stopPropagation(); toggleLike(this, {{ $campaign->id }})">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    @else
                                        <button class="action-btn" onclick="event.stopPropagation(); window.location.href='{{ route('login') }}';">
                                            <i class="bi bi-heart"></i>
                                        </button>
                                    @endauth
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

            <!-- Pagination -->
            <div class="pagination-container fade-in" id="paginationContainer">
                @if(isset($campaigns) && method_exists($campaigns, 'links') && $campaigns->hasPages())
                    {{ $campaigns->appends(request()->query())->links('pagination::bootstrap-5') }}
                @else
                    <div class="text-center text-muted py-4">
                        @if(isset($campaigns) && $campaigns->count() > 0)
                            Affichage de {{ $campaigns->count() }} campagne(s)
                        @else
                            Aucune campagne trouvée
                        @endif
                    </div>
                @endif
            </div>

            <!-- 🆕 SECTION RECOMMANDÉE -->
            @auth
                <section class="recommended-section">
                    <div class="section-header fade-in">
                        <h2 class="section-title recommended-title">Recommandé pour vous</h2>

                    </div>

                    <div class="recommendation-list" id="recommendedGrid">
                        @if(isset($recommendedCampaigns) && count($recommendedCampaigns) > 0)
                            <script>
                                window.recommendedCampaignsData = @json($recommendedCampaigns);
                                console.log('✅ Recommandations serveur:', window.recommendedCampaignsData);
                            </script>
                            @foreach($recommendedCampaigns as $campaign)
                                <div class="recommendation-item fade-in"
                                     data-category="{{ $campaign['category'] }}"
                                     onclick="goToCampaignDetail({{ $campaign['id'] }})">
                                    <div class="recommendation-image">
                                        <img src="{{ $campaign['image_url'] ?? asset('assets/images/home6/placeholder.jpg') }}"
                                             alt="{{ $campaign['title'] }}"
                                             loading="lazy">
                                        <div class="campaign-overlay"></div>
                                        <div class="campaign-status">{{ ucfirst($campaign['status']) }}</div>
                                    </div>
                                    <div class="recommendation-content">
                                        <div class="campaign-category campaign-text">{{ ucfirst($campaign['category']) }}</div>
                                        <h4 class="recommendation-title">{{ Str::limit($campaign['title'], 50) }}</h4>
                                        <p class="recommendation-description">{{ Str::limit($campaign['description'], 100) }}</p>
                                        <div class="campaign-dates">
                                            <div class="date-item">
                                                <i class="bi bi-calendar2-event"></i>
                                                <span>{{ \Carbon\Carbon::parse($campaign['start_date'])->format('d/m/Y') }}</span>
                                            </div>
                                            <div class="date-item">
                                                <i class="bi bi-calendar2-check"></i>
                                                <span>{{ \Carbon\Carbon::parse($campaign['end_date'])->format('d/m/Y') }}</span>
                                            </div>
                                        </div>
                                        <div class="campaign-footer campaign-actions">
                                            <div class="campaign-stats">
                                                <div class="stat-item">
                                                    <i class="bi bi-eye"></i>
                                                    <span>{{ $campaign['views_count'] ?? 0 }}</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="bi bi-heart"></i>
                                                    <span>{{ $campaign['likes_count'] ?? 0 }}</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="bi bi-chat"></i>
                                                    <span>{{ $campaign['comments_count'] ?? 0 }}</span>
                                                </div>
                                            </div>
                                            <div class="campaign-actions action-buttons">
                                                @auth
                                                    <button class="action-btn {{ $campaign['is_liked'] ? 'liked' : '' }}"
                                                            onclick="event.stopPropagation(); toggleLike(this, {{ $campaign['id'] }}, true)">
                                                        <i class="bi bi-heart"></i>
                                                    </button>
                                                @else
                                                    <button class="action-btn" onclick="event.stopPropagation(); window.location.href='{{ route('login') }}';">
                                                        <i class="bi bi-heart"></i>
                                                    </button>
                                                @endauth
                                                <button class="action-btn" onclick="event.stopPropagation(); shareCampaign({{ $campaign['id'] }})">
                                                    <i class="bi bi-share"></i>
                                                </button>
                                            </div>
                                        </div>
                                        <a href="/campaigns/{{ $campaign['id'] }}" class="cta-button echofy-button style-five" onclick="event.stopPropagation();">
                                            En savoir plus
                                            <i class="bi bi-arrow-right"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="bi bi-lightning-charge"></i>
                                </div>
                                <h3 class="empty-title">Aucune recommandation pour le moment</h3>
                                <p class="empty-description">
                                    Participez à nos campagnes (likes, commentaires, vues) pour recevoir des recommandations personnalisées !
                                </p>
                                <a href="#campaignsGrid" class="cta-button echofy-button style-five">
                                    Découvrir toutes les campagnes
                                    <i class="bi bi-arrow-down"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </section>
            @endauth
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
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Notification fixe */
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

        /* 🆕 SECTION RECOMMANDATIONS */
        .recommended-section {
            padding: 40px 0;
            margin-top: 60px;
        }

        .recommended-section .section-header {
            margin-bottom: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.06);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .recommendation-list {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-top: 24px;
            padding-top: 24px;
            border-top: 2px solid rgba(226, 232, 240, 0.5);
        }

        .recommendation-item {
            display: flex;
            gap: 20px;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 16px;
            border: 1px solid rgba(16, 185, 129, 0.2);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .recommendation-item:hover {
            background: rgba(16, 185, 129, 0.05);
            transform: translateY(-4px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
        }

        .recommendation-image {
            position: relative;
            width: 200px;
            height: 280px;
            overflow: hidden;
            border-radius: 12px;
            flex-shrink: 0;
        }

        .recommendation-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .recommendation-item:hover .recommendation-image img {
            transform: scale(1.1);
        }

        .recommendation-content {
            flex: 1;
        }

        .recommendation-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 8px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .recommendation-description {
            color: #64748b;
            font-size: 0.95rem;
            line-height: 1.6;
            margin-bottom: 12px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .loading-placeholder {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .placeholder-card {
            background: rgba(255, 255, 255, 0.6);
            border-radius: 16px;
            padding: 20px;
            height: 160px;
            animation: pulse 2s infinite;
            display: flex;
            gap: 20px;
        }

        .placeholder-image {
            width: 200px;
            height: 120px;
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            border-radius: 12px;
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            flex-shrink: 0;
        }

        .placeholder-content .placeholder-line {
            height: 16px;
            background: linear-gradient(90deg, #f1f5f9 25%, #e2e8f0 50%, #f1f5f9 75%);
            border-radius: 8px;
            margin-bottom: 12px;
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        .placeholder-line.short { width: 60%; }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
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
            font-size: 2.8rem;
            font-weight: 800;
            background: linear-gradient(135deg, #1e293b, #475569);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1.2;
            margin-bottom: 24px;
        }

        .recommended-title {
            font-size: 1.8rem;
            font-weight: 700;
            color: #1e293b;
            text-align: left;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .recommended-title::before {
            content: '✨';
            font-size: 1.6rem;
        }

        .section-description {
            font-size: 1.2rem;
            color: #64748b;
            max-width: 900px;
            margin: 0 auto;
        }

        .recommended-section .section-description {
            color: #64748b;
            font-weight: 500;
            font-size: 0.95rem;
        }

        /* Filtres */
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

        .filter-group { display: flex; flex-direction: column; gap: 8px; }
        .filter-label { font-size: 0.9rem; font-weight: 600; color: #475569; margin-bottom: 4px; }

        .search-container { position: relative; }
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

        .custom-select { position: relative; }
        .select-input {
            width: 100%;
            padding: 16px 20px;
            background: white;
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            font-size: 1rem;
            color: #475569;
            cursor: pointer;
            transition: all 0.3s;
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
            z-index: 1;
        }

        .campaign-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1), rgba(5, 150, 105, 0.2));
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .campaign-card:hover .campaign-overlay,
        .recommendation-item:hover .campaign-overlay {
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

        .date-item i { color: #10b981; }

        .campaign-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 20px;
            border-top: 1px solid rgba(226, 232, 240, 0.5);
        }

        .campaign-stats { display: flex; gap: 20px; }
        .stat-item {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #64748b;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-item i { color: #10b981; font-size: 1rem; }

        .campaign-actions { display: flex; gap: 8px; }
        .action-btn {
            width: 40px;
            height: 40px;
            border: none;
            background: rgba(16, 185, 129, 0.1);
            color: #10b981;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s;
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
            transition: all 0.3s;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
            margin-top: 16px;
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
            grid-column: 1 / -1;
        }

        .empty-icon { font-size: 4rem; color: #cbd5e1; margin-bottom: 24px; }
        .empty-title { font-size: 1.5rem; font-weight: 700; color: #475569; margin-bottom: 12px; }
        .empty-description { color: #64748b; font-size: 1.1rem; }

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

        /* Pagination */
        .pagination-container {
            margin-top: 60px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination { display: flex; justify-content: center; align-items: center; gap: 8px; padding: 20px 0; }
        .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 48px;
            height: 48px;
            border: 2px solid #e2e8f0;
            background: white;
            color: #64748b;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            transition: all 0.3s;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .page-link:hover {
            background: #10b981;
            color: white;
            border-color: #10b981;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.2);
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #10b981, #059669);
            border-color: #10b981;
            color: white;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
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

        /* Responsive */
        @media (max-width: 1024px) {
            .campaigns-grid {
                grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
                gap: 24px;
            }
            .filters { grid-template-columns: 1fr; gap: 16px; }
            .recommendation-item {
                flex-direction: row;
                gap: 16px;
            }
        }

        @media (max-width: 768px) {
            .section-title { font-size: 2.5rem; }
            .recommended-title { font-size: 1.5rem; }
            .filters-container { padding: 24px 20px; margin: 0 -20px 32px; border-radius: 0; }
            .campaigns-grid { grid-template-columns: 1fr; gap: 20px; }
            .recommended-section { padding: 20px 0; }
            .recommendation-item {
                flex-direction: column;
                gap: 12px;
            }
            .recommendation-image {
                width: 100%;
                height: 140px;
                border-radius: 12px 12px 0 0;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        window.isAuthenticated = @json(auth()->check());
        window.userId = @json(auth()->id() ?? null);
        window.csrfToken = '{{ csrf_token() }}';

        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.recommendedCampaignsData !== 'undefined' && window.recommendedCampaignsData.length > 0) {
                displayRecommendations(window.recommendedCampaignsData);
                setTimeout(fetchRecommendations, 1000);
            } else if (window.isAuthenticated) {
                loadRecommendations();
            }
        });

        async function loadRecommendations() {
            const token = localStorage.getItem('jwt_token');
            if (!token || !window.isAuthenticated) {
                console.log('Non authentifié ou token manquant');
                return;
            }

            showLoadingRecommendations();
            await fetchRecommendations();
        }

        async function fetchRecommendations() {
            try {
                const token = localStorage.getItem('jwt_token');
                const response = await fetch('{{ route("front.campaigns.recommendations") }}', {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    displayRecommendations(data.campaigns);
                    const countElement = document.getElementById('recommendationCount');
                    if (countElement) {
                        countElement.textContent = `(${data.count} sélectionnées)`;
                    }

                    if (data.count > 0) {
                        showNotification(`${data.count} recommandations personnalisées chargées !`);
                    }

                    setTimeout(fetchRecommendations, 30 * 60 * 1000);
                } else {
                    throw new Error('Erreur API');
                }
            } catch (error) {
                console.error('Erreur fetch recommandations:', error);
                if (typeof window.recommendedCampaignsData !== 'undefined') {
                    displayRecommendations(window.recommendedCampaignsData);
                } else {
                    showEmptyRecommendations();
                }
            }
        }

        function showLoadingRecommendations() {
            const grid = document.getElementById('recommendedGrid');
            if (grid) {
                grid.innerHTML = `
                    <div class="loading-placeholder">
                        <div class="placeholder-card">
                            <div class="placeholder-image"></div>
                            <div class="placeholder-content">
                                <div class="placeholder-line"></div>
                                <div class="placeholder-line short"></div>
                                <div class="placeholder-line"></div>
                            </div>
                        </div>
                        <div class="placeholder-card">
                            <div class="placeholder-image"></div>
                            <div class="placeholder-content">
                                <div class="placeholder-line"></div>
                                <div class="placeholder-line short"></div>
                                <div class="placeholder-line"></div>
                            </div>
                        </div>
                        <div class="placeholder-card">
                            <div class="placeholder-image"></div>
                            <div class="placeholder-content">
                                <div class="placeholder-line"></div>
                                <div class="placeholder-line short"></div>
                                <div class="placeholder-line"></div>
                            </div>
                        </div>
                    </div>
                `;
            }
        }

        function displayRecommendations(campaigns) {
            const grid = document.getElementById('recommendedGrid');
            if (!grid) return;

            if (!campaigns || campaigns.length === 0) {
                showEmptyRecommendations();
                return;
            }

            grid.innerHTML = campaigns.map(campaign => `
                <div class="recommendation-item fade-in"
                     data-category="${campaign.category}"
                     onclick="goToCampaignDetail(${campaign.id})">
                    <div class="recommendation-image">
                        <img src="${campaign.image_url || '{{ asset("assets/images/home6/placeholder.jpg") }}'}"
                             alt="${campaign.title}"
                             loading="lazy"
                             onerror="this.src='{{ asset('assets/images/home6/placeholder.jpg') }}'">
                        <div class="campaign-overlay"></div>
                        <div class="campaign-status">${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}</div>
                    </div>
                    <div class="recommendation-content">
                        <div class="campaign-category campaign-text">${campaign.category.charAt(0).toUpperCase() + campaign.category.slice(1)}</div>
                        <h4 class="recommendation-title">${campaign.title}</h4>
                        <p class="recommendation-description">${campaign.description}</p>
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
                                <div class="stat-item"><i class="bi bi-eye"></i><span>${campaign.views_count || 0}</span></div>
                                <div class="stat-item"><i class="bi bi-heart"></i><span>${campaign.likes_count || 0}</span></div>
                                <div class="stat-item"><i class="bi bi-chat"></i><span>${campaign.comments_count || 0}</span></div>
                            </div>
                            <div class="campaign-actions action-buttons">
                                <button class="action-btn ${campaign.is_liked ? 'liked' : ''}"
                                        onclick="event.stopPropagation(); toggleLike(this, ${campaign.id}, true)">
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
                </div>
            `).join('');
        }

        function showEmptyRecommendations() {
            const grid = document.getElementById('recommendedGrid');
            if (grid) {
                grid.innerHTML = `
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="bi bi-lightning-charge"></i>
                        </div>
                        <h3 class="empty-title">Aucune recommandation pour le moment</h3>
                        <p class="empty-description">
                            Participez à nos campagnes (likes, commentaires, vues) pour recevoir des recommandations personnalisées !
                        </p>
                        <a href="#campaignsGrid" class="cta-button echofy-button style-five">
                            Découvrir toutes les campagnes
                            <i class="bi bi-arrow-down"></i>
                        </a>
                    </div>
                `;
            }
        }

        function toggleLike(button, campaignId, isRecommended = false) {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                showNotification('Vous devez être connecté pour aimer une campagne.', 'danger');
                window.location.href = '{{ route("login") }}';
                return;
            }

            fetch(`/campaigns/${campaignId}/like`, {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.classList.toggle('liked');
                        const heartIcon = button.querySelector('i');
                        if (heartIcon) {
                            heartIcon.style.color = button.classList.contains('liked') ? '#ef4444' : '#10b981';
                        }

                        const stats = button.closest('.campaign-actions')?.querySelector('.stat-item:nth-child(2) span');
                        if (stats) {
                            stats.textContent = data.likes_count;
                        }

                        showNotification(data.action === 'liked' ? 'Campagne aimée !' : 'Like retiré.');

                        if (isRecommended) {
                            invalidateRecommendationsCache();
                        }
                    } else {
                        showNotification(data.error || 'Erreur', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    showNotification('Erreur lors du like', 'danger');
                });
        }

        function invalidateRecommendationsCache() {
            const token = localStorage.getItem('jwt_token');
            if (!token) return;

            fetch('{{ route("front.campaigns.invalidate-cache") }}', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
                .then(() => {
                    console.log('Cache invalidé');
                    fetchRecommendations();
                })
                .catch(err => console.error('Erreur invalidation:', err));
        }

        function showNotification(message, type = 'success') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} show`;
            notification.textContent = message;
            document.getElementById('notification-container').appendChild(notification);
            setTimeout(() => notification.remove(), 3000);
        }

        function shareCampaign(campaignId) {
            if (navigator.share) {
                navigator.share({
                    title: 'Echofy - Campagne',
                    url: `${window.location.origin}/campaigns/${campaignId}`
                });
            } else {
                navigator.clipboard.writeText(`${window.location.origin}/campaigns/${campaignId}`)
                    .then(() => showNotification('Lien copié !'));
            }
        }

        let searchTimeout;
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', function() {
                const form = document.getElementById('filterForm');
                if (form) {
                    clearTimeout(searchTimeout);
                    searchTimeout = setTimeout(() => form.submit(), 500);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const categorySelect = document.getElementById('categorySelect');
            const statusSelect = document.getElementById('statusSelect');

            if (categorySelect) categorySelect.value = '{{ $category ?? "all" }}';
            if (statusSelect) statusSelect.value = '{{ $status ?? "all" }}';

            [categorySelect, statusSelect].forEach(select => {
                if (select) {
                    select.addEventListener('change', () => {
                        const form = document.getElementById('filterForm');
                        if (form) form.submit();
                    });
                }
            });
        });

        function goToCampaignDetail(campaignId) {
            window.location.href = `/campaigns/${campaignId}`;
        }

        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('page-link') && !e.target.closest('.active')) {
                e.preventDefault();
                const url = new URL(e.target.href, window.location.origin);
                const search = document.getElementById('searchInput')?.value || '';
                const category = document.getElementById('categorySelect')?.value || 'all';
                const status = document.getElementById('statusSelect')?.value || 'all';

                if (search) url.searchParams.set('search', search);
                if (category !== 'all') url.searchParams.set('category', category);
                if (status !== 'all') url.searchParams.set('status', status);

                window.location.href = url.toString();
            }
        });
    </script>
@endpush
