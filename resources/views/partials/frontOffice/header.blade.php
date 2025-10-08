<!--==================================================-->
<!-- Start Echofy Header Area -->
<!--==================================================-->
<div class="header-area home-six" id="sticky-header">
    <div class="container">
        <div class="row add-bg align-items-center">
            <div class="col-lg-3">
                <div class="header-logo">
                    <a href="{{ url('/') }}">
                        <img src="{{ asset('assets/images/home6/logo.png') }}" alt="logo">
                    </a>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="header-menu">
                    <ul>
                        <li><a href="{{ url('/') }}">Accueil</a></li>
                        <li><a href="{{ route('communities.index') }}">Communautés</a></li>
                        <li><a href="{{ url('/events') }}">Événements</a></li>
                        <li><a href="{{ url('/campaigns?search=&category=all&status=all') }}">Nos Campagnes</a></li>
                        <li><a href="{{ url('/contact') }}">Contact</a></li>
                    </ul>
                    <div class="header-secrch-icon search-box-outer">
                        <a href="#"><i class="bi bi-search"></i></a>
                    </div>
                    <div class="header-button" id="auth-area">
                        <!-- Boutons pour utilisateur non connecté -->
                        <div class="auth-buttons" id="auth-buttons">
                            <a href="{{ route('login') }}" class="btn-login">Login</a>
                            <a href="{{ route('register') }}" class="btn-register">S'inscrire</a>
                        </div>
                        <!-- Avatar pour utilisateur connecté (caché par défaut) -->
                        <div class="user-avatar" id="user-avatar" style="display: none;">
                            <div class="avatar-circle">
                                <span class="initials" id="user-initials"></span>
                                <img id="user-image" src="" alt="User Avatar" style="display: none;">
                            </div>
                            <div class="dropdown-menu" id="user-dropdown">
                                <div class="dropdown-arrow"></div>
                                <a href="#" class="profile-link">
                                    <i class="bi bi-person"></i>
                                    Profile
                                </a>
                                <a href="{{ route('organizer.communities.index') }}" class="organizer-link" id="organizer-link" style="display: none;">
                                    <i class="bi bi-people"></i>
                                    Mes Communautés
                                </a>
                                <a href="#" id="theme-toggle" class="theme-toggle">
                                    <i class="bi bi-moon"></i>
                                    <span class="theme-text">Mode sombre</span>
                                </a>
                                <div class="dropdown-divider"></div>
                                <a href="#" id="logout-button" class="logout-btn">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Logout
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!--==================================================-->
<!-- End Echofy Header Area -->
<!--==================================================-->

<!--==================================================-->
<!-- Start Mobile Menu Area -->
<!--==================================================-->
<div class="mobile-menu-area sticky d-sm-block d-md-block d-lg-none">
    <div class="mobile-menu">
        <nav class="header-menu">
            <ul class="nav_scroll">
                <li class="menu-item-has-children">
                    <a href="#">Home</a>
                    <ul class="sub-menu">
                        <li><a href="{{ url('/') }}">Home 1</a></li>
                        <li><a href="{{ url('/home2') }}">Home 2</a></li>
                        <li><a href="{{ url('/home3') }}">Home 3</a></li>
                        <li><a href="{{ url('/home4') }}">Home 4</a></li>
                        <li><a href="{{ url('/home5') }}">Home 5</a></li>
                        <li><a href="{{ url('/home6') }}">Home 6</a></li>
                        <li><a href="{{ url('/home7') }}">Home 7</a></li>
                    </ul>
                </li>
                <li><a href="{{ url('/about') }}">About</a></li>

                <li><a href="{{ route('communities.index') }}">Communautés</a></li>
                <li><a href="{{ url('/blog') }}">Blog</a></li>

                <li><a href="{{ url('/campaigns?search=&category=all&status=all') }}">Nos Campagnes</a></li>

                <li><a href="{{ url('/contact') }}">Contact</a></li>
                <li id="mobile-auth-area">
                    <div class="mobile-auth-buttons" id="mobile-auth-buttons">
                        <a href="{{ route('login') }}" class="btn-login">Login</a>
                        <a href="{{ route('register') }}" class="btn-register">S'inscrire</a>
                    </div>
                    <div class="user-avatar" id="mobile-user-avatar" style="display: none;">
                        <div class="avatar-circle">
                            <span class="initials" id="mobile-user-initials"></span>
                            <img id="mobile-user-image" src="" alt="User Avatar" style="display: none;">
                        </div>
                        <div class="dropdown-menu" id="mobile-user-dropdown">
                            <a href="#" class="profile-link">
                                <i class="bi bi-person"></i>
                                Profile
                            </a>
                            <a href="{{ route('organizer.communities.index') }}" class="organizer-link" id="mobile-organizer-link" style="display: none;">
                                <i class="bi bi-people"></i>
                                Mes Communautés
                            </a>
                            <a href="#" id="mobile-theme-toggle" class="theme-toggle">
                                <i class="bi bi-moon"></i>
                                <span class="theme-text">Mode sombre</span>
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="#" id="mobile-logout-button" class="logout-btn">
                                <i class="bi bi-box-arrow-right"></i>
                                Logout
                            </a>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>
<!--==================================================-->
<!-- End Mobile Menu Area -->
<!--==================================================-->

<style>
    .header-menu {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-menu ul {
        display: flex;
        align-items: center;
        margin: 0;
        padding: 0;
        list-style: none;
        gap: 30px;
    }

    .header-button {
        display: flex;
        align-items: center;
        height: 50px;
    }

    /* Styles pour les boutons d'authentification */
    .auth-buttons {
        display: flex;
        gap: 15px;
        align-items: center;
    }

    .btn-login {
        padding: 10px 20px;
        border: 2px solid #667eea;
        border-radius: 25px;
        color: #667eea;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-login:hover {
        background: #667eea;
        color: white;
        transform: translateY(-2px);
    }

    .btn-register {
        padding: 10px 20px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 4px; /* Coins carrés comme demandé */
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .btn-register:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    /* Avatar styles */
    .user-avatar {
        position: relative;
        display: inline-flex;
        align-items: center;
        height: 100%;
    }

    .avatar-circle {
        width: 45px;
        height: 45px;
        border-radius: 50%; /* Parfaitement circulaire */
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: 2px solid rgba(255, 255, 255, 0.3);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        overflow: hidden;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
    }

    .avatar-circle:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.25);
        border-color: rgba(255, 255, 255, 0.5);
    }

    .avatar-circle img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .avatar-circle .initials {
        font-size: 16px;
        font-weight: 600;
        color: #ffffff;
        text-transform: uppercase;
        letter-spacing: 1px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }

    /* Dropdown menu styles */
    .dropdown-menu {
        position: absolute;
        top: 55px;
        right: 0;
        min-width: 200px;
        background: #ffffff;
        border-radius: 12px;
        padding: 8px 0;
        z-index: 1000;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        border: 1px solid rgba(0, 0, 0, 0.05);
        opacity: 0;
        visibility: hidden;
        transform: translateY(-10px);
        transition: all 0.3s ease;
    }

    .dropdown-menu.show {
        opacity: 1;
        visibility: visible;
        transform: translateY(0);
    }

    .dropdown-arrow {
        position: absolute;
        top: -8px;
        right: 15px;
        width: 16px;
        height: 16px;
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-bottom: none;
        border-right: none;
        transform: rotate(45deg);
    }

    .dropdown-menu a {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 20px;
        color: #333333;
        text-decoration: none;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
        border: none;
        background: none;
    }

    .dropdown-menu a:hover {
        background: #f8f9fa;
        color: #667eea;
    }

    .dropdown-menu a i {
        font-size: 16px;
        width: 16px;
        text-align: center;
    }

    .dropdown-divider {
        height: 1px;
        background: #e9ecef;
        margin: 8px 0;
    }

    .logout-btn {
        color: #dc3545 !important;
    }

    .logout-btn:hover {
        background: #fff5f5 !important;
        color: #dc3545 !important;
    }

    .theme-toggle i {
        transition: all 0.3s ease;
    }

    .theme-toggle:hover i {
        transform: scale(1.1);
    }

    /* Dark theme styles */
    body.dark-theme {
        background-color: #1a1a1a;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    body.dark-theme .dropdown-menu {
        background: #2d2d2d;
        border-color: #404040;
    }

    body.dark-theme .dropdown-arrow {
        background: #2d2d2d;
        border-color: #404040;
    }

    body.dark-theme .dropdown-menu a {
        color: #ffffff;
    }

    body.dark-theme .dropdown-menu a:hover {
        background: #404040;
        color: #667eea;
    }

    body.dark-theme .dropdown-divider {
        background: #404040;
    }

    body.dark-theme .logout-btn:hover {
        background: #4a2626 !important;
    }

    /* Mobile styles */
    @media (max-width: 991px) {
        .user-avatar {
            margin-left: 15px;
        }

        .dropdown-menu {
            right: -10px;
            left: auto;
        }

        .mobile-auth-buttons {
            display: flex;
            gap: 10px;
            padding: 10px 0;
        }

        .mobile-auth-buttons .btn-login,
        .mobile-auth-buttons .btn-register {
            padding: 8px 16px;
            font-size: 14px;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const token = localStorage.getItem('jwt_token');
        console.log('Token JWT:', token);

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
                        return response.json().then(errorData => {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }).catch(() => {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Données reçues:', data);
                    if (data.success) {
                        // Masquer les boutons d'auth et afficher l'avatar
                        document.getElementById('auth-buttons').style.display = 'none';
                        document.getElementById('mobile-auth-buttons').style.display = 'none';
                        document.getElementById('user-avatar').style.display = 'inline-flex';
                        document.getElementById('mobile-user-avatar').style.display = 'block';

                        // Mettre à jour l'avatar
                        updateAvatar(data.user);

                        // Afficher le lien organisateur si l'utilisateur est organisateur
                        if (data.user.role === 'organizer') {
                            document.getElementById('organizer-link').style.display = 'flex';
                            document.getElementById('mobile-organizer-link').style.display = 'flex';
                        }

                        // Configurer les dropdowns
                        setupDropdown('user-avatar', 'user-dropdown');
                        setupDropdown('mobile-user-avatar', 'mobile-user-dropdown');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    // Garder les boutons d'auth visibles
                });
        }

        function updateAvatar(user) {
            const elements = [
                { image: 'user-image', initials: 'user-initials' },
                { image: 'mobile-user-image', initials: 'mobile-user-initials' }
            ];

            elements.forEach(el => {
                const userImage = document.getElementById(el.image);
                const userInitials = document.getElementById(el.initials);

                if (user.has_image && user.profile_image) {
                    userImage.src = user.profile_image;
                    userImage.style.display = 'block';
                    userInitials.style.display = 'none';
                } else {
                    userInitials.textContent = user.initials;
                    userInitials.style.display = 'block';
                    userImage.style.display = 'none';
                }
            });
        }

        function setupDropdown(avatarId, dropdownId) {
            const avatarContainer = document.getElementById(avatarId);
            const avatarCircle = avatarContainer ? avatarContainer.querySelector('.avatar-circle') : null;
            const dropdown = document.getElementById(dropdownId);

            if (avatarCircle && dropdown) {
                avatarCircle.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    // Fermer tous les autres dropdowns
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        if (menu.id !== dropdownId) {
                            menu.classList.remove('show');
                        }
                    });

                    // Toggle le dropdown actuel
                    dropdown.classList.toggle('show');
                });
            }
        }

        // Fermer les dropdowns en cliquant ailleurs
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.user-avatar')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.remove('show');
                });
            }
        });

        // Gestion du thème
        setupThemeToggle();

        function setupThemeToggle() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            if (savedTheme === 'dark') {
                document.body.classList.add('dark-theme');
                updateThemeButton(true);
            }

            const themeButtons = ['theme-toggle', 'mobile-theme-toggle'];

            themeButtons.forEach(buttonId => {
                const themeButton = document.getElementById(buttonId);
                if (themeButton) {
                    themeButton.addEventListener('click', (e) => {
                        e.preventDefault();
                        const isDark = document.body.classList.toggle('dark-theme');

                        localStorage.setItem('theme', isDark ? 'dark' : 'light');
                        updateThemeButton(isDark);

                        // Fermer le dropdown
                        document.querySelectorAll('.dropdown-menu').forEach(menu => {
                            menu.classList.remove('show');
                        });
                    });
                }
            });
        }

        function updateThemeButton(isDark) {
            const themeButtons = document.querySelectorAll('.theme-toggle');
            themeButtons.forEach(button => {
                const icon = button.querySelector('i');
                const text = button.querySelector('.theme-text');

                if (isDark) {
                    icon.className = 'bi bi-sun';
                    text.textContent = 'Mode clair';
                } else {
                    icon.className = 'bi bi-moon';
                    text.textContent = 'Mode sombre';
                }
            });
        }

        // Gestion de la déconnexion
        function setupLogout(buttonId) {
            const logoutButton = document.getElementById(buttonId);
            if (logoutButton) {
                logoutButton.addEventListener('click', function (e) {
                    e.preventDefault();
                    const token = localStorage.getItem('jwt_token');
                    fetch('{{ route("logout") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Authorization': `Bearer ${token}`,
                        },
                    })
                        .then(response => {
                            console.log('Réponse de /logout:', response); // Débogage
                            if (!response.ok) {
                                return response.json().then(errorData => {
                                    throw new Error(`HTTP error! status: ${response.status}, message: ${JSON.stringify(errorData)}`);
                                }).catch(() => {
                                    throw new Error(`HTTP error! status: ${response.status}, non-JSON response`);
                                });
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log('Données de déconnexion:', data); // Débogage
                            if (data.success) {
                                localStorage.removeItem('jwt_token');
                                window.location.href = '{{ route("home") }}';
                            } else {
                                alert(data.error || 'Erreur lors de la déconnexion');
                            }
                        })
                        .catch(error => {
                            console.error('Erreur lors de la déconnexion:', error);
                            alert('Une erreur est survenue lors de la déconnexion. Veuillez réessayer.');
                        });
                });
            }
        }

        setupLogout('logout-button');
        setupLogout('mobile-logout-button');
    });
</script>
