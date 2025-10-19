@extends('layouts.admin')

@section('title', 'Créer un Événement - Echofy')

@vite(['resources/js/app.js', 'resources/css/app.css'])

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Créer un Nouvel Événement</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Événements</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Formulaire de Création d'Événement</h6>
                </div>
                <div class="card-body">
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="form-header">
                        <h2>Créer un nouvel événement</h2>
                        <p>Définissez un événement pour votre plateforme Echofy</p>
                    </div>

                    <form id="eventForm" method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informations de base
                            </div>

                            <div class="form-group">
                                <label for="title">Titre <span class="required">*</span></label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ old('title') }}" required>
                                <div class="character-count"><span id="titleCount">0</span>/255 caractères</div>
                                <div class="error-message" id="titleError"></div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Décrivez brièvement l'événement...">{{ old('description') }}</textarea>
                                <div class="character-count"><span id="descriptionCount">0</span>/1000 caractères</div>
                                <div class="error-message" id="descriptionError"></div>
                            </div>

                            <div class="form-group">
                                <label for="date">Date et Heure <span class="required">*</span></label>
                                <input type="datetime-local" id="date" name="date" class="form-control" value="{{ old('date') }}" required>
                                <div class="error-message" id="dateError"></div>
                            </div>

                            <div class="form-group">
                                <label for="location">Lieu <span class="required">*</span></label>
                                <div class="location-input-container">
                                    <input type="text" id="location" name="location" class="form-control" value="{{ old('location') }}" required placeholder="Saisissez l'adresse ou utilisez la carte">
                                    <button type="button" id="useMapBtn" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-map-marker-alt"></i> Utiliser la carte
                                    </button>
                                </div>
                                <div class="character-count"><span id="locationCount">0</span>/255 caractères</div>
                                <div class="error-message" id="locationError"></div>
                                
                                <!-- Hidden fields for coordinates -->
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
                                
                                <!-- Map container -->
                                <div id="mapContainer" class="map-container" style="display: none;">
                                    <div id="map" style="height: 300px; width: 100%;"></div>
                                    <div class="map-controls">
                                        <button type="button" id="confirmLocationBtn" class="btn btn-primary btn-sm">
                                            <i class="fas fa-check"></i> Confirmer l'emplacement
                                        </button>
                                        <button type="button" id="cancelMapBtn" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-times"></i> Annuler
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="capacity">Capacité (optionnel)</label>
                                <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity') }}" min="0">
                                <div class="error-message" id="capacityError"></div>
                            </div>

                            <div class="form-group">
                                <label for="status">Statut <span class="required">*</span></label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="upcoming" {{ old('status') === 'upcoming' ? 'selected' : '' }}>À venir</option>
                                    <option value="ongoing" {{ old('status') === 'ongoing' ? 'selected' : '' }}>Actif</option>
                                </select>
                                <div class="error-message" id="statusError"></div>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Catégorie <span class="required">*</span></label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="error-message" id="category_idError"></div>
                            </div>

                            <div class="form-group">
                                <label for="img">Image (optionnel)</label>
                                <input type="file" id="img" name="img" class="form-control-file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                <div class="error-message" id="imgError"></div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" onclick="cancelForm()">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-plus"></i> Créer l'Événement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .form-header {
            text-align: center;
            margin-bottom: 2rem;
        }

        .form-header h2 {
            color: #333;
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .form-header p {
            color: #666;
            font-size: 1.1rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 600;
            font-size: 1rem;
        }

        .form-group .required {
            color: #dc3545;
        }

        .form-control, .form-control-file {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-control:focus, .form-control-file:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .form-control.error, .form-control-file.error {
            border-color: #dc3545;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
        }

        .location-input-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .location-input-container input {
            flex: 1;
        }

        .map-container {
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            overflow: hidden;
        }

        .map-controls {
            padding: 10px;
            background-color: #f8f9fa;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        #map {
            border-radius: 0;
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

        .form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.8rem 2rem;
            border: none;
            border-radius: 25px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-outline {
            background: white;
            color: #28a745;
            border: 2px solid #28a745;
        }

        .btn-outline:hover {
            background: #28a745;
            color: white;
        }

        .character-count {
            text-align: right;
            color: #666;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .character-count.limit {
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .form-actions {
                flex-direction: column;
            }

            .btn {
                justify-content: center;
                width: 100%;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        let uploadedFiles = [];
        let map = null;
        let marker;
        let vectorSource;
        let vectorLayer;

        // Compteurs de caractères
        function setupCharacterCounters() {
            const counters = [
                { input: 'title', counter: 'titleCount', max: 255 },
                { input: 'description', counter: 'descriptionCount', max: 1000 },
                { input: 'location', counter: 'locationCount', max: 255 }
            ];

            counters.forEach(({ input, counter, max }) => {
                const inputElement = document.getElementById(input);
                const counterElement = document.getElementById(counter);

                inputElement.addEventListener('input', function() {
                    const count = this.value.length;
                    counterElement.textContent = count;
                    counterElement.parentElement.classList.toggle('limit', count > max);
                });

                // Initialize counter on page load
                counterElement.textContent = inputElement.value.length;
            });
        }

        // Validation du formulaire
        function validateForm() {
            let isValid = true;

            const title = document.getElementById('title');
            if (!title.value.trim()) {
                showError('titleError', 'Le titre est requis');
                isValid = false;
            } else if (title.value.length > 255) {
                showError('titleError', 'Le titre ne peut pas dépasser 255 caractères');
                isValid = false;
            } else {
                hideError('titleError');
            }

            const description = document.getElementById('description');
            if (description.value.length > 1000) {
                showError('descriptionError', 'La description ne peut pas dépasser 1000 caractères');
                isValid = false;
            } else {
                hideError('descriptionError');
            }

            const date = document.getElementById('date');
            if (!date.value) {
                showError('dateError', 'La date et l\'heure sont requises');
                isValid = false;
            } else {
                const selectedDateTime = new Date(date.value);
                const now = new Date();
                if (selectedDateTime < now) {
                    showError('dateError', 'La date et l\'heure doivent être dans le futur');
                    isValid = false;
                } else {
                    hideError('dateError');
                }
            }

            const location = document.getElementById('location');
            if (!location.value.trim()) {
                showError('locationError', 'Le lieu est requis');
                isValid = false;
            } else if (location.value.length > 255) {
                showError('locationError', 'Le lieu ne peut pas dépasser 255 caractères');
                isValid = false;
            } else {
                hideError('locationError');
            }

            const capacity = document.getElementById('capacity');
            if (capacity.value < 0) {
                showError('capacityError', 'La capacité ne peut pas être négative');
                isValid = false;
            } else {
                hideError('capacityError');
            }

            const status = document.getElementById('status');
            if (!status.value) {
                showError('statusError', 'Le statut est requis');
                isValid = false;
            } else {
                hideError('statusError');
            }

            const category_id = document.getElementById('category_id');
            if (!category_id.value) {
                showError('category_idError', 'La catégorie est requise');
                isValid = false;
            } else {
                hideError('category_idError');
            }

            const img = document.getElementById('img');
            if (img.files.length > 0 && !['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'].includes(img.files[0].type)) {
                showError('imgError', 'Format d\'image invalide (JPEG, PNG, JPG, GIF, WEBP uniquement)');
                isValid = false;
            } else if (img.files.length > 0 && img.files[0].size > 2048 * 1024) {
                showError('imgError', 'La taille de l\'image ne doit pas dépasser 2 Mo');
                isValid = false;
            } else {
                hideError('imgError');
            }

            return isValid;
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.textContent = message;
                errorElement.style.display = 'block';
                const input = document.getElementById(elementId.replace('Error', ''));
                if (input) input.classList.add('error');
            }
        }

        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            if (errorElement) {
                errorElement.style.display = 'none';
                const input = document.getElementById(elementId.replace('Error', ''));
                if (input) input.classList.remove('error');
            }
        }

        // Soumission du formulaire
        document.getElementById('eventForm').addEventListener('submit', function(e) {
            e.preventDefault();

            ['title', 'description', 'date', 'location', 'capacity', 'status', 'category_id', 'img'].forEach(field => {
                hideError(`${field}Error`);
            });

            if (!validateForm()) {
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
            submitBtn.disabled = true;

            const token = localStorage.getItem('jwt_token');
            if (!token) {
                alert('Vous devez être connecté pour créer un événement.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                window.location.href = '{{ route("login") }}';
                return;
            }

            const formData = new FormData();
            formData.append('title', document.getElementById('title').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('date', document.getElementById('date').value);
            formData.append('location', document.getElementById('location').value);
            formData.append('latitude', document.getElementById('latitude').value || '');
            formData.append('longitude', document.getElementById('longitude').value || '');
            formData.append('capacity', document.getElementById('capacity').value || '');
            formData.append('status', document.getElementById('status').value);
            formData.append('category_id', document.getElementById('category_id').value);
            if (document.getElementById('img').files[0]) {
                formData.append('img', document.getElementById('img').files[0]);
            }

            fetch('{{ route("admin.events.store") }}', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 422) {
                        return response.json().then(data => {
                            throw { status: 422, data };
                        });
                    }
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("admin.events.index") }}';
                }
            })
            .catch(error => {
                if (error.status === 422 && error.data && error.data.messages) {
                    Object.keys(error.data.messages).forEach(field => {
                        const errorElement = document.getElementById(`${field}Error`);
                        if (errorElement) {
                            errorElement.textContent = error.data.messages[field].join('\n');
                            errorElement.style.display = 'block';
                            const input = document.getElementById(field);
                            if (input) input.classList.add('error');
                        }
                    });
                } else {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la création');
                }
            })
            .finally(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            });
        });

        // Annuler le formulaire
        function cancelForm() {
            if (confirm('Êtes-vous sûr de vouloir annuler ? Vos modifications seront perdues.')) {
                window.location.href = '{{ route("admin.events.index") }}';
            }
        }

        // Initialize map when use map button is clicked
        document.getElementById('useMapBtn').addEventListener('click', function() {
            document.getElementById('mapContainer').style.display = 'block';
            setTimeout(() => initMap(), 0); // Ensure DOM is ready
        });

        // Cancel map
        document.getElementById('cancelMapBtn').addEventListener('click', function() {
            document.getElementById('mapContainer').style.display = 'none';
            if (map) {
                map.setTarget(null); // Unbind map from DOM
                map = null;
                vectorSource = null;
                vectorLayer = null;
                marker = null;
                document.getElementById('map').innerHTML = ''; // Clear map container
            }
        });

        // Confirm location
        document.getElementById('confirmLocationBtn').addEventListener('click', function() {
            if (marker) {
                const coordinates = marker.getGeometry().getCoordinates();
                const [lon, lat] = window.ol.toLonLat(coordinates); // Transform to EPSG:4326
                document.getElementById('latitude').value = lat;
                document.getElementById('longitude').value = lon;
                reverseGeocode(lat, lon);
            } else {
                alert('Veuillez sélectionner un emplacement sur la carte.');
            }
        });

        function initMap() {
            if (map) {
                map.setTarget(null);
                document.getElementById('map').innerHTML = '';
            }

            // Create vector source and layer for markers
            vectorSource = new window.ol.VectorSource();
            vectorLayer = new window.ol.VectorLayer({
                source: vectorSource
            });

            // Check for existing coordinates (for edit mode)
            const latitudeInput = document.getElementById('latitude').value;
            const longitudeInput = document.getElementById('longitude').value;
            let center = [10.1936, 36.8663]; // Default to Ariana, Tunis, Tunisia

            if (latitudeInput && longitudeInput && !isNaN(latitudeInput) && !isNaN(longitudeInput)) {
                center = [parseFloat(longitudeInput), parseFloat(latitudeInput)];
            }

            // Create map with French OSM tiles
            map = new window.ol.Map({
                target: 'map',
                layers: [
                    new window.ol.TileLayer({
                        source: new window.ol.OSM({
                            url: 'https://{a-c}.tile.openstreetmap.fr/osmfr/{z}/{x}/{y}.png' // French OSM tile server
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
                    center: window.ol.fromLonLat(center),
                    zoom: 13
                })
            });

            // Place marker if editing with existing coordinates
            if (latitudeInput && longitudeInput && !isNaN(latitudeInput) && !isNaN(longitudeInput)) {
                const coordinates = window.ol.fromLonLat([parseFloat(longitudeInput), parseFloat(latitudeInput)]);
                marker = new window.ol.Feature({
                    geometry: new window.ol.Point(coordinates)
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
            }

            // Add click event to map
            map.on('singleclick', function(event) {
                const coordinates = event.coordinate;
                vectorSource.clear();
                marker = new window.ol.Feature({
                    geometry: new window.ol.Point(coordinates)
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
            });
        }

        function reverseGeocode(lat, lon) {
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&accept-language=fr`)
                .then(response => response.json())
                .then(data => {
                    const locationInput = document.getElementById('location');
                    if (data.display_name) {
                        locationInput.value = data.display_name;
                    } else {
                        locationInput.value = `Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`;
                    }
                    document.getElementById('mapContainer').style.display = 'none';
                    const count = locationInput.value.length;
                    document.getElementById('locationCount').textContent = count;
                    document.getElementById('locationCount').parentElement.classList.toggle('limit', count > 255);
                })
                .catch(error => {
                    console.error('Reverse geocoding failed:', error);
                    const locationInput = document.getElementById('location');
                    locationInput.value = `Lat: ${lat.toFixed(6)}, Lon: ${lon.toFixed(6)}`;
                    document.getElementById('mapContainer').style.display = 'none';
                    const count = locationInput.value.length;
                    document.getElementById('locationCount').textContent = count;
                    document.getElementById('locationCount').parentElement.classList.toggle('limit', count > 255);
                });
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            setupCharacterCounters();
        });
    </script>
@endpush