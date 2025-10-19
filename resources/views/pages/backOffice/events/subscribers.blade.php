@extends('layouts.admin')

@section('title', 'Abonnés de l\'Événement - Echofy')

@section('content')
    <div class="content-header">
        <h1 class="page-title">Abonnés de l'Événement</h1>
        <nav class="breadcrumb-nav">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.index') }}">Événements</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.events.show', $event->id) }}">{{ $event->title }}</a></li>
                <li class="breadcrumb-item active">Abonnés</li>
            </ol>
        </nav>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="card-title">
                        <i class="fas fa-users"></i> Abonnés pour "{{ $event->title }}"
                    </h6>
                    <div class="event-info">
                        <span class="badge badge-primary">{{ $subscribers->total() }} abonné(s)</span>
                        <span class="badge badge-info">{{ $event->status }}</span>
                    </div>
                </div>
                <div class="card-body">
                    {{-- Search and Filter --}}
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.events.subscribers', $event->id) }}" class="search-form">
                                <div class="input-group">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Rechercher par nom ou email..."
                                           value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('admin.events.subscribers', $event->id) }}" class="btn btn-outline-secondary">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            <a href="{{ route('admin.events.show', $event->id) }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour à l'événement
                            </a>
                        </div>
                    </div>

                    @if($subscribers->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Nom</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Compétences</th>
                                        <th>Transport</th>
                                        <th>Inscrit le</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($subscribers as $index => $registration)
                                        <tr>
                                            <td>{{ $subscribers->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-circle mr-2">
                                                        {{ strtoupper(substr($registration->user->name, 0, 1)) }}
                                                    </div>
                                                    <strong>{{ $registration->user->name }}</strong>
                                                </div>
                                            </td>
                                            <td>{{ $registration->user->email }}</td>
                                            <td>
                                                <span class="badge badge-info">{{ $registration->role ?? 'N/A' }}</span>
                                            </td>
                                            <td>
                                                <span class="text-muted">{{ Str::limit($registration->skills ?? 'N/A', 30) }}</span>
                                            </td>
                                            <td>
                                                @if($registration->has_transportation)
                                                    <span class="badge badge-success">
                                                        <i class="fas fa-car"></i> Oui
                                                    </span>
                                                @else
                                                    <span class="badge badge-warning">
                                                        <i class="fas fa-walking"></i> Non
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    {{ $registration->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <button type="button" class="btn btn-sm btn-outline-primary" 
                                                            data-toggle="modal" 
                                                            data-target="#subscriberModal{{ $registration->id }}">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger" 
                                                            onclick="confirmRemoveSubscriber({{ $registration->id }}, '{{ $registration->user->name }}')">
                                                        <i class="fas fa-user-times"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination --}}
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div class="pagination-info">
                                <span class="text-muted">
                                    Affichage de {{ $subscribers->firstItem() ?? 0 }} à {{ $subscribers->lastItem() ?? 0 }} 
                                    sur {{ $subscribers->total() }} abonné(s)
                                </span>
                            </div>
                            <div class="pagination-links">
                                {{ $subscribers->appends(request()->query())->links() }}
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <div class="no-subscribers-icon mb-3">
                                <i class="fas fa-users-slash fa-3x text-muted"></i>
                            </div>
                            <h4>Aucun abonné trouvé</h4>
                            <p class="text-muted">
                                @if(request('search'))
                                    Aucun abonné ne correspond à votre recherche.
                                @else
                                    Cet événement n'a pas encore d'abonnés.
                                @endif
                            </p>
                            @if(request('search'))
                                <a href="{{ route('admin.events.subscribers', $event->id) }}" class="btn btn-primary">
                                    <i class="fas fa-times"></i> Effacer la recherche
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Subscriber Detail Modals --}}
    @foreach($subscribers as $registration)
        <div class="modal fade" id="subscriberModal{{ $registration->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-user"></i> Détails de l'abonné
                        </h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="text-center">
                                    <div class="avatar-circle-large mx-auto mb-3">
                                        {{ strtoupper(substr($registration->user->name, 0, 1)) }}
                                    </div>
                                    <h5>{{ $registration->user->name }}</h5>
                                    <p class="text-muted">{{ $registration->user->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h6>Informations d'inscription</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Rôle :</strong></td>
                                        <td>{{ $registration->role ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Compétences :</strong></td>
                                        <td>{{ $registration->skills ?? 'Non spécifiées' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Transport :</strong></td>
                                        <td>
                                            @if($registration->has_transportation)
                                                <span class="badge badge-success">Oui</span>
                                            @else
                                                <span class="badge badge-warning">Non</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Participation antérieure :</strong></td>
                                        <td>
                                            @if($registration->has_participated_before)
                                                <span class="badge badge-info">Oui</span>
                                            @else
                                                <span class="badge badge-secondary">Non</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>Contact d'urgence :</strong></td>
                                        <td>{{ $registration->emergency_contact ?? 'Non spécifié' }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Inscrit le :</strong></td>
                                        <td>{{ $registration->created_at->format('d/m/Y à H:i') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                        <button type="button" class="btn btn-danger" 
                                onclick="confirmRemoveSubscriber({{ $registration->id }}, '{{ $registration->user->name }}')">
                            <i class="fas fa-user-times"></i> Retirer l'abonnement
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    {{-- Remove Subscriber Confirmation Modal --}}
    <div class="modal fade" id="removeSubscriberModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle text-warning"></i> Confirmer la suppression
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Êtes-vous sûr de vouloir retirer l'abonnement de <strong id="subscriberName"></strong> ?</p>
                    <p class="text-muted">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger" id="confirmRemoveBtn">
                        <i class="fas fa-user-times"></i> Retirer l'abonnement
                    </button>
                </div>
            </div>
        </div>
    </div>

@push('styles')
<style>
    .avatar-circle {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 14px;
    }

    .avatar-circle-large {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        font-size: 32px;
    }

    .event-info {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .search-form .input-group {
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    .search-form .form-control {
        border: none;
        padding: 12px 15px;
    }

    .search-form .form-control:focus {
        box-shadow: none;
        border: none;
    }

    .search-form .btn {
        border-radius: 0 25px 25px 0;
    }

    .table th {
        background-color: #f8f9fa;
        border-top: none;
        font-weight: 600;
        color: #495057;
    }

    .badge {
        font-size: 0.75em;
        padding: 0.4em 0.8em;
    }

    .no-subscribers-icon {
        opacity: 0.5;
    }

    .pagination-info {
        font-size: 0.9rem;
    }

    .pagination-links .pagination {
        margin: 0;
    }

    .pagination-links .page-link {
        border-radius: 8px;
        margin: 0 2px;
        border: 2px solid #e9ecef;
        color: #28a745;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .pagination-links .page-link:hover {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
        border-color: #28a745;
        transform: translateY(-2px);
    }

    .pagination-links .page-item.active .page-link {
        background: linear-gradient(135deg, #28a745, #20c997);
        border-color: #28a745;
        color: white;
    }
</style>
@endpush

@push('scripts')
<script>
    let subscriberToRemove = null;

    function confirmRemoveSubscriber(registrationId, subscriberName) {
        subscriberToRemove = registrationId;
        document.getElementById('subscriberName').textContent = subscriberName;
        $('#removeSubscriberModal').modal('show');
    }

    document.getElementById('confirmRemoveBtn').addEventListener('click', function() {
        if (subscriberToRemove) {
            removeSubscriber(subscriberToRemove);
        }
    });

    function removeSubscriber(registrationId) {
        // Show loading state
        const btn = document.getElementById('confirmRemoveBtn');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Suppression...';
        btn.disabled = true;

        fetch(`/events/${registrationId}/unsubscribe`, {
            method: 'DELETE',
            headers: {
                'Authorization': `Bearer ${localStorage.getItem('jwt_token')}`,
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showToast('success', 'Abonnement retiré avec succès !');
                // Reload page after a short delay
                setTimeout(() => {
                    window.location.reload();
                }, 1500);
            } else {
                showToast('error', data.message || 'Erreur lors de la suppression de l\'abonnement.');
                // Restore button
                btn.innerHTML = originalText;
                btn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Remove subscriber error:', error);
            showToast('error', 'Erreur lors de la suppression de l\'abonnement.');
            // Restore button
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
    }

    // Toast notification function
    function showToast(type, message) {
        // Create toast container if it doesn't exist
        let toastContainer = document.getElementById('toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.id = 'toast-container';
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }
        
        // Create toast element
        const toastId = 'toast-' + Date.now();
        const iconClass = type === 'success' ? 'fas fa-check-circle text-success' : 'fas fa-exclamation-circle text-danger';
        const bgClass = type === 'success' ? 'bg-success' : 'bg-danger';
        
        const toastElement = document.createElement('div');
        toastElement.id = toastId;
        toastElement.className = `toast ${bgClass} text-white`;
        toastElement.setAttribute('role', 'alert');
        toastElement.innerHTML = `
            <div class="toast-header ${bgClass} text-white border-0">
                <i class="${iconClass} me-2"></i>
                <strong class="me-auto">${type === 'success' ? 'Succès' : 'Erreur'}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        toastContainer.appendChild(toastElement);
        
        // Show toast
        const toast = new bootstrap.Toast(toastElement, {
            autohide: true,
            delay: 5000
        });
        toast.show();
        
        // Remove element after hiding
        toastElement.addEventListener('hidden.bs.toast', function() {
            if (toastContainer.contains(toastElement)) {
                toastContainer.removeChild(toastElement);
            }
        });
    }
</script>
@endpush
@endsection
