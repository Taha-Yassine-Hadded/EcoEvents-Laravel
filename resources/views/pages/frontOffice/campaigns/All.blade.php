@extends('layouts.app')

@section('title', 'Campagnes de Sensibilisation - Echofy')

@section('content')
    <!--==================================================-->
    <!-- Start Echofy Campaigns Area -->
    <!--==================================================-->
    <section class="campaigns-area home-six">
        <div class="container">
            <!-- Notification Container -->
            <div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 1000;"></div>

            <div class="row">
                <div class="col-md-12 text-center">
                    <div class="section-title center">
                        <h4><img src="{{ asset('assets/images/home6/section-title-shape.png') }}" alt="">Nos Campagnes</h4>
                        <h1>Campagnes de Sensibilisation Environnementale</h1>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="filters">
                        <form id="filterForm" action="{{ route('front.campaigns.index') }}" method="GET">
                            <div class="search-bar">
                                <i class="bi bi-search"></i>
                                <input type="text" name="search" placeholder="Rechercher une campagne..." id="searchInput" value="{{ $search ?? '' }}">
                            </div>
                            <div class="category-filter">
                                <select name="category" id="categorySelect">
                                    <option value="all" {{ $category == 'all' ? 'selected' : '' }}>Toutes</option>
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
                            </div>
                            <div class="status-filter">
                                <select name="status" id="statusSelect">
                                    <option value="all" {{ $status == 'all' ? 'selected' : '' }}>Tous</option>
                                    <option value="upcoming" {{ $status == 'upcoming' ? 'selected' : '' }}>À venir</option>
                                    <option value="active" {{ $status == 'active' ? 'selected' : '' }}>En cours</option>
                                    <option value="ended" {{ $status == 'ended' ? 'selected' : '' }}>Terminé</option>
                                </select>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="campaigns-grid">
                        @forelse ($campaigns as $campaign)
                            <div class="single-campaign-box" data-category="{{ $campaign->category }}" onclick="goToCampaignDetail({{ $campaign->id }})">
                                <div class="campaign-thumb">
                                    <img src="{{ !empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($campaign->media_urls['images'][0]) ? \Illuminate\Support\Facades\Storage::url($campaign->media_urls['images'][0]) : asset('assets/images/home6/placeholder.jpg') }}" alt="{{ $campaign->title }}">
                                    <div class="campaign-status">{{ ucfirst($campaign->status) }}</div>
                                </div>
                                <div class="campaign-content">
                                    <div class="campaign-text">
                                        <span>{{ ucfirst($campaign->category) }}</span>
                                    </div>
                                    <h4>{{ \Illuminate\Support\Str::limit($campaign->title, 50) }}</h4>
                                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($campaign->content), 150) }}</p>
                                    <div class="campaign-dates">
                                        <span><i class="bi bi-calendar2-event"></i> {{ $campaign->start_date->format('d/m/Y') }}</span>
                                        <span><i class="bi bi-calendar2-check"></i> {{ $campaign->end_date->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="campaign-actions">
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
                                        <div class="action-buttons">
                                            <button class="action-btn {{ auth()->check() && auth()->user()->likedCampaigns()->where('campaign_id', $campaign->id)->exists() ? 'liked' : '' }}" onclick="event.stopPropagation(); toggleLike(this, {{ $campaign->id }})">
                                                <i class="bi bi-heart"></i>
                                            </button>
                                            <button class="action-btn" onclick="event.stopPropagation(); shareCampaign({{ $campaign->id }})">
                                                <i class="bi bi-share"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="echofy-button style-five">
                                        <a href="{{ url('/campaigns/' . $campaign->id) }}">En savoir plus<i class="bi bi-arrow-right-short"></i></a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-center">Aucune campagne trouvée.</p>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </section>
    <!--==================================================-->
    <!-- End Echofy Campaigns Area -->
    <!--==================================================-->
@endsection

@push('styles')
    <style>
        .campaigns-area.home-six {
            padding: 60px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .section-title.center h4 {
            font-size: 1.2rem;
            color: #28a745;
            margin-bottom: 1rem;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }

        .section-title.center h1 {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 2rem;
        }

        .filters {
            display: flex;
            flex-direction: row;
            align-items: center;
            margin-bottom: 2rem;
            gap: 1rem;
        }

        .search-bar {
            display: flex;
            align-items: center;
            background: white;
            border-radius: 25px;
            padding: 0.5rem 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            flex: 2;
            min-width: 200px;
        }

        .search-bar input {
            border: none;
            outline: none;
            flex: 1;
            padding: 0.5rem;
            font-size: 1rem;
        }

        .search-bar i {
            color: #28a745;
            margin-right: 0.5rem;
        }

        .category-filter, .status-filter {
            flex: 1;
            min-width: 150px;
        }

        .category-filter select, .status-filter select {
            width: 100%;
            padding: 0.5rem 1rem;
            border: 2px solid #28a745;
            background: white;
            color: #28a745;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 500;
            font-size: 1rem;
        }

        .category-filter select:focus, .status-filter select:focus {
            outline: none;
            border-color: #218838;
        }

        .category-filter select option, .status-filter select option {
            color: #333;
        }

        .campaigns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .single-campaign-box {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .single-campaign-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.15);
        }

        .campaign-thumb {
            width: 100%;
            height: 200px;
            position: relative;
            overflow: hidden;
        }

        .campaign-thumb img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .campaign-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            color: white;
            background: rgba(40, 167, 69, 0.9);
        }

        .campaign-content {
            padding: 1.5rem;
        }

        .campaign-text span {
            display: inline-block;
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }

        .campaign-content h4 {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .campaign-content p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 1rem;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .campaign-dates {
            display: flex;
            justify-content: space-between;
            color: #888;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }

        .campaign-dates i {
            margin-right: 0.3rem;
        }

        .campaign-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid #eee;
            margin-bottom: 1rem;
        }

        .campaign-stats {
            display: flex;
            gap: 1rem;
        }

        .stat-item {
            display: flex;
            align-items: center;
            gap: 0.3rem;
            color: #666;
            font-size: 0.9rem;
        }

        .stat-item i {
            color: #28a745;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            background: none;
            border: none;
            color: #666;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 0.5rem;
            border-radius: 8px;
        }

        .action-btn:hover {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .action-btn.liked {
            color: #dc3545;
        }

        .echofy-button.style-five a {
            display: inline-flex;
            align-items: center;
            padding: 0.5rem 1rem;
            background: #28a745;
            color: white;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .echofy-button.style-five a:hover {
            background: #20c997;
        }

        .echofy-button.style-five i {
            margin-left: 0.5rem;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
        }

        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            color: #28a745;
            border: 2px solid #28a745;
            background: white;
            transition: all 0.3s ease;
        }

        .pagination a:hover, .pagination span.current {
            background: #28a745;
            color: white;
        }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
            opacity: 0;
            transition: opacity 0.5s ease;
        }

        .alert.show {
            opacity: 1;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .section-title.center h1 {
                font-size: 2rem;
            }

            .campaigns-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .filters {
                flex-direction: row;
                flex-wrap: nowrap;
                gap: 0.5rem;
            }

            .search-bar {
                flex: 2;
                min-width: 100px;
            }

            .category-filter, .status-filter {
                flex: 1;
                min-width: 100px;
            }

            .category-filter select, .status-filter select {
                font-size: 0.9rem;
            }
        }
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
                grid.innerHTML = '<p class="text-center">Aucune campagne trouvée.</p>';
                return;
            }

            campaigns.forEach(campaign => {
                const isLiked = {{ auth()->check() ? '!!' . auth()->user()->likedCampaigns()->where('campaign_id', "' + campaign.id + '").exists() : 'false' }};
                const campaignBox = document.createElement('div');
                campaignBox.className = 'single-campaign-box';
                campaignBox.setAttribute('data-category', campaign.category);
                campaignBox.onclick = () => goToCampaignDetail(campaign.id);
                campaignBox.innerHTML = `
                    <div class="campaign-thumb">
                        <img src="${campaign.thumbnail}" alt="${campaign.title}">
                        <div class="campaign-status">${campaign.status.charAt(0).toUpperCase() + campaign.status.slice(1)}</div>
                    </div>
                    <div class="campaign-content">
                        <div class="campaign-text">
                            <span>${campaign.category.charAt(0).toUpperCase() + campaign.category.slice(1)}</span>
                        </div>
                        <h4>${campaign.title.substring(0, 50)}${campaign.title.length > 50 ? '...' : ''}</h4>
                        <p>${campaign.content.substring(0, 150)}${campaign.content.length > 150 ? '...' : ''}</p>
                        <div class="campaign-dates">
                            <span><i class="bi bi-calendar2-event"></i> ${campaign.start_date}</span>
                            <span><i class="bi bi-calendar2-check"></i> ${campaign.end_date}</span>
                        </div>
                        <div class="campaign-actions">
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
                            <div class="action-buttons">
                                <button class="action-btn ${isLiked ? 'liked' : ''}" onclick="event.stopPropagation(); toggleLike(this, ${campaign.id})">
                                    <i class="bi bi-heart"></i>
                                </button>
                                <button class="action-btn" onclick="event.stopPropagation(); shareCampaign(${campaign.id})">
                                    <i class="bi bi-share"></i>
                                </button>
                            </div>
                        </div>
                        <div class="echofy-button style-five">
                            <a href="/campaigns/${campaign.id}">En savoir plus<i class="bi bi-arrow-right-short"></i></a>
                        </div>
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
                            heartIcon.style.color = '#dc3545';
                        } else {
                            heartIcon.style.color = '#666';
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
