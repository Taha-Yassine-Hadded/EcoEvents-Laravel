@extends('layouts.admin')

@section('title', 'Détails de l\'Événement - Echofy')

@section('content')
    <div class="content-header">
        <h1 class="page-title">Détails de l'Événement</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Événements</a></li>
                <li class="breadcrumb-item active">{{ $event->title }}</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">{{ $event->title }}</h6>
                    <div class="action-buttons">
                        <a href="{{ route('admin.events.edit', $event->id) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <button class="btn btn-danger" onclick="confirmDelete({{ $event->id }})">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <img src="{{ $event->img ? Storage::url($event->img) : asset('storage/events/default-event.jpg') }}" alt="{{ $event->title }}" class="img-fluid rounded" style="max-height: 300px; object-fit: cover;">
                        </div>
                        <div class="col-md-8">
                            <p><strong>Description :</strong> {{ $event->description ?? 'Aucune description' }}</p>
                            <p><strong>Catégorie :</strong> {{ $event->category->name ?? 'N/A' }}</p>
                            <p><strong>Statut :</strong> {{ ucfirst($event->status == 'upcoming' ? 'À venir' : ($event->status == 'ongoing' ? 'En cours' : ($event->status == 'completed' ? 'Terminé' : 'Annulé'))) }}</p>
                            <p><strong>Date :</strong> {{ $event->date?->format('d/m/Y') ?? 'N/A' }}</p>
                            <p><strong>Lieu :</strong> {{ $event->location ?? 'N/A' }}</p>
                            <p><strong>Capacité :</strong> {{ $event->capacity ?? 'N/A' }}</p>
                            <p><strong>Inscriptions :</strong> {{ $event->registrations->count() }}</p>
                            <p><strong>Organisateur :</strong> {{ $event->organizer?->name ?? 'Inconnu' }} ({{ $event->organizer?->email ?? 'N/A' }})</p>
                            <p><strong>Créé le :</strong> {{ $event->created_at?->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
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
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            let eventToDelete = null;

            function confirmDelete(id) {
                eventToDelete = id;
                document.getElementById('deleteModalText').textContent =
                    'Êtes-vous sûr de vouloir supprimer cet événement ? Cette action est irréversible.';
                document.getElementById('deleteModal').classList.add('active');

                document.getElementById('confirmDeleteBtn').onclick = () => deleteEvent(id);
            }

            function deleteEvent(id) {
                fetch(`/admin/events/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                    .then(response => {
                        if (!response.ok) throw new Error('Erreur lors de la suppression');
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            window.location.href = '{{ route('admin.events.index') }}';
                        } else {
                            alert('Erreur: ' + (data.error || 'Suppression échouée'));
                        }
                    })
                    .catch(error => {
                        console.error('Erreur réseau:', error);
                        alert('Erreur réseau lors de la suppression');
                    })
                    .finally(() => closeDeleteModal());
            }

            function closeDeleteModal() {
                document.getElementById('deleteModal').classList.remove('active');
                eventToDelete = null;
            }
        </script>
    @endpush

    @push('styles')
        <style>
            .action-buttons {
                display: flex;
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
            .btn-cancel {
                background: #6c757d;
                color: white;
            }
            .btn-cancel:hover {
                background: #545b62;
            }
        </style>
    @endpush
@endsection