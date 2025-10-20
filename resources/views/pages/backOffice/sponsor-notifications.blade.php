@extends('layouts.sponsor')

@section('title', 'Notifications')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-bell"></i> Mes Notifications
                    </h3>
                    <div>
                        <button class="btn btn-sm btn-outline-primary" onclick="markAllAsRead()">
                            <i class="fas fa-check-double"></i> Tout marquer comme lu
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="loadPreferences()">
                            <i class="fas fa-cog"></i> Préférences
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <select class="form-control" id="statusFilter" onchange="loadNotifications()">
                                <option value="">Tous les statuts</option>
                                <option value="unread">Non lues</option>
                                <option value="read">Lues</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <select class="form-control" id="typeFilter" onchange="loadNotifications()">
                                <option value="">Tous les types</option>
                                <option value="email">Email</option>
                                <option value="sms">SMS</option>
                                <option value="push">Push</option>
                                <option value="in_app">In-App</option>
                            </select>
                        </div>
                    </div>

                    <!-- Liste des notifications -->
                    <div id="notificationsList">
                        <div class="text-center">
                            <div class="spinner-border" role="status">
                                <span class="sr-only">Chargement...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Pagination -->
                    <div id="pagination" class="mt-3"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal des préférences -->
<div class="modal fade" id="preferencesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Préférences de Notifications</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="preferencesContent">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="sr-only">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary" onclick="savePreferences()">Sauvegarder</button>
            </div>
        </div>
    </div>
</div>

<script>
let currentPage = 1;
let preferences = {};

// Charger les notifications
async function loadNotifications(page = 1) {
    try {
        const token = localStorage.getItem('jwt_token');
        const statusFilter = document.getElementById('statusFilter').value;
        const typeFilter = document.getElementById('typeFilter').value;
        
        let url = `/api/sponsor/notifications?page=${page}`;
        if (statusFilter) url += `&status=${statusFilter}`;
        if (typeFilter) url += `&type=${typeFilter}`;

        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            displayNotifications(data.notifications.data);
            displayPagination(data.notifications);
        }
    } catch (error) {
        console.error('Erreur lors du chargement des notifications:', error);
    }
}

// Afficher les notifications
function displayNotifications(notifications) {
    const container = document.getElementById('notificationsList');
    
    if (notifications.length === 0) {
        container.innerHTML = `
            <div class="text-center py-4">
                <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                <p class="text-muted">Aucune notification trouvée</p>
            </div>
        `;
        return;
    }

    let html = '';
    notifications.forEach(notification => {
        const isUnread = notification.status === 'sent';
        const iconClass = getNotificationIcon(notification.type);
        const timeAgo = new Date(notification.created_at).toLocaleString('fr-FR');
        
        html += `
            <div class="notification-item ${isUnread ? 'unread' : ''}" onclick="markAsRead(${notification.id})">
                <div class="d-flex align-items-start">
                    <div class="notification-icon mr-3">
                        <i class="${iconClass}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="mb-1 ${isUnread ? 'font-weight-bold' : ''}">${notification.subject || 'Notification'}</h6>
                            <small class="text-muted">${timeAgo}</small>
                        </div>
                        <p class="mb-1">${notification.content}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted">
                                <i class="fas fa-tag"></i> ${notification.trigger_event}
                                <span class="badge badge-${getStatusColor(notification.status)} ml-2">${notification.status}</span>
                            </small>
                            <button class="btn btn-sm btn-outline-danger" onclick="deleteNotification(${notification.id}, event)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        `;
    });

    container.innerHTML = html;
}

// Obtenir l'icône selon le type
function getNotificationIcon(type) {
    const icons = {
        'email': 'fas fa-envelope text-primary',
        'sms': 'fas fa-sms text-success',
        'push': 'fas fa-mobile-alt text-info',
        'in_app': 'fas fa-bell text-warning'
    };
    return icons[type] || 'fas fa-bell';
}

// Obtenir la couleur du statut
function getStatusColor(status) {
    const colors = {
        'pending': 'secondary',
        'sent': 'primary',
        'delivered': 'success',
        'failed': 'danger',
        'read': 'light'
    };
    return colors[status] || 'secondary';
}

// Afficher la pagination
function displayPagination(paginationData) {
    const container = document.getElementById('pagination');
    
    if (paginationData.last_page <= 1) {
        container.innerHTML = '';
        return;
    }

    let html = '<nav><ul class="pagination justify-content-center">';
    
    // Page précédente
    if (paginationData.current_page > 1) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadNotifications(${paginationData.current_page - 1})">Précédent</a></li>`;
    }
    
    // Pages
    for (let i = 1; i <= paginationData.last_page; i++) {
        const activeClass = i === paginationData.current_page ? 'active' : '';
        html += `<li class="page-item ${activeClass}"><a class="page-link" href="#" onclick="loadNotifications(${i})">${i}</a></li>`;
    }
    
    // Page suivante
    if (paginationData.current_page < paginationData.last_page) {
        html += `<li class="page-item"><a class="page-link" href="#" onclick="loadNotifications(${paginationData.current_page + 1})">Suivant</a></li>`;
    }
    
    html += '</ul></nav>';
    container.innerHTML = html;
}

// Marquer comme lu
async function markAsRead(id) {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch(`/api/sponsor/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            loadNotifications(currentPage);
        }
    } catch (error) {
        console.error('Erreur lors du marquage comme lu:', error);
    }
}

// Marquer tout comme lu
async function markAllAsRead() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            loadNotifications(currentPage);
            showAlert('Toutes les notifications ont été marquées comme lues', 'success');
        }
    } catch (error) {
        console.error('Erreur lors du marquage comme lu:', error);
    }
}

// Supprimer une notification
async function deleteNotification(id, event) {
    event.stopPropagation();
    
    if (!confirm('Êtes-vous sûr de vouloir supprimer cette notification ?')) {
        return;
    }

    try {
        const token = localStorage.getItem('jwt_token');
        
        const response = await fetch(`/api/sponsor/notifications/${id}`, {
            method: 'DELETE',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'Content-Type': 'application/json',
            }
        });

        if (response.ok) {
            loadNotifications(currentPage);
            showAlert('Notification supprimée', 'success');
        }
    } catch (error) {
        console.error('Erreur lors de la suppression:', error);
    }
}

// Charger les préférences
async function loadPreferences() {
    try {
        const token = localStorage.getItem('jwt_token');
        const response = await fetch('/api/sponsor/notifications/preferences', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
            }
        });

        if (response.ok) {
            const data = await response.json();
            preferences = data.preferences;
            displayPreferences(data.preferences, data.available_types);
            $('#preferencesModal').modal('show');
        }
    } catch (error) {
        console.error('Erreur lors du chargement des préférences:', error);
    }
}

// Afficher les préférences
function displayPreferences(preferences, availableTypes) {
    const container = document.getElementById('preferencesContent');
    
    let html = '';
    
    Object.entries(availableTypes).forEach(([key, label]) => {
        const pref = preferences.find(p => p.notification_type === key) || {
            email_enabled: true,
            sms_enabled: false,
            push_enabled: true,
            in_app_enabled: true
        };
        
        html += `
            <div class="preference-group mb-4">
                <h6>${label}</h6>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="${key}_email" ${pref.email_enabled ? 'checked' : ''}>
                            <label class="form-check-label" for="${key}_email">
                                <i class="fas fa-envelope"></i> Email
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="${key}_sms" ${pref.sms_enabled ? 'checked' : ''}>
                            <label class="form-check-label" for="${key}_sms">
                                <i class="fas fa-sms"></i> SMS
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="${key}_push" ${pref.push_enabled ? 'checked' : ''}>
                            <label class="form-check-label" for="${key}_push">
                                <i class="fas fa-mobile-alt"></i> Push
                            </label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="${key}_in_app" ${pref.in_app_enabled ? 'checked' : ''}>
                            <label class="form-check-label" for="${key}_in_app">
                                <i class="fas fa-bell"></i> In-App
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

// Sauvegarder les préférences
async function savePreferences() {
    try {
        const token = localStorage.getItem('jwt_token');
        const preferencesData = [];
        
        // Collecter les données des checkboxes
        document.querySelectorAll('.preference-group').forEach(group => {
            const h6 = group.querySelector('h6');
            const typeKey = Object.keys(preferences).find(key => 
                preferences[key].notification_type === h6.textContent
            );
            
            if (typeKey) {
                preferencesData.push({
                    notification_type: preferences[typeKey].notification_type,
                    email_enabled: group.querySelector(`#${preferences[typeKey].notification_type}_email`).checked,
                    sms_enabled: group.querySelector(`#${preferences[typeKey].notification_type}_sms`).checked,
                    push_enabled: group.querySelector(`#${preferences[typeKey].notification_type}_push`).checked,
                    in_app_enabled: group.querySelector(`#${preferences[typeKey].notification_type}_in_app`).checked
                });
            }
        });

        const response = await fetch('/api/sponsor/notifications/preferences', {
            method: 'PUT',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'Authorization': 'Bearer ' + token,
            },
            body: JSON.stringify({ preferences: preferencesData })
        });

        if (response.ok) {
            $('#preferencesModal').modal('hide');
            showAlert('Préférences sauvegardées avec succès', 'success');
        }
    } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error);
    }
}

// Afficher une alerte
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">
            <span>&times;</span>
        </button>
    `;
    
    document.body.insertBefore(alertDiv, document.body.firstChild);
    
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

// Charger les notifications au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    loadNotifications();
});
</script>

<style>
.notification-item {
    padding: 15px;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.notification-item:hover {
    background-color: #f8f9fa;
}

.notification-item.unread {
    background-color: #e3f2fd;
    border-left: 4px solid #2196f3;
}

.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #f8f9fa;
    display: flex;
    align-items: center;
    justify-content: center;
}

.preference-group {
    border: 1px solid #dee2e6;
    border-radius: 8px;
    padding: 15px;
}
</style>
@endsection
