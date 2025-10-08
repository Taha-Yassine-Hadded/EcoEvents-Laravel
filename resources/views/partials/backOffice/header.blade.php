<!-- Admin Header -->
<header class="admin-header">
    <div class="header-content">
        <!-- Left side - Logo and Menu Toggle -->
        <div class="header-left">
            <div class="sidebar-toggle">
                <i class="fas fa-bars"></i>
            </div>
            <div class="admin-logo">
                <h3>Echofy Admin</h3>
            </div>
        </div>

        <!-- Right side - Search, Notifications, Profile -->
        <div class="header-right">
            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" placeholder="Search..." class="search-input">
                <i class="fas fa-search search-icon"></i>
            </div>

            <!-- Notifications -->
            <div class="notification-dropdown">
                <div class="notification-icon">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="notification-dropdown-content">
                    <div class="notification-header">
                        <h4>Notifications</h4>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-user text-primary"></i>
                        <div>
                            <p>New user registered</p>
                            <span>2 minutes ago</span>
                        </div>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-shopping-cart text-success"></i>
                        <div>
                            <p>New order received</p>
                            <span>5 minutes ago</span>
                        </div>
                    </div>
                    <div class="notification-item">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        <div>
                            <p>Server maintenance required</p>
                            <span>1 hour ago</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="user-dropdown">
                <div class="user-info">
                    <img src="{{ Vite::asset('resources/assets/images/fav-icon/icon.png') }}" alt="User" class="user-avatar">
                    <span class="user-name" id="user-fullname">Loading...</span>
                    <i class="fas fa-chevron-down"></i>
                </div>
                <div class="user-dropdown-content">
                    <a href="/" class="dropdown-item">
                        <i class="fas fa-home"></i>
                        Home
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        Profile
                    </a>
                    <a href="#" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        Settings
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" class="dropdown-item" id="logout-link">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </div>
</header>

<style>
    .admin-header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        height: 70px;
        background: #fff;
        border-bottom: 1px solid #e3e6f0;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        height: 100%;
        padding: 0 20px;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .sidebar-toggle {
        cursor: pointer;
        padding: 10px;
        border-radius: 5px;
        transition: background 0.3s;
    }

    .sidebar-toggle:hover {
        background: #f8f9fc;
    }

    .sidebar-toggle i {
        font-size: 18px;
        color: #5a5c69;
    }

    .admin-logo h3 {
        margin: 0;
        color: #5a5c69;
        font-weight: 600;
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .search-container {
        position: relative;
    }

    .search-input {
        padding: 8px 15px 8px 40px;
        border: 1px solid #d1d3e2;
        border-radius: 20px;
        width: 250px;
        outline: none;
        transition: all 0.3s;
    }

    .search-input:focus {
        border-color: #4e73df;
        box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
    }

    .search-icon {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #858796;
    }

    .notification-dropdown, .user-dropdown {
        position: relative;
    }

    .notification-icon, .user-info {
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 12px;
        border-radius: 5px;
        transition: background 0.3s;
    }

    .notification-icon:hover, .user-info:hover {
        background: #f8f9fc;
    }

    .notification-icon {
        position: relative;
    }

    .notification-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #e74a3b;
        color: white;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        font-size: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .user-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        object-fit: cover;
    }

    .user-name {
        color: #5a5c69;
        font-weight: 500;
    }

    .notification-dropdown-content, .user-dropdown-content {
        position: absolute;
        top: 100%;
        right: 0;
        background: white;
        border: 1px solid #e3e6f0;
        border-radius: 8px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        min-width: 280px;
        display: none;
        z-index: 1001;
    }

    .notification-dropdown:hover .notification-dropdown-content,
    .user-dropdown:hover .user-dropdown-content {
        display: block;
    }

    .notification-header {
        padding: 15px 20px;
        border-bottom: 1px solid #e3e6f0;
    }

    .notification-header h4 {
        margin: 0;
        color: #5a5c69;
    }

    .notification-item {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 15px 20px;
        border-bottom: 1px solid #f8f9fc;
        transition: background 0.3s;
    }

    .notification-item:hover {
        background: #f8f9fc;
    }

    .notification-item:last-child {
        border-bottom: none;
    }

    .notification-item i {
        width: 20px;
        text-align: center;
    }

    .notification-item p {
        margin: 0;
        color: #5a5c69;
        font-weight: 500;
    }

    .notification-item span {
        font-size: 12px;
        color: #858796;
    }

    .dropdown-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #5a5c69;
        text-decoration: none;
        transition: background 0.3s;
    }

    .dropdown-item:hover {
        background: #f8f9fc;
        color: #5a5c69;
    }

    .dropdown-divider {
        height: 1px;
        background: #e3e6f0;
        margin: 8px 0;
    }

    @media (max-width: 768px) {
        .search-container {
            display: none;
        }

        .search-input {
            width: 200px;
        }

        .user-name {
            display: none;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Récupérer le token depuis localStorage
        const token = localStorage.getItem('jwt_token');
        const userFullName = document.getElementById('user-fullname');
        const logoutLink = document.getElementById('logout-link');

        // Récupérer les informations de l'utilisateur via /user
        if (token) {
            fetch('{{ route("user.get") }}', {
                method: 'GET',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'Accept': 'application/json',
                },
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erreur lors de la récupération des informations utilisateur');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success && data.user) {
                        userFullName.textContent = data.user.name || 'Utilisateur';
                    } else {
                        userFullName.textContent = 'Utilisateur inconnu';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    userFullName.textContent = 'Erreur de chargement';
                    window.location.href = '{{ route("login") }}';
                });
        } else {
            userFullName.textContent = 'Non connecté';
            window.location.href = '{{ route("login") }}';
        }

        // Gérer la déconnexion
        if (logoutLink) {
            logoutLink.addEventListener('click', function(event) {
                event.preventDefault();
                if (token) {
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${token}`,
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        },
                    })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                localStorage.removeItem('jwt_token');
                                window.location.href = '{{ route("login") }}';
                            } else {
                                alert(data.error || 'Erreur lors de la déconnexion');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur:', error);
                            alert('Une erreur est survenue lors de la déconnexion');
                        });
                } else {
                    window.location.href = '{{ route("login") }}';
                }
            });
        }
    });
</script>