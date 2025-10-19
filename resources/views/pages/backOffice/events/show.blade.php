@extends('layouts.admin')

@section('title', 'Détails de l\'Événement - Echofy')

@vite(['resources/js/app.js', 'resources/css/app.css'])

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
                        <a href="{{ route('admin.events.subscribers', $event->id) }}" class="btn btn-info">
                            <i class="fas fa-users"></i> Abonnés ({{ $event->registrations->count() }})
                        </a>
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
                            @if ($event->img && Storage::disk('public')->exists($event->img))
                                <img src="{{ Storage::url($event->img) }}" alt="{{ $event->title }}" class="img-fluid rounded" style="max-height: 300px; object-fit: cover;">
                            @else
                                <div class="default-event-placeholder d-flex align-items-center justify-content-center" style="height: 300px; background: linear-gradient(135deg, #28a745, #20c997); color: white; border-radius: 8px;">
                                    <i class="fas fa-calendar-alt" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-8">
                            <p><strong>Description :</strong> {{ $event->description ?? 'Aucune description' }}</p>
                            <p><strong>Catégorie :</strong> {{ $event->category->name ?? 'N/A' }}</p>
                            <p><strong>Date et Heure :</strong> {{ $event->date ? $event->date->format('d/m/Y H:i') : 'N/A' }}</p>
                            <p><strong>Lieu :</strong> {{ $event->location ?? 'N/A' }}</p>
                            <p><strong>Capacité :</strong> {{ $event->capacity ?? 'N/A' }}</p>
                            <p><strong>Inscriptions :</strong> {{ $event->registrations->count() }}</p>
                            <p><strong>Organisateur :</strong> {{ $event->organizer?->name ?? 'Inconnu' }} ({{ $event->organizer?->email ?? 'N/A' }})</p>
                            <p><strong>Créé le :</strong> {{ $event->created_at?->format('d/m/Y') ?? 'N/A' }}</p>
                        </div>
                    </div>
                    @if ($event->latitude && $event->longitude)
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-map-marker-alt"></i> Emplacement</h5>
                                <div id="map" style="height: 300px; width: 100%; border: 1px solid #ddd; border-radius: 8px;"></div>
                            </div>
                        </div>
                        <!-- Registrations Table -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <h5 class="section-title"><i class="fas fa-users"></i> Inscriptions</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover" id="registrationsTable">
                                        <thead style="background: linear-gradient(135deg, #28a745, #20c997); color: white;">
                                            <tr>
                                                <th>Nom</th>
                                                <th>Email</th>
                                                <th>Rôle</th>
                                                <th>Compétences</th>
                                                <th>Inscrit le</th>
                                                <th>Transport</th>
                                                <th>Participation Précédente</th>
                                                <th>Contact d'Urgence</th>
                                            </tr>
                                        </thead>
                                        <tbody id="registrationsBody">
                                            <!-- Populated dynamically via JavaScript -->
                                        </tbody>
                                    </table>
                                    <div id="noRegistrations" class="alert alert-info" style="display: none;">
                                        Aucune inscription pour cet événement.
                                    </div>
                                    <div id="errorMessage" class="alert alert-danger" style="display: none;">
                                        Erreur lors du chargement des inscriptions. Veuillez réessayer.
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
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
@endsection

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
        .btn-info {
            background: #17a2b8;
            color: white;
        }
        .btn-info:hover {
            background: #138496;
            transform: translateY(-1px);
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
        .section-title {
            color: #28a745;
            font-size: 1.3rem;
            font-weight: 600;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .table-responsive {
            border-radius: 8px;
            overflow-x: auto;
        }
        .table {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .table th, .table td {
            vertical-align: middle;
            padding: 12px;
            font-size: 0.9rem;
        }
        .table th {
            font-weight: 600;
            text-transform: uppercase;
        }
        .table-hover tbody tr:hover {
            background-color: #f8f9fa;
        }
        .alert {
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
    </style>
@endpush

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

        // Fetch and display registrations
        document.addEventListener('DOMContentLoaded', function() {
            fetchRegistrations();
        });

        function fetchRegistrations() {
            const eventId = {{ $event->id }};
            const registrationsBody = document.getElementById('registrationsBody');
            const noRegistrations = document.getElementById('noRegistrations');
            const errorMessage = document.getElementById('errorMessage');

            fetch(`/api/events/${eventId}/registrations`, {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 401) {
                        throw new Error('Vous devez être connecté.');
                    } else if (response.status === 403) {
                        throw new Error('Vous n\'êtes pas autorisé à voir ces inscriptions.');
                    } else {
                        throw new Error('Erreur lors du chargement des inscriptions.');
                    }
                }
                return response.json();
            })
            .then(data => {
                registrationsBody.innerHTML = '';
                noRegistrations.style.display = 'none';
                errorMessage.style.display = 'none';

                if (data.success && data.registrations.length > 0) {
                    data.registrations.forEach(reg => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${reg.user.name || 'N/A'}</td>
                            <td>${reg.user.email || 'N/A'}</td>
                            <td>${reg.role || 'N/A'}</td>
                            <td>${reg.skills || 'N/A'}</td>
                            <td>${new Date(reg.registered_at).toLocaleString('fr-FR', { dateStyle: 'short', timeStyle: 'short' })}</td>
                            <td>${reg.has_transportation ? 'Oui' : 'Non'}</td>
                            <td>${reg.has_participated_before ? 'Oui' : 'Non'}</td>
                            <td>${reg.emergency_contact || 'N/A'}</td>
                        `;
                        registrationsBody.appendChild(row);
                    });
                } else {
                    noRegistrations.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                errorMessage.textContent = error.message || 'Erreur lors du chargement des inscriptions. Veuillez réessayer.';
                errorMessage.style.display = 'block';
                noRegistrations.style.display = 'none';
            });
        }

        function formatStatus(status) {
            switch (status) {
                case 'registered': return 'Inscrit';
                case 'cancelled': return 'Annulé';
                case 'pending': return 'En attente';
                default: return status || 'N/A';
            }
        }

        // Initialize map if coordinates exist
        @if ($event->latitude && $event->longitude)
            document.addEventListener('DOMContentLoaded', function() {
                const latitude = {{ $event->latitude }};
                const longitude = {{ $event->longitude }};

                const vectorSource = new window.ol.VectorSource();
                const vectorLayer = new window.ol.VectorLayer({
                    source: vectorSource
                });

                const marker = new window.ol.Feature({
                    geometry: new window.ol.Point(window.ol.fromLonLat([longitude, latitude]))
                });
                marker.setStyle(new window.ol.Style({
                    image: new window.ol.Icon({
                        anchor: [0.5, 1],
                        src: 'data:image/svg+xml;base64,' + btoa(`
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#ff0000"/>
                            </svg>
                        `),
                        scale: 1.5
                    })
                }));
                vectorSource.addFeature(marker);

                const map = new window.ol.Map({
                    target: 'map',
                    layers: [
                        new window.ol.TileLayer({
                            source: new window.ol.OSM({
                                url: 'https://{a-c}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png'
                            })
                        }),
                        vectorLayer
                    ],
                    controls: window.ol.defaultControls({
                        attribution: false,
                        rotate: false,
                        zoom: true
                    }),
                    view: new window.ol.View({
                        center: window.ol.fromLonLat([longitude, latitude]),
                        zoom: 15
                    })
                });
            });
        @endif
    </script>
@endpush