@extends('layouts.admin')

@section('title', 'Créer une Catégorie - Echofy')

@section('content')
    <!-- Content Header -->
    <div class="content-header">
        <h1 class="page-title">Créer une Nouvelle Catégorie</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Catégories</a></li>
                <li class="breadcrumb-item active">Créer</li>
            </ol>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">Formulaire de Création de Catégorie</h6>
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
                        <h2>Créer une nouvelle catégorie</h2>
                        <p>Définissez une catégorie pour organiser vos événements ou campagnes</p>
                    </div>

                    <form id="categoryForm" method="POST" action="{{ route('admin.categories.store') }}">
                        @csrf
                        <div class="form-section">
                            <div class="section-title">
                                <i class="fas fa-info-circle"></i>
                                Informations de base
                            </div>

                            <div class="form-group">
                                <label for="name">Nom <span class="required">*</span></label>
                                <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}" required>
                                <div class="character-count"><span id="nameCount">0</span>/100 caractères</div>
                                <div class="error-message" id="nameError"></div>
                            </div>

                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea id="description" name="description" class="form-control" rows="4" placeholder="Décrivez brièvement la catégorie...">{{ old('description') }}</textarea>
                                <div class="character-count"><span id="descriptionCount">0</span>/500 caractères</div>
                                <div class="error-message" id="descriptionError"></div>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="button" class="btn btn-outline" onclick="cancelForm()">
                                <i class="fas fa-times"></i> Annuler
                            </button>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <i class="fas fa-plus"></i> Créer la Catégorie
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

        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .form-control.error {
            border-color: #dc3545;
        }

        .error-message {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            display: none;
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
                { input: 'name', counter: 'nameCount', max: 100 },
                { input: 'description', counter: 'descriptionCount', max: 500 }
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

            // Validation du nom
            const name = document.getElementById('name');
            if (!name.value.trim()) {
                showError('nameError', 'Le nom est requis');
                isValid = false;
            } else if (name.value.length > 100) {
                showError('nameError', 'Le nom ne peut pas dépasser 100 caractères');
                isValid = false;
            } else {
                hideError('nameError');
            }

            // Validation de la description
            const description = document.getElementById('description');
            if (description.value.length > 500) {
                showError('descriptionError', 'La description ne peut pas dépasser 500 caractères');
                isValid = false;
            } else {
                hideError('descriptionError');
            }

            return isValid;
        }

        function showError(elementId, message) {
            const errorElement = document.getElementById(elementId);
            errorElement.textContent = message;
            errorElement.style.display = 'block';
        }

        function hideError(elementId) {
            const errorElement = document.getElementById(elementId);
            errorElement.style.display = 'none';
        }

        // Soumission du formulaire
        document.getElementById('categoryForm').addEventListener('submit', function(e) {
        e.preventDefault();

        if (!validateForm()) {
            return;
        }

        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création en cours...';
        submitBtn.disabled = true;

        const token = localStorage.getItem('jwt_token');
        if (!token) {
            alert('Vous devez être connecté pour créer une catégorie.');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            window.location.href = '{{ route("login") }}';
            return;
        }

        fetch('{{ route("admin.categories.store") }}', {
            method: 'POST',
            headers: {
                'Authorization': `Bearer ${token}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                name: document.getElementById('name').value,
                description: document.getElementById('description').value
            })
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
                if (data.redirect) {
                    window.location.href = data.redirect;
                }
            }
        })
        .catch(error => {
            if (error.status === 422 && error.data && error.data.messages) {
                // Display validation errors
                Object.keys(error.data.messages).forEach(field => {
                    const errorElement = document.getElementById(`${field}Error`);
                    if (errorElement) {
                        errorElement.textContent = error.data.messages[field][0]; // First error message
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
                window.location.href = '{{ route("admin.categories.index") }}';
            }
        }

        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            setupCharacterCounters();
        });
    </script>
@endpush