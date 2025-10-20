// Script pour les notifications en temps réel
class NotificationManager {
    constructor() {
        this.init();
    }

    init() {
        this.loadNotificationCount();
        this.setupPolling();
    }

    async loadNotificationCount() {
        try {
            const token = localStorage.getItem('jwt_token');
            if (!token) return;

            const response = await fetch('/api/sponsor/notifications/unread', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.updateNotificationBadge(data.unread_count);
            }
        } catch (error) {
            console.error('Erreur lors du chargement du compteur de notifications:', error);
        }
    }

    updateNotificationBadge(count) {
        // Mettre à jour le badge dans le sidebar
        const sidebarBadge = document.getElementById('notification-count');
        if (sidebarBadge) {
            if (count > 0) {
                sidebarBadge.textContent = count;
                sidebarBadge.style.display = 'inline-block';
            } else {
                sidebarBadge.style.display = 'none';
            }
        }
        
        // Mettre à jour le badge dans la navbar
        const navbarBadge = document.getElementById('navbar-notification-count');
        const navbarBtn = document.getElementById('navbar-notification-btn');
        
        if (navbarBadge) {
            if (count > 0) {
                navbarBadge.textContent = count;
                navbarBadge.style.display = 'inline-block';
                
                // Ajouter l'animation si c'est la première notification
                if (navbarBtn && !navbarBtn.classList.contains('has-notifications')) {
                    navbarBtn.classList.add('has-notifications');
                    
                    // Supprimer l'animation après 10 secondes
                    setTimeout(() => {
                        navbarBtn.classList.remove('has-notifications');
                    }, 10000);
                }
            } else {
                navbarBadge.style.display = 'none';
                if (navbarBtn) {
                    navbarBtn.classList.remove('has-notifications');
                }
            }
        }
    }

    setupPolling() {
        // Vérifier les nouvelles notifications toutes les 30 secondes
        setInterval(() => {
            this.loadNotificationCount();
        }, 30000);
    }

    // Méthode pour envoyer une notification de test
    async sendTestNotification() {
        try {
            const token = localStorage.getItem('jwt_token');
            const response = await fetch('/api/sponsor/notifications/test', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                }
            });

            if (response.ok) {
                this.loadNotificationCount();
                this.showToast('Notification de test envoyée !', 'success');
            }
        } catch (error) {
            console.error('Erreur lors de l\'envoi de la notification de test:', error);
        }
    }

    showToast(message, type = 'info') {
        // Créer un toast simple
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            ${message}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        `;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }
}

// Initialiser le gestionnaire de notifications
document.addEventListener('DOMContentLoaded', function() {
    window.notificationManager = new NotificationManager();
    
    // Initialiser les tooltips Bootstrap
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl, {
                placement: 'bottom',
                trigger: 'hover'
            });
        });
    }
});

// Fonction globale pour envoyer une notification de test (pour les tests)
function sendTestNotification() {
    if (window.notificationManager) {
        window.notificationManager.sendTestNotification();
    }
}

// Fonction pour créer une notification de test depuis la navbar
function createTestNotification() {
    if (window.notificationManager) {
        // Simuler une nouvelle notification
        const currentCount = parseInt(document.getElementById('navbar-notification-count').textContent) || 0;
        window.notificationManager.updateNotificationBadge(currentCount + 1);
        
        // Afficher un toast
        window.notificationManager.showToast('Nouvelle notification de test créée !', 'success');
    }
}
