@extends('layouts.admin')

@section('title', 'Modifier un Événement - Echofy')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Modifier un Événement</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Événements</a></li>
                <li class="breadcrumb-item active">Modifier</li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Formulaire de Modification d'Événement</h6>
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
                        <h2>Modifier l'événement "{{ $event->title }}"</h2>
                        <p>Modifiez les détails de cet événement sur Echofy</p>
                    </div>

                    <form id="eventForm" method="POST" action="{{ route('admin.events.update', $event->id) }}" enctype="multipart/form-data">
                        @method('PUT')
                        @csrf
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informations de base
                            </div>

                            <div class="form-group">
                                <label for="title">Titre <span class="required">*</span></label>
                                <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $event->title) }}" required>
                                <div class="character-count"><span id="titleCount">0</span>/255 caractères</div>
                                <div class="error-message" id="titleError"></div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Décrivez brièvement l'événement...">{{ old('description', $event->description) }}</textarea>
                                <div class="character-count"><span id="descriptionCount">0</span>/1000 caractères</div>
                                <div class="error-message" id="descriptionError"></div>
                            </div>

                            <div class="form-group">
                                <label for="date">Date <span class="required">*</span></label>
                                <input type="date" id="date" name="date" class="form-control" value="{{ old('date', $event->date?->toDateString()) }}" required>
                                <div class="error-message" id="dateError"></div>
                            </div>

                            <div class="form-group">
                                <label for="location">Lieu <span class="required">*</span></label>
                                <input type="text" id="location" name="location" class="form-control" value="{{ old('location', $event->location) }}" required>
                                <div class="character-count"><span id="locationCount">0</span>/255 caractères</div>
                                <div class="error-message" id="locationError"></div>
                            </div>

                            <div class="form-group">
                                <label for="capacity">Capacité (optionnel)</label>
                                <input type="number" id="capacity" name="capacity" class="form-control" value="{{ old('capacity', $event->capacity) }}" min="0">
                                <div class="error-message" id="capacityError"></div>
                            </div>

                            <div class="form-group">
                                <label for="status">Statut <span class="required">*</span></label>
                                <select id="status" name="status" class="form-control" required>
                                    <option value="upcoming" {{ old('status', $event->status) === 'upcoming' ? 'selected' : '' }}>À venir</option>
                                    <option value="ongoing" {{ old('status', $event->status) === 'ongoing' ? 'selected' : '' }}>Actif</option>
                                    <option value="completed" {{ old('status', $event->status) === 'completed' ? 'selected' : '' }}>Terminé</option>
                                    <option value="cancelled" {{ old('status', $event->status) === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                                <div class="error-message" id="statusError"></div>
                            </div>

                            <div class="form-group">
                                <label for="category_id">Catégorie <span class="required">*</span></label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id', $event->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                    @endforeach
                                </select>
                                <div class="error-message" id="category_idError"></div>
                            </div>

                            <div class="form-group">
                                <label for="img">Image (optionnel)</label>
                                @if ($event->img)
                                    <div class="current-image">
                                        <img src="{{ Storage::url($event->img) }}" alt="Image actuelle" style="max-width: 200px; max-height: 200px; margin-bottom: 10px;">
                                        <p>Image actuelle : {{ basename($event->img) }}</p>
                                    </div>
                                @endif
                                <input type="file" id="img" name="img" class="form-control-file" accept="image/jpeg,image/png,image/jpg,image/gif,image/webp">
                                <div class="error-message" id="imgError"></div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" onclick="cancelForm()">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-save"></i> Sauvegarder les Modifications
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

        .current-image {
            margin-bottom: 1rem;
        }

        .current-image img {
            border: 2px solid #e9ecef;
            border-radius: 8px;
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
            });
        }

        // Validation du formulaire
        function validateForm() {
            let isValid = true;

            // Validation du titre
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

            // Validation de la description
            const description = document.getElementById('description');
            if (description.value.length > 1000) {
                showError('descriptionError', 'La description ne peut pas dépasser 1000 caractères');
                isValid = false;
            } else {
                hideError('descriptionError');
            }

            // Validation de la date
            const date = document.getElementById('date');
            if (!date.value) {
                showError('dateError', 'La date est requise');
                isValid = false;
            } else {
                const today = new Date().toISOString().split('T')[0];
                if (date.value < today) {
                    showError('dateError', 'La date doit être aujourd\'hui ou après');
                    isValid = false;
                } else {
                    hideError('dateError');
                }
            }

            // Validation du lieu
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

            // Validation de la capacité
            const capacity = document.getElementById('capacity');
            if (capacity.value < 0) {
                showError('capacityError', 'La capacité ne peut pas être négative');
                isValid = false;
            } else {
                hideError('capacityError');
            }

            // Validation du statut
            const status = document.getElementById('status');
            if (!status.value) {
                showError('statusError', 'Le statut est requise');
                isValid = false;
            } else {
                hideError('statusError');
            }

            // Validation de la catégorie
            const category_id = document.getElementById('category_id');
            if (!category_id.value) {
                showError('category_idError', 'La catégorie est requise');
                isValid = false;
            } else {
                hideError('category_idError');
            }

            // Validation de l'image
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
                errorElement.textContent = message; // Overwrite with the latest error
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

            // Clear existing server-side errors before validation
            ['title', 'description', 'date', 'location', 'capacity', 'status', 'category_id', 'img'].forEach(field => {
                hideError(`${field}Error`);
            });

            if (!validateForm()) {
                return;
            }

            const submitBtn = document.getElementById('submitBtn');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sauvegarde en cours...';
            submitBtn.disabled = true;

            const token = localStorage.getItem('jwt_token');
            if (!token) {
                alert('Vous devez être connecté pour modifier un événement.');
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                window.location.href = '{{ route("login") }}';
                return;
            }

            const formData = new FormData();
            formData.append('_method', 'PUT'); // Simulate PUT request
            formData.append('title', document.getElementById('title').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('date', document.getElementById('date').value);
            formData.append('location', document.getElementById('location').value);
            formData.append('capacity', document.getElementById('capacity').value || '');
            formData.append('status', document.getElementById('status').value);
            formData.append('category_id', document.getElementById('category_id').value);
            if (document.getElementById('img').files[0]) {
                formData.append('img', document.getElementById('img').files[0]);
            }

            fetch('{{ route("admin.events.update", $event->id) }}', {
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
                    // Display all validation errors for each field
                    Object.keys(error.data.messages).forEach(field => {
                        const errorElement = document.getElementById(`${field}Error`);
                        if (errorElement) {
                            errorElement.textContent = error.data.messages[field].join('\n'); // Show all errors with newlines
                            errorElement.style.display = 'block';
                            const input = document.getElementById(field);
                            if (input) input.classList.add('error');
                        }
                    });
                } else {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la modification');
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

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            setupCharacterCounters();
        });
    </script>
@endpush