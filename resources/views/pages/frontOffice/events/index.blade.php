@extends('layouts.app')

@section('title', 'Echofy - Événements')

@section('content')
    {{-- Breadcrumb Section --}}
    <div class="breadcumb-area">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-12 text-center">
                    <div class="breadcumb-content">
                        <div class="breadcumb-title">
                            <h4>Nos Événements</h4>
                        </div>
                        <ul>
                            <li>
                                <a href="{{ url('/') }}">
                                    <img src="{{ Vite::asset('resources/assets/images/inner-images/breadcumb-text-shape.png') }}" alt="">EcoEvents
                                </a>
                            </li>
                            <li>Événements</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Events Grid Area --}}
    <div class="blog-grid-area">
        <div class="container">
            <div class="row">
                {{-- Events List --}}
                <div class="col-lg-8">
                    <div class="row">
                        @forelse ($events as $event)
                            @include('pages.frontOffice.events.partials.event-card', ['event' => $event])
                        @empty
                            <div class="col-12 text-center">
                                <p>Aucun événement trouvé.</p>
                            </div>
                        @endforelse
                    </div>
                    
                    {{-- Pagination --}}
                    @include('pages.frontOffice.events.partials.pagination')
                </div>

                {{-- Sidebar Filters --}}
                <div class="col-lg-4">
                    <div class="row">
                        <div class="col-lg-12">
                            @include('pages.frontOffice.events.partials.search-widget')
                            
                            {{-- Create Event Button (Hidden by default, shown via JavaScript for organizers) --}}
                            <div id="create-event-container" style="display: none;">
                                <div class="widget-sidber mb-4">
                                    <div class="widget-sidber-content">
                                        <h4>Créer un événement</h4>
                                    </div>
                                    <div class="widget-body text-center">
                                        <a href="{{ route('admin.events.create') }}" class="btn btn-success w-100" style="border-radius: 25px; font-weight: 600; padding: 12px 20px; background: linear-gradient(135deg, #28a745, #20c997); border: none;">
                                            <i class="fas fa-plus"></i> Nouveau événement
                                        </a>
                                    </div>
                                </div>
                            </div>

                            {{-- Organizer Filter (Hidden by default, shown via JavaScript for organizers) --}}
                            <div id="organizer-filter-container" style="display: none;">
                                @include('pages.frontOffice.events.partials.organizer-filter')
                            </div>
                            
                            @include('pages.frontOffice.events.partials.categories-filter', ['categories' => $categories ?? []])
                            @include('pages.frontOffice.events.partials.time-filter')
                            @include('pages.frontOffice.events.partials.status-filter')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/events-frontend.css') }}">
        <style>
            /* Organizer Filter Styling */
            .organizer-events-filter {
                background: linear-gradient(135deg, #f8f9fa, #e9ecef);
                border-radius: 15px;
                padding: 20px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.1);
                border-left: 5px solid #28a745;
            }
            
            .filter-header {
                margin-bottom: 15px;
            }
            
            .filter-header h5 {
                color: #28a745;
                font-weight: 600;
                margin: 0;
            }
            
            .filter-header i {
                margin-right: 8px;
            }
            
            .filter-buttons {
                display: flex;
                gap: 15px;
                align-items: center;
            }
            
            .filter-buttons .btn {
                border-radius: 25px;
                font-weight: 600;
                padding: 10px 20px;
                transition: all 0.3s ease;
                border-width: 2px;
            }
            
            .filter-buttons .btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            }
            
            .filter-buttons .btn.active {
                background: #28a745;
                color: white;
                border-color: #28a745;
                box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
            }
            
            .filter-buttons .btn-outline-success {
                border-color: #28a745;
                color: #28a745;
            }
            
            .filter-buttons .btn-outline-success:hover:not(.active) {
                background: #28a745;
                color: white;
                border-color: #28a745;
            }
            
            .filter-buttons .btn-outline-primary {
                border-color: #007bff;
                color: #007bff;
            }
            
            .filter-buttons .btn-outline-primary:hover:not(.active) {
                background: #007bff;
                color: white;
                border-color: #007bff;
            }
            
            .filter-buttons .btn-outline-primary.active {
                background: #007bff;
                color: white;
                border-color: #007bff;
                box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
            }
            
            /* Responsive Design */
            @media (max-width: 768px) {
                .filter-buttons {
                    flex-direction: column;
                    align-items: stretch;
                }
                
                .filter-buttons .btn {
                    width: 100%;
                    margin-bottom: 10px;
                }
            }

            /* Create Event Modal Styling */
            .modal-header {
                background: linear-gradient(135deg, #28a745, #20c997);
                color: white;
                border-bottom: none;
            }
            
            .modal-header .btn-close {
                filter: invert(1);
            }
            
            .modal-body {
                padding: 2rem;
            }
            
            .form-label {
                font-weight: 600;
                color: #333;
                margin-bottom: 0.5rem;
            }
            
            .form-label i {
                color: #28a745;
                margin-right: 0.5rem;
            }
            
            .form-control:focus,
            .form-select:focus {
                border-color: #28a745;
                box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
            }
            
            .modal-footer {
                background: #f8f9fa;
                border-top: 1px solid #dee2e6;
            }
            
            .modal-footer .btn {
                padding: 0.5rem 1.5rem;
                border-radius: 25px;
                font-weight: 600;
            }

            .modal-footer .btn-success {
                background: linear-gradient(135deg, #28a745, #20c997);
                border: none;
            }

            .modal-footer .btn-success:hover {
                background: linear-gradient(135deg, #20c997, #1e7e34);
                transform: translateY(-1px);
                box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
            }

            /* Create Event Button Hover Effect */
            #create-event-container .btn:hover {
                background: linear-gradient(135deg, #20c997, #1e7e34) !important;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(40, 167, 69, 0.4);
            }
        </style>
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/events-frontend.js') }}"></script>
        <script>
            // JWT Authentication Check (same logic as show page)
            document.addEventListener('DOMContentLoaded', function() {
                checkAuthentication();
            });

            function checkAuthentication() {
                // Check for JWT token in localStorage or sessionStorage
                const token = getJWTToken();
                
                if (!token) {
                    return; // No token, organizer filter stays hidden
                }
                
                // Verify token with server
                fetch('/user', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Token invalid');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.user) {
                        showAuthenticatedFilters(data.user);
                    } else {
                        throw new Error('Invalid response format');
                    }
                })
                .catch(error => {
                    console.error('Authentication error:', error);
                    // Remove invalid token
                    clearJWTTokens();
                });
            }

            function getJWTToken() {
                // Check multiple possible token storage locations
                return localStorage.getItem('jwt_token') || 
                       sessionStorage.getItem('jwt_token') ||
                       localStorage.getItem('token') || 
                       sessionStorage.getItem('token') ||
                       localStorage.getItem('auth_token') || 
                       sessionStorage.getItem('auth_token');
            }

            function clearJWTTokens() {
                localStorage.removeItem('jwt_token');
                localStorage.removeItem('token');
                localStorage.removeItem('auth_token');
                sessionStorage.removeItem('jwt_token');
                sessionStorage.removeItem('token');
                sessionStorage.removeItem('auth_token');
            }

            function showAuthenticatedFilters(user) {
                // Show organizer features if user is organizer
                if (user.role === 'organizer') {
                    // Show create event button
                    const createEventContainer = document.getElementById('create-event-container');
                    if (createEventContainer) {
                        createEventContainer.style.display = 'block';
                    }
                    
                    // Show organizer filter
                    const organizerFilterContainer = document.getElementById('organizer-filter-container');
                    if (organizerFilterContainer) {
                        organizerFilterContainer.style.display = 'block';
                    }
                    
                    // Store user ID for filtering
                    localStorage.setItem('organizer_user_id', user.id);
                }
            }

            function filterOrganizerEvents(filterType) {
                const url = new URL(window.location);
                
                if (filterType === 'mine') {
                    const userId = localStorage.getItem('organizer_user_id');
                    if (!userId) {
                        alert('Vous devez être connecté pour voir vos événements.');
                        return;
                    }
                    
                    url.searchParams.set('organizer_filter', 'mine');
                    url.searchParams.set('organizer_id', userId);
                } else {
                    url.searchParams.delete('organizer_filter');
                    url.searchParams.delete('organizer_id');
                }
                
                window.location.href = url.toString();
            }

            // Create Event Modal Functions
            function openCreateModal() {
                console.log('openCreateModal function called');
                loadCategoriesForCreate();
                const modal = new bootstrap.Modal(document.getElementById('createEventModal'));
                modal.show();
            }

            function loadCategoriesForCreate() {
                const token = getJWTToken();
                fetch('/api/categories', {
                    method: 'GET',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    const categorySelect = document.getElementById('create_category_id');
                    categorySelect.innerHTML = '<option value="">Sélectionner une catégorie</option>';
                    
                    if (data.categories) {
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categorySelect.appendChild(option);
                        });
                    }
                })
                .catch(error => {
                    console.error('Error loading categories:', error);
                });
            }

            function submitCreateEvent() {
                console.log('submitCreateEvent function called');
                
                const token = getJWTToken();
                console.log('JWT Token:', !!token);
                
                if (!token) {
                    alert('Vous devez être connecté pour créer un événement.');
                    return;
                }

                const form = document.getElementById('createEventForm');
                const formData = new FormData(form);
                
                console.log('Form data prepared, submitting...');
                
                // Show loading state
                const createBtn = document.querySelector('[onclick="submitCreateEvent()"]');
                const originalText = createBtn.innerHTML;
                createBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Création...';
                createBtn.disabled = true;

                fetch('/organizer/events', {
                    method: 'POST',
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById('createEventModal'));
                        modal.hide();
                        
                        // Reset form
                        form.reset();
                        
                        window.location.reload();
                    } else {
                        alert(data.message || 'Erreur lors de la création de l\'événement.');
                    }
                })
                .catch(error => {
                    console.error('Create error:', error);
                    alert('Erreur lors de la création. Veuillez réessayer.');
                })
                .finally(() => {
                    // Restore button state
                    createBtn.innerHTML = originalText;
                    createBtn.disabled = false;
                });
            }
        </script>
    @endpush
@endsection