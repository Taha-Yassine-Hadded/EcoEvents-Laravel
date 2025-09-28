@extends('layouts.admin')

@section('title', 'Administration des Événements - Echofy')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Gestion des Événements</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Événements</li>
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
                                        Total Événements
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalEvents">{{ $events->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                                        Événements en Cours
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="ongoingEvents">{{ $events->where('status', 'ongoing')->count() }}</div>
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
                                        Événements Terminés
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="completedEvents">{{ $events->where('status', 'completed')->count() }}</div>
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
                                        Événements Annulés
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="cancelledEvents">{{ $events->where('status', 'cancelled')->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-ban fa-2x text-gray-300"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6 mb-4">
                    <div class="card border-left-secondary shadow h-100 py-2">
                        <div class="card-body">
                            <div class="row no-gutters align-items-center">
                                <div class="col mr-2">
                                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                        Événements à Venir
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="upcomingEvents">{{ $events->where('status', 'upcoming')->count() }}</div>
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
                                <input type="text" placeholder="Rechercher un événement..." id="searchInput">
                            </div>
                            <select class="filter-select" id="statusFilter">
                                <option value="">Tous les statuts</option>
                                <option value="ongoing">En cours</option>
        <option value="completed">Terminé</option>
        <option value="cancelled">Annulé</option>
        <option value="upcoming">À venir</option>
                            </select>
                            <select class="filter-select" id="categoryFilter">
                                <option value="">Toutes catégories</option>
                                @foreach (\App\Models\Category::all() as $category)
                                    <option value="{{ $category->name }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="bulk-actions">
                            <a href="{{ route('admin.events.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Nouvel Événement
                            </a>
                            <button class="btn btn-danger" id="bulkDeleteBtn" onclick="bulkDelete()" disabled>
                                <i class="fas fa-trash"></i>
                                Supprimer sélection
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des événements -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Liste des Événements</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>Événement</th>
                                <th>Catégorie</th>
                                <th>Statut</th>
                                <th>Date</th>
                                <th>Lieu</th>
                                <th>Capacité</th>
                                <th>Inscriptions</th>
                                <th>Organisateur</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="eventsTableBody">
                            @forelse ($events as $event)
                                <tr class="event-row" data-id="{{ $event->id }}">
                                    <td>
                                        <input type="checkbox" class="checkbox event-checkbox" value="{{ $event->id }}" onchange="updateBulkActions()">
                                    </td>
                                    <td>
                                        <div class="event-info">
                                            <div class="event-thumbnail">
                                                <img src="{{ $event->img ? asset('storage/' . $event->img) : asset('storage/events/default-event.jpg') }}" alt="{{ $event->title }}">
                                            </div>
                                            <div class="event-details">
                                                <h4>{{ Str::limit($event->title, 50) }}</h4>
                                                <p>{{ Str::limit(strip_tags($event->description ?? ''), 100) }}</p>
                                                <div class="event-meta">Créé le {{ $event->created_at->format('d/m/Y') }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="category-badge">{{ ucfirst($event->category->name ?? 'N/A') }}</span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-{{ $event->status }}">{{ ucfirst($event->status == 'upcoming' ? 'À venir' : ($event->status == 'ongoing' ? 'En cours' : ($event->status == 'completed' ? 'Terminé' : 'Annulé'))) }}</span>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem;">
                                            <div>{{ $event->date->format('d/m/Y') }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem;">
                                            {{ Str::limit($event->location, 50) }}
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem;">
                                            {{ $event->capacity ?? 'N/A' }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="stats-mini">
                                            <div class="stat-mini">
                                                <i class="fas fa-users"></i>
                                                <span>{{ $event->registrations->count() }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div style="font-size: 0.9rem;">
                                            <div><strong>{{ $event->organizer ? $event->organizer->name : 'Inconnu' }}</strong></div>
                                            <div style="color: #666;">{{ $event->organizer ? $event->organizer->email : 'N/A' }}</div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-view" onclick="viewEvent({{ $event->id }})" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="action-btn btn-edit" onclick="editEvent({{ $event->id }})" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn btn-delete" onclick="confirmDelete({{ $event->id }})" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="empty-state">
                                        <i class="fas fa-folder-open"></i>
                                        <p>Aucun événement trouvé.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="pagination justify-content-center">
                {{ $events->links('vendor.pagination.custom') }}
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
                Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.
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

            .event-info {
                display: flex;
                align-items: center;
                gap: 1rem;
            }

            .event-thumbnail {
                width: 60px;
                height: 60px;
                border-radius: 8px;
                background: linear-gradient(135deg, #28a745, #20c997);
                flex-shrink: 0;
                overflow: hidden;
            }

            .event-thumbnail img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            .event-details h4 {
                color: #333;
                margin-bottom: 0.3rem;
                font-size: 1rem;
            }

            .event-details p {
                color: #666;
                font-size: 0.9rem;
                line-height: 1.4;
                margin: 0;
            }

            .event-meta {
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

            .status-ongoing {
                background: rgba(40, 167, 69, 0.1);
                color: #28a745;
            }

            .status-completed {
                background: rgba(108, 117, 125, 0.1);
                color: #6c757d;
            }

            .status-cancelled {
                background: rgba(220, 53, 69, 0.1);
                color: #dc3545;
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

                .event-info {
                    min-width: 250px;
                }
            }

            .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem 0;
            list-style: none;
        }

        .pagination .page-item {
            margin: 0 0.25rem;
        }

        .pagination .page-link {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0.5rem 1rem;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            color: #333;
            background: white;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: all 0.3s ease;
            min-width: 40px;
        }

        .pagination .page-link:hover,
        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            border-color: #28a745;
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(40, 167, 69, 0.3);
        }

        .pagination .page-item.disabled .page-link {
            color: #6c757d;
            background: #f8f9fa;
            cursor: not-allowed;
            opacity: 0.65;
        }

        .pagination .page-link i {
            font-size: 1rem;
            margin: 0;
        }

        @media (max-width: 768px) {
            .pagination .page-link {
                padding: 0.4rem 0.8rem;
                font-size: 0.9rem;
                min-width: 35px;
            }

            .pagination {
                gap: 0.3rem;
            }
        }
        </style>
    @endpush

    @push('scripts')
        <script>
            let selectedEvents = [];
            let eventToDelete = null;

            console.log('Script de events/index.blade.php chargé à', new Date().toLocaleString());

            let events = [];
            try {
                events = @json($eventsForJs);
                console.log('Données des événements:', events);
            } catch (e) {
                console.error('Erreur lors du parsing des événements:', e.message);
                events = [];
            }

            function toggleSelectAll() {
                console.log('Sélection de tous les événements');
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.event-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });

                updateBulkActions();
            }

            function updateBulkActions() {
                console.log('Mise à jour des actions en masse');
                const checkboxes = document.querySelectorAll('.event-checkbox:checked');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                selectedEvents = Array.from(checkboxes).map(cb => cb.value);
                bulkDeleteBtn.disabled = selectedEvents.length === 0;

                const selectAll = document.getElementById('selectAll');
                const allCheckboxes = document.querySelectorAll('.event-checkbox');
                selectAll.checked = selectedEvents.length === allCheckboxes.length;
                selectAll.indeterminate = selectedEvents.length > 0 && selectedEvents.length < allCheckboxes.length;
            }

            function viewEvent(id) {
                console.log('Affichage de l\'événement ID:', id);
                window.location.href = `/admin/events/${id}/details`;
            }

            function editEvent(id) {
                console.log('Édition de l\'événement ID:', id);
                window.location.href = `/admin/events/${id}/edit`;
            }

            function confirmDelete(id) {
                console.log('Confirmation de suppression pour l\'événement ID:', id);
                eventToDelete = id;
                document.getElementById('deleteModalText').textContent =
                    'Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.';
                document.getElementById('deleteModal').classList.add('active');

                document.getElementById('confirmDeleteBtn').onclick = () => deleteEvent(id);
            }

            function deleteEvent(id) {
                console.log('Suppression de l\'événement ID:', id);
                const token = localStorage.getItem('jwt_token');
                if (!token) {
                    console.error('Token JWT manquant');
                    alert('Vous devez être connecté pour effectuer cette action.');
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                fetch(`/admin/events/${id}`, {
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
                                    alert('Événement supprimé avec succès');
                                    events = events.filter(e => e.id != id);
                                    filterEvents();
                                }, 500);
                            }
                        } else {
                            console.error('Erreur de suppression:', data.error);
                            closeDeleteModal();
                            updateStats();
                            events = events.filter(e => e.id != id);
                            filterEvents();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau lors de la suppression:', error.message);
                        closeDeleteModal();
                        updateStats();
                        events = events.filter(e => e.id != id);
                        filterEvents();
                    });
            }

            function bulkDelete() {
                if (selectedEvents.length === 0) return;

                console.log('Suppression en masse des événements:', selectedEvents);
                const count = selectedEvents.length;
                document.getElementById('deleteModalText').textContent =
                    `Êtes-vous sûr de vouloir supprimer ${count} événement${count > 1 ? 's' : ''} ? Cette action est irréversible.`;
                document.getElementById('deleteModal').classList.add('active');

                document.getElementById('confirmDeleteBtn').onclick = () => {
                    const token = localStorage.getItem('jwt_token');
                    if (!token) {
                        console.error('Token JWT manquant pour suppression en masse');
                        alert('Vous devez être connecté pour effectuer cette action.');
                        window.location.href = '{{ route("login") }}';
                        return;
                    }

                    Promise.all(selectedEvents.map(id =>
                        fetch(`/admin/events/${id}`, {
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
                            let success = true;
                            results.forEach(result => {
                                if (result.data.success) {
                                    const row = document.querySelector(`tr[data-id="${result.id}"]`);
                                    if (row) row.remove();
                                    events = events.filter(e => e.id != result.id);
                                } else {
                                    success = false;
                                    console.error('Erreur lors de la suppression de l\'événement ID:', result.id, result.data.error);
                                }
                            });
                            closeDeleteModal();
                            updateStats();
                            filterEvents();
                            selectedEvents = [];
                            updateBulkActions();
                        })
                        .catch(error => {
                            console.error('Erreur réseau lors de la suppression en masse:', error.message);
                        });
                };
            }

            function closeDeleteModal() {
                console.log('Fermeture de la modale de suppression');
                document.getElementById('deleteModal').classList.remove('active');
                eventToDelete = null;
            }

            function updateStats() {
                console.log('Mise à jour des statistiques');
                const visibleRows = document.querySelectorAll('#eventsTableBody tr:not(.empty-state)');
                const ongoingCount = document.querySelectorAll('.status-ongoing').length;
                const completedCount = document.querySelectorAll('.status-completed').length;
                const cancelledCount = document.querySelectorAll('.status-cancelled').length;
                const upcomingCount = document.querySelectorAll('.status-upcoming').length;

                document.getElementById('totalEvents').textContent = visibleRows.length;
                document.getElementById('ongoingEvents').textContent = ongoingCount;
                document.getElementById('completedEvents').textContent = completedCount;
                document.getElementById('cancelledEvents').textContent = cancelledCount;
                document.getElementById('upcomingEvents').textContent = upcomingCount;
            }

            function filterEvents() {
                console.log('Filtrage des événements');
                const searchInput = document.getElementById('searchInput').value.toLowerCase();
                const statusFilter = document.getElementById('statusFilter').value;
                const categoryFilter = document.getElementById('categoryFilter').value;
                const tbody = document.getElementById('eventsTableBody');

                console.log('Critères de filtrage:', {
                    search: searchInput,
                    status: statusFilter,
                    category: categoryFilter
                });

                tbody.innerHTML = '';

                const filteredEvents = events.filter(event => {
                    const matchesSearch = (event.title || '').toLowerCase().includes(searchInput) ||
                        (event.content || '').toLowerCase().includes(searchInput);
                    const matchesStatus = !statusFilter || event.status === statusFilter;
                    const matchesCategory = !categoryFilter || event.category === categoryFilter;

                    return matchesSearch && matchesStatus && matchesCategory;
                });

                console.log('Événements filtrés:', filteredEvents);

                if (filteredEvents.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="10" class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <p>Aucun événement trouvé.</p>
                            </td>
                        </tr>
                    `;
                } else {
                    filteredEvents.forEach(event => {
                        const statusText = event.status === 'upcoming' ? 'À venir' : (event.status === 'ongoing' ? 'En cours' : (event.status === 'completed' ? 'Terminé' : 'Annulé'));
                        const thumbnail = event.img ? `{{ asset('storage/') }}/${event.img}` : `{{ asset('storage/events/default-event.jpg') }}`;
                        const organizerName = event.organizer ? event.organizer.name : 'Inconnu';
                        const organizerEmail = event.organizer ? event.organizer.email : 'N/A';
                        const createdAt = event.created_at ? new Date(event.created_at).toLocaleDateString('fr-FR') : 'N/A';
                        const eventDate = event.date ? new Date(event.date).toLocaleDateString('fr-FR') : 'N/A';
                        const capacity = event.capacity || 'N/A';
                        const registrationsCount = event.registrations_count || 0;

                        const row = document.createElement('tr');
                        row.className = 'event-row';
                        row.setAttribute('data-id', event.id);
                        row.innerHTML = `
                            <td>
                                <input type="checkbox" class="checkbox event-checkbox" value="${event.id}" onchange="updateBulkActions()">
                            </td>
                            <td>
                                <div class="event-info">
                                    <div class="event-thumbnail">
                                        <img src="${thumbnail}" alt="${event.title}">
                                    </div>
                                    <div class="event-details">
                                        <h4>${event.title.substring(0, 50)}${event.title.length > 50 ? '...' : ''}</h4>
                                        <p>${(event.content || '').substring(0, 100)}${event.content && event.content.length > 100 ? '...' : ''}</p>
                                        <div class="event-meta">Créé le ${createdAt}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="category-badge">${event.category.charAt(0).toUpperCase() + event.category.slice(1)}</span>
                            </td>
                            <td>
                                <span class="status-badge status-${event.status}">${statusText}</span>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    <div><strong>Date:</strong> ${eventDate}</div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    ${(event.location || '').substring(0, 50)}${event.location && event.location.length > 50 ? '...' : ''}
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    ${capacity}
                                </div>
                            </td>
                            <td>
                                <div class="stats-mini">
                                    <div class="stat-mini">
                                        <i class="fas fa-users"></i>
                                        <span>${registrationsCount}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-size: 0.9rem;">
                                    <div><strong>${organizerName}</strong></div>
                                    <div style="color: #666;">${organizerEmail}</div>
                                </div>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-view" onclick="viewEvent(${event.id})" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="action-btn btn-edit" onclick="editEvent(${event.id})" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="action-btn btn-delete" onclick="confirmDelete(${event.id})" title="Supprimer">
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

                searchInput.addEventListener('input', filterEvents);
                statusFilter.addEventListener('change', filterEvents);
                categoryFilter.addEventListener('change', filterEvents);
            }

            document.addEventListener('DOMContentLoaded', function() {
                console.log('Document chargé, initialisation des filtres');
                setupFilters();
                updateStats();
            });
        </script>
    @endpush
@endsection