@extends('layouts.admin')

@section('title', 'Administration des Catégories - Echofy')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Gestion des Catégories</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Catégories</li>
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
                                        Total Catégories
                                    </div>
                                    <div class="h5 mb-0 font-weight-bold text-gray-800" id="totalCategories">{{ $categories->count() }}</div>
                                </div>
                                <div class="col-auto">
                                    <i class="fas fa-list fa-2x text-gray-300"></i>
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
                                <input type="text" placeholder="Rechercher une catégorie..." id="searchInput">
                            </div>
                        </div>
                        <div class="bulk-actions">
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i>
                                Nouvelle Catégorie
                            </a>
                            <button class="btn btn-danger" id="bulkDeleteBtn" onclick="bulkDelete()" disabled>
                                <i class="fas fa-trash"></i>
                                Supprimer sélection
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tableau des catégories -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Liste des Catégories</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                            <tr>
                                <th>
                                    <input type="checkbox" class="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                </th>
                                <th>Nom</th>
                                <th>Description</th>
                                <th>Événements Associés</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody id="categoriesTableBody">
                            @forelse ($categories as $category)
                                <tr class="category-row" data-id="{{ $category->id }}">
                                    <td>
                                        <input type="checkbox" class="checkbox category-checkbox" value="{{ $category->id }}" onchange="updateBulkActions()">
                                    </td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ Str::limit($category->description ?? 'Aucune description', 100) }}</td>
                                    <td>{{ $category->events->count() }}</td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="action-btn btn-edit" onclick="editCategory({{ $category->id }})" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="action-btn btn-delete" onclick="confirmDelete({{ $category->id }})" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-state">
                                        <i class="fas fa-folder-open"></i>
                                        <p>Aucune catégorie trouvée.</p>
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
                {{ $categories->links('vendor.pagination.custom') }}
            </div>

            <!-- Modal de suppression -->
            <div class="delete-modal" id="deleteModal">
                <div class="modal-content">
                    <div class="modal-icon">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h3 class="modal-title">Confirmer la suppression</h3>
                    <p class="modal-text" id="deleteModalText">
                        Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.
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

            @media (max-width: 768px) {
                .search-box {
                    min-width: auto;
                    width: 100%;
                }

                .table-responsive {
                    overflow-x: auto;
                }

                table {
                    min-width: 600px;
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
            let selectedCategories = [];
            let categoryToDelete = null;

            console.log('Script de categories/index.blade.php chargé à', new Date().toLocaleString());

            let categories = [];
            try {
                categories = @json($categories->items());
                console.log('Données des catégories:', categories);
            } catch (e) {
                console.error('Erreur lors du parsing des catégories:', e.message);
                categories = [];
            }

            function toggleSelectAll() {
                console.log('Sélection de toutes les catégories');
                const selectAll = document.getElementById('selectAll');
                const checkboxes = document.querySelectorAll('.category-checkbox');

                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAll.checked;
                });

                updateBulkActions();
            }

            function updateBulkActions() {
                console.log('Mise à jour des actions en masse');
                const checkboxes = document.querySelectorAll('.category-checkbox:checked');
                const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

                selectedCategories = Array.from(checkboxes).map(cb => cb.value);
                bulkDeleteBtn.disabled = selectedCategories.length === 0;

                const selectAll = document.getElementById('selectAll');
                const allCheckboxes = document.querySelectorAll('.category-checkbox');
                selectAll.checked = selectedCategories.length === allCheckboxes.length;
                selectAll.indeterminate = selectedCategories.length > 0 && selectedCategories.length < allCheckboxes.length;
            }

            function editCategory(id) {
                console.log('Édition de la catégorie ID:', id);
                window.location.href = `/admin/categories/${id}/edit`;
            }

            function confirmDelete(id) {
                console.log('Confirmation de suppression pour la catégorie ID:', id);
                categoryToDelete = id;
                document.getElementById('deleteModalText').textContent =
                    'Êtes-vous sûr de vouloir supprimer cette catégorie ? Cette action est irréversible.';
                document.getElementById('deleteModal').classList.add('active');

                document.getElementById('confirmDeleteBtn').onclick = () => deleteCategory(id);
            }

            function deleteCategory(id) {
                console.log('Suppression de la catégorie ID:', id);
                const token = localStorage.getItem('jwt_token');
                if (!token) {
                    console.error('Token JWT manquant');
                    alert('Vous devez être connecté pour effectuer cette action.');
                    window.location.href = '{{ route("login") }}';
                    return;
                }

                fetch(`/admin/categories/${id}`, {
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
                                    categories = categories.filter(c => c.id != id);
                                    filterCategories();
                                }, 500);
                            }
                        } else {
                            console.error('Erreur de suppression:', data.error);
                            alert(data.error || 'Erreur lors de la suppression de la catégorie');
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau lors de la suppression:', error.message);
                        alert('Une erreur est survenue lors de la suppression: ' + error.message);
                    });
            }

            function bulkDelete() {
    if (selectedCategories.length === 0) return;

    console.log('Suppression en masse des catégories:', selectedCategories);
    const count = selectedCategories.length;
    document.getElementById('deleteModalText').textContent =
        `Êtes-vous sûr de vouloir supprimer ${count} catégorie${count > 1 ? 's' : ''} ? Cette action est irréversible.`;
    document.getElementById('deleteModal').classList.add('active');

    document.getElementById('confirmDeleteBtn').onclick = () => {
        const token = localStorage.getItem('jwt_token');
        if (!token) {
            console.error('Token JWT manquant pour suppression en masse');
            alert('Vous devez être connecté pour effectuer cette action.');
            window.location.href = '{{ route("login") }}';
            return;
        }

        // Store selected categories before clearing
        const categoriesToDelete = [...selectedCategories];
        
        // Close modal immediately
        closeDeleteModal();
        
        // Remove selected categories from local data and update UI immediately
        categoriesToDelete.forEach(id => {
            const row = document.querySelector(`tr[data-id="${id}"]`);
            if (row) row.remove();
            categories = categories.filter(c => c.id != id);
        });
        
        // Update UI components
        updateStats();
        filterCategories();
        selectedCategories = [];
        updateBulkActions();
        
        // Reset select all checkbox
        const selectAll = document.getElementById('selectAll');
        selectAll.checked = false;
        selectAll.indeterminate = false;
        
        // Send delete requests in background
        Promise.all(categoriesToDelete.map(id =>
            fetch(`{{ route('admin.categories.destroy', ['category' => ':id']) }}`.replace(':id', id), {
                method: 'DELETE',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
        ));
    };
}

            function closeDeleteModal() {
                console.log('Fermeture de la modale de suppression');
                document.getElementById('deleteModal').classList.remove('active');
                categoryToDelete = null;
            }

            function updateStats() {
                console.log('Mise à jour des statistiques');
                const visibleRows = document.querySelectorAll('#categoriesTableBody tr:not(.empty-state)');
                document.getElementById('totalCategories').textContent = visibleRows.length;
                document.getElementById('activeCategories').textContent = visibleRows.length;
            }

            function filterCategories() {
                console.log('Filtrage des catégories');
                const searchInput = document.getElementById('searchInput').value.toLowerCase();
                const tbody = document.getElementById('categoriesTableBody');

                tbody.innerHTML = '';

                const filteredCategories = categories.filter(category => {
                    const matchesSearch = (category.name || '').toLowerCase().includes(searchInput) ||
                        (category.description || '').toLowerCase().includes(searchInput);
                    return matchesSearch;
                });

                if (filteredCategories.length === 0) {
                    tbody.innerHTML = `
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-folder-open"></i>
                                <p>Aucune catégorie trouvée.</p>
                            </td>
                        </tr>
                    `;
                } else {
                    filteredCategories.forEach(category => {
                        const row = document.createElement('tr');
                        row.className = 'category-row';
                        row.setAttribute('data-id', category.id);
                        row.innerHTML = `
                            <td><input type="checkbox" class="checkbox category-checkbox" value="${category.id}" onchange="updateBulkActions()"></td>
                            <td>${category.name}</td>
                            <td>${(category.description || 'Aucune description').substring(0, 100)}${category.description && category.description.length > 100 ? '...' : ''}</td>
                            <td>${category.events_count || 0}</td>
                            <td>
                                <div class="action-buttons">
                                    <button class="action-btn btn-edit" onclick="editCategory(${category.id})" title="Modifier"><i class="fas fa-edit"></i></button>
                                    <button class="action-btn btn-delete" onclick="confirmDelete(${category.id})" title="Supprimer"><i class="fas fa-trash"></i></button>
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
                searchInput.addEventListener('input', filterCategories);
            }

            document.addEventListener('DOMContentLoaded', function() {
                console.log('Document chargé, initialisation des filtres');
                setupFilters();
                updateStats();
            });
        </script>
    @endpush
@endsection