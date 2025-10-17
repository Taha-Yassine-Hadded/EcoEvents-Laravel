@extends('layouts.admin')

@section('title', 'Administration des Campagnes - Echofy')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Gestion des Campagnes</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Campagnes</li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <!-- Statistiques du dashboard -->
            <div class="row mb-4">
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-primary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                        Total Campagnes
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCampaigns">{{ $campaigns->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-success shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                        Campagnes Actives
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="activeCampaigns">{{ $campaigns->where('status', 'active')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-play-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-warning shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                        Campagnes Terminées
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="endedCampaigns">{{ $campaigns->where('status', 'ended')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-info shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                        Campagnes à Venir
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="upcomingCampaigns">{{ $campaigns->where('status', 'upcoming')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-clock fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contrôles et filtres -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Filtres et Actions</h6>
                </div>
                <div class="card-body">
                    <div class="controls-row">
                        <div class="search-filters">
                            <div class="search-box">
                                <i class="fas fa-search"></i>
                                <input type="text" placeholder="Rechercher une campagne..." id="searchInput">
                            </div>
                            <select class="filter-select" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="active">Active</option>
                                <option value="ended">Terminée</option>
                                <option value="upcoming">À Venir</option>
                            </select>
                            <select class="filter-select" id="categoryFilter">
                                <option value="">Toutes catégories</option>
                                <option value="recyclage">Recyclage</option>
                                <option value="climat">Climat</option>
                                <option value="biodiversite">Biodiversité</option>
                                <option value="eau">Ressources en eau</option>
                                <option value="energie">Énergie renouvelable</option>
                                <option value="transport">Transport durable</option>
                                <option value="alimentation">Alimentation durable</option>
                                <option value="pollution">Lutte contre la pollution</option>
                                <option value="sensibilisation">Sensibilisation générale</option>
                            </select>
                        </div>
                        <div class="bulk-actions">
                            <a href="{{ route('admin.campaigns.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Nouvelle Campagne
                            </a>
                            <button class="btn btn-danger" id="bulkDeleteBtn" onclick="bulkDelete()" disabled>
                                <i class="fas fa-trash"></i>
                                Supprimer sélection
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des campagnes -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Liste des Campagnes</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>Campagne</th>
                                <th>Catégorie</th>
                                <th>Statut</th>
                                <th>Dates</th>
                                <th>Engagement</th>
                                <th>Créateur</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="campaignsTableBody">
                            @forelse ($campaigns as $campaign)
                                <tr class="campaign-row" data-id="{{ $campaign->id }}">
                                    <td>
                                        <input type="checkbox" class="checkbox campaign-checkbox" value="{{ $campaign->id }}" onchange="updateBulkActions()">
                                    </td>
                                    <td>
                                        <div class="campaign-info">
                                            <div class="campaign-thumbnail">
                                                @if (!empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']) && Storage::disk('public')->exists($campaign->media_urls['images'][0]))
                                                    <img src="{{ Storage::url($campaign->media_urls['images'][0]) }}" alt="{{ $campaign->title }}">
                                                @else
                                                    <img src="https://via.placeholder.com/60x60?text=Image" alt="Placeholder">
                                                @endif
                                            </div>
                                            <div class="campaign-details">
                                                <h4>{{ Str::limit($campaign->title, 50) }}</h4>
                                                <p>{{ Str::limit(strip_tags($campaign->content), 100) }}</p>
                                                <div class="campaign-meta">Créée le {{ $campaign->created_at->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-badge">{{ ucfirst($campaign->category) }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $campaign->status }}">{{ ucfirst($campaign->status == 'upcoming' ? 'À venir' : ($campaign->status == 'active' ? 'Active' : 'Terminée')) }}</span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem;">
                                            <div><strong>Début:</strong> {{ $campaign->start_date->format('d/m/Y') }}</div>
                                            <div><strong>Fin:</strong> {{ $campaign->end_date->format('d/m/Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stats-mini">
                                            <div class="stat-mini">
                                                <i class="fas fa-eye"></i>
                                                <span>{{ $campaign->views_count }}</span>
                                            </div>
                                            <div class="stat-mini">
                                                <i class="fas fa-heart"></i>
                                                <span>{{ $campaign->likes_count ?? 0 }}</span>
                                            </div>
                                            <div class="stat-mini">
                                                <i class="fas fa-comment"></i>
                                                <span>{{ $campaign->comments_count ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem;">
                                            <div><strong>{{ $campaign->creator ? $campaign->creator->name : 'Inconnu' }}</strong></div>
                                            <div style="color: #666;">{{ $campaign->creator ? $campaign->creator->email : 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-view" onclick="viewCampaign({{ $campaign->id }})" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn btn-edit" onclick="editCampaign({{ $campaign->id }})" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn btn-delete" onclick="confirmDelete({{ $campaign->id }})" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="empty-state">
                                        <i class="fas fa-folder-open"></i>
                                        <p>Aucune campagne trouvée.</p>
                                    </td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination">
                {{ $campaigns->links() }}
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
                <button class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="fas fa-trash"></i>
                    Supprimer
                </button>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            .controls-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                flex-wrap: wrap;
                gap: 1rem;
            }

            .search-filters {
                display: flex;
                align-items: center;
                gap: 1rem;
                flex-wrap: wrap;
            }

            .search-box {
                display: flex;
                align-items: center;
                background: #f8f9fa;
                border-radius: 25px;
                padding: 0.5rem 1rem;
                min-width: 300px;
            }

            .search-box input {
                border: none;
                background: transparent;
                outline: none;
                flex: 1;
                padding: 0.5rem;
                font-size: 1rem;
            }

            .search-box i {
                color: #28a745;
                margin-right: 0.5rem;
            }

            .filter-select {
                padding: 0.6rem 1rem;
                border: 2px solid #e9ecef;
                border-radius: 8px;
                background: white;
                cursor: pointer;
                font-size: 0.95rem;
            }

            .filter-select:focus {
                outline: none;
                border-color: #28a745;
            }

            .bulk-actions {
                display: flex;
                align-items: center;
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

            .btn-primary {
                background: linear-gradient(135deg, #28a745, #20c997);
                color: white;
                box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
            }

            .btn-primary:hover {
                transform: translateY(-1px);
                box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
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

            .campaign-info {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .campaign-thumbnail {
                width: 60px;
                height: 60px;
                border-radius: 8px;
                background: linear-gradient(135deg, #28a745, #20c997);
                flex-shrink: 0;
                overflow: hidden;
            }

            .campaign-thumbnail img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .campaign-details h4 {
                color: #333;
                margin-bottom: 0.3rem;
                font-size: 1rem;
            }

            .campaign-details p {
                color: #666;
                font-size: 0.9rem;
                line-height: 1.4;
                margin: 0;
            }

            .campaign-meta {
                font-size: 0.85rem;
                color: #888;
            }

            .status-badge {
                display: inline-block;
                padding: 0.3rem 0.8rem;
                border-radius: 15px;
                font-size: 0.8rem;
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
                display: inline-block;
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
                padding: 0.25rem 0.6rem;
                border-radius: 12px;
                font-size: 0.8rem;
                font-weight: 500;
            }

            .stats-mini {
                display: flex;
                gap: 1rem;
                font-size: 0.9rem;
            }

            .stat-mini {
                display: flex;
                align-items: center;
                gap: 0.3rem;
                color: #666;
            }

            .stat-mini i {
                color: #28a745;
            }

            .action-buttons {
                display: flex;
                gap: 0.5rem;
                align-items: center;
            }

            .action-btn {
                padding: 0.5rem;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: all 0.3s ease;
                font-size: 0.9rem;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .btn-view {
                background: rgba(0, 123, 255, 0.1);
                color: #007bff;
            }

            .btn-view:hover {
                background: #007bff;
                color: white;
            }

            .btn-edit {
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }

            .btn-edit:hover {
                background: #28a745;
                color: white;
            }

            .btn-delete {
                background: rgba(220, 53, 69, 0.1);
                color: #dc3545;
            }

            .btn-delete:hover {
                background: #dc3545;
                color: white;
            }

            .checkbox {
                width: 18px;
                height: 18px;
                cursor: pointer;
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

            .empty-state {
                text-align: center;
                padding: 3rem;
                color: #666;
            }

            .empty-state i {
                font-size: 4rem;
                color: #ddd;
                margin-bottom: 1rem;
            }

            @media (max-width: 1200px) {
                .search-box {
                    min-width: 250px;
                }

                .controls-row {
                    flex-direction: column;
                    align-items: stretch;
                }
            }

            @media (max-width: 768px) {
                .search-filters {
                    flex-direction: column;
                }

                .search-box {
                    min-width: auto;
                    width: 100%;
                }

                .table-responsive {
                    overflow-x: auto;
                }

                table {
                    min-width: 800px;
                }

                .campaign-info {
                    min-width: 250px;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            let selectedCampaigns = [];
            let campaignToDelete = null;

            console.log('Script de All.blade.php chargé à', new Date().toLocaleString());

            let campaigns = [];
            try {
                campaigns = @json($campaignsForJs);
                console.log('Données des campagnes:', campaigns);
            } catch (e) {
                console.error('Erreur lors du parsing des campagnes:', e.message);
                campaigns = [];
            }

            function toggleSelectAll() {
                console.log('Sélection de toutes les campagnes');
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.campaign-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });

                updateBulkActions();
            }

            function updateBulkActions() {
                console.log('Mise à jour des actions en masse');
                const checkboxes = document.querySelectorAll('.campaign-checkbox:checked');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                selectedCampaigns = Array.from(checkboxes).map(cb => cb.value);
                bulkDeleteBtn.disabled = selectedCampaigns.length === 0;

                const selectAll = document.getElementById('selectAll');
                const allCheckboxes = document.querySelectorAll('.campaign-checkbox');
                selectAll.checked = selectedCampaigns.length === allCheckboxes.length;
                selectAll.indeterminate = selectedCampaigns.length > 0 && selectedCampaigns.length < allCheckboxes.length;
            }

            function viewCampaign(id) {
                console.log('Affichage de la campagne ID:', id);
                window.location.href = `/admin/campaigns/${id}`;
            }

            function editCampaign(id) {
                console.log('Édition de la campagne ID:', id);
                window.location.href = `/admin/campaigns/${id}`;
            }

            function confirmDelete(id) {
                console.log('Confirmation de suppression pour la campagne ID:', id);
                campaignToDelete = id;
                document.getElementById('deleteModalText').textContent =
                    'Êtes-vous sûr de vouloir supprimer cette campagne ? Cette action est irréversible.';
                document.getElementById('deleteModal').classList.add('active');

                document.getElementById('confirmDeleteBtn').onclick = () => deleteCampaign(id);
            }

            function deleteCampaign(id) {
                console.log('Suppression de la campagne ID:', id);
                const token = localStorage.getItem('jwt_token');
                if (!token) {
                    console.error('Token JWT manquant');
                    alert('Vous devez être connecté pour effectuer cette action.');
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                fetch(`/admin/campaigns/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        console.log('Statut de la réponse:', response.status);
                        if (!response.ok) {
                            throw new Error(`Erreur HTTP: ${response.status} ${response.statusText}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Réponse de suppression:', data);
                        if (data.success) {
                            const row = document.querySelector(`tr[data-id="${id}"]`);
                            if (row) {
                                row.style.opacity = '0.5';
                                setTimeout(() => {
                                    row.remove();
                                    closeDeleteModal();
                                    updateStats();
                                    alert('Campagne supprimée avec succès');
                                    campaigns = campaigns.filter(c => c.id != id);
                                    filterCampaigns();
                                }, 500);
                            }
                        } else {
                            console.error('Erreur de suppression:', data.error);
                            alert(data.error || 'Erreur lors de la suppression de la campagne');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau lors de la suppression:', error.message);
                        alert('Une erreur est survenue lors de la suppression: ' + error.message);
                    });
            }

            function bulkDelete() {
                if (selectedCampaigns.length === 0) return;

                console.log('Suppression en masse des campagnes:', selectedCampaigns);
                const count = selectedCampaigns.length;
                document.getElementById('deleteModalText').textContent =
                    `Êtes-vous sûr de vouloir supprimer ${count} campagne${count > 1 ? 's' : ''} ? Cette action est irréversible.`;
                document.getElementById('deleteModal').classList.add('active');

                document.getElementById('confirmDeleteBtn').onclick = () => {
                    const token = localStorage.getItem('jwt_token');
                    if (!token) {
                        console.error('Token JWT manquant pour suppression en masse');
                        alert('Vous devez être connecté pour effectuer cette action.');
                        window.location.href = '{{ route("login") }}';
                        return;
                    }

                    Promise.all(selectedCampaigns.map(id =>
                        fetch(`/admin/campaigns/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'Authorization': `Bearer ${token}`,
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        }).then(response => {
                            if (!response.ok) {
                                throw new Error(`Erreur HTTP pour ID ${id}: ${response.status}`);
                            }
                            return response.json().then(data => ({ id, data }));
                        })
                    ))
                        .then(results => {
                            console.log('Résultats de la suppression en masse:', results);
                            let success = true;
                            results.forEach(result => {
                                if (result.data.success) {
                                    const row = document.querySelector(`tr[data-id="${result.id}"]`);
                                    if (row) row.remove();
                                    campaigns = campaigns.filter(c => c.id != result.id);
                                } else {
                                    success = false;
                                    console.error('Erreur lors de la suppression de la campagne ID:', result.id, result.data.error);
                                }
                            });
                            closeDeleteModal();
                            updateStats();
                            filterCampaigns();
                            alert(success ? 'Campagnes supprimées avec succès' : 'Erreur lors de la suppression de certaines campagnes');
                            selectedCampaigns = [];
                            updateBulkActions();
                        })
                        .catch(error => {
                            console.error('Erreur réseau lors de la suppression en masse:', error.message);
                            alert('Une erreur est survenue lors de la suppression: ' + error.message);
                        });
                };
            }

            function closeDeleteModal() {
                console.log('Fermeture de la modale de suppression');
                document.getElementById('deleteModal').classList.remove('active');
                campaignToDelete = null;
            }

            function updateStats() {
                console.log('Mise à jour des statistiques');
                const visibleRows = document.querySelectorAll('#campaignsTableBody tr:not(.empty-state)');
                const activeCount = document.querySelectorAll('.status-active').length;
                const endedCount = document.querySelectorAll('.status-ended').length;
                const upcomingCount = document.querySelectorAll('.status-upcoming').length;

                document.getElementById('totalCampaigns').textContent = visibleRows.length;
                document.getElementById('activeCampaigns').textContent = activeCount;
                document.getElementById('endedCampaigns').textContent = endedCount;
                document.getElementById('upcomingCampaigns').textContent = upcomingCount;
            }

            function filterCampaigns() {
                console.log('Filtrage des campagnes');
                const searchInput = document.getElementById('searchInput').value.toLowerCase();
                const statusFilter = document.getElementById('statusFilter').value;
                const categoryFilter = document.getElementById('categoryFilter').value;
                const tbody = document.getElementById('campaignsTableBody');

                console.log('Critères de filtrage:', {
                    search: searchInput,
                    status: statusFilter,
                    category: categoryFilter
                });

                tbody.innerHTML = '';

                const filteredCampaigns = campaigns.filter(campaign => {
                    const matchesSearch = (campaign.title || '').toLowerCase().includes(searchInput) ||
                        (campaign.content || '').toLowerCase().includes(searchInput);
                    const matchesStatus = !statusFilter || campaign.status === statusFilter;
                    const matchesCategory = !categoryFilter || campaign.category === categoryFilter;

                    return matchesSearch && matchesStatus && matchesCategory;
                });

                console.log('Campagnes filtrées:', filteredCampaigns);

                if (filteredCampaigns.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <p>Aucune campagne trouvée.</p>
                            </td>
                        </tr>
                    `;
                } else {
                    filteredCampaigns.forEach(campaign => {
                        const statusText = campaign.status === 'upcoming' ? 'À venir' : (campaign.status === 'active' ? 'Active' : 'Terminée');
                        const thumbnail = campaign.thumbnail || 'https://via.placeholder.com/60x60?text=Image';
                        const creatorName = campaign.creator ? campaign.creator.name : 'Inconnu';
                        const creatorEmail = campaign.creator ? campaign.creator.email : 'N/A';
                        const createdAt = new Date(campaign.created_at).toLocaleDateString('fr-FR');
                        const startDate = new Date(campaign.start_date).toLocaleDateString('fr-FR');
                        const endDate = new Date(campaign.end_date).toLocaleDateString('fr-FR');

                        const row = document.createElement('tr');
                        row.className = 'campaign-row';
                        row.setAttribute('data-id', campaign.id);
                        row.innerHTML = `
                            <td>
                                <input type="checkbox" class="checkbox campaign-checkbox" value="${campaign.id}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="campaign-info">
                                    <div class="campaign-thumbnail">
                                        <img src="${thumbnail}" alt="${campaign.title}">
                                    </div>
                                    <div class="campaign-details">
                                        <h4>${campaign.title.substring(0, 50)}${campaign.title.length > 50 ? '...' : ''}</h4>
                                        <p>${campaign.content.substring(0, 100)}${campaign.content.length > 100 ? '...' : ''}</p>
                                        <div class="campaign-meta">Créée le ${createdAt}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge">${campaign.category.charAt(0).toUpperCase() + campaign.category.slice(1)}</span>
                            </td>
                            <td>
                                <span class="status-badge status-${campaign.status}">${statusText}</span>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    <div><strong>Début:</strong> ${startDate}</div>
                                    <div><strong>Fin:</strong> ${endDate}</div>
                                </div>
                            </td>
                            <td>
                                <div class="stats-mini">
                                    <div class="stat-mini">
                                        <i class="fas fa-eye"></i>
                                        <span>${campaign.views_count}</span>
                                    </div>
                                    <div class="stat-mini">
                                        <i class="fas fa-heart"></i>
                                        <span>${campaign.likes_count || 0}</span>
                                    </div>
                                    <div class="stat-mini">
                                        <i class="fas fa-comment"></i>
                                        <span>${campaign.comments_count || 0}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    <div><strong>${creatorName}</strong></div>
                                    <div style="color: #666;">${creatorEmail}</div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-view" onclick="viewCampaign(${campaign.id})" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn btn-edit" onclick="editCampaign(${campaign.id})" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn btn-delete" onclick="confirmDelete(${campaign.id})" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        `;
                        tbody.appendChild(row);
                    });
                }

                updateStats();
            }

            function setupFilters() {
                console.log('Initialisation des filtres');
                const searchInput = document.getElementById('searchInput');
                const statusFilter = document.getElementById('statusFilter');
                const categoryFilter = document.getElementById('categoryFilter');

                searchInput.addEventListener('input', filterCampaigns);
                statusFilter.addEventListener('change', filterCampaigns);
                categoryFilter.addEventListener('change', filterCampaigns);
            }

            document.addEventListener('DOMContentLoaded', function() {
                console.log('Document chargé, initialisation des filtres');
                setupFilters();
                updateStats();
            });
        </script>
    @endpush
@endsection
