<!DOCTYPE HTML>
<html lang="en-US">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>@yield('title', 'Echofy Sponsor')</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="56x56" href="{{ asset('assets/images/fav-icon/icon.png') }}">

    <!-- bootstrap CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <!-- carousel CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/owl.carousel.min.css') }}">
    <!-- animate CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/animate.css') }}">
    <!-- animated-text CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/animated-text.css') }}">
    <!-- font-awesome CSS via CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- font-flaticon CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/flaticon.css') }}">
    <!-- theme-default CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/theme-default.css') }}">
    <!-- meanmenu CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/meanmenu.min.css') }}">
    <!-- transitions CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/owl.transitions.css') }}">
    <!-- venobox CSS -->
    <link rel="stylesheet" href="{{ asset('assets/venobox/venobox.css') }}">
    
    <style>
        /* Sponsor Dashboard Styles - Couleurs vert et blanc comme la page d'accueil */
        .sponsor-dashboard {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        
        .sponsor-header {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        .sponsor-header h1 {
            margin: 0;
            font-weight: 600;
        }
        
        .sponsor-nav {
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 15px 0;
            margin-bottom: 30px;
        }
        
        .sponsor-nav .nav-link {
            color: #333;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        
        .sponsor-nav .nav-link:hover,
        .sponsor-nav .nav-link.active {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }
        
        .stats-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
            border-left: 4px solid #28a745;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            font-size: 24px;
            color: white;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            margin: 10px 0;
            color: #28a745;
        }
        
        .stats-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .action-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
            border-top: 4px solid #28a745;
        }
        
        .action-card:hover {
            transform: translateY(-5px);
        }
        
        .action-card .btn {
            border-radius: 25px;
            padding: 12px 30px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .action-card .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.3);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
        }
        
        .btn-warning {
            background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
            border: none;
            color: white;
        }
        
        .btn-info {
            background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);
            border: none;
        }
        
        .campaign-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 30px;
            border-top: 4px solid #28a745;
        }
        
        .campaign-card:hover {
            transform: translateY(-5px);
        }
        
        .campaign-card .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        
        .campaign-card .card-body {
            padding: 20px;
        }
        
        .campaign-card .card-footer {
            background: #f8f9fa;
            border-top: none;
            padding: 15px 20px;
        }
        
        .welcome-section {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .welcome-section h4 {
            margin: 0 0 10px 0;
            font-weight: 600;
        }
        
        .welcome-section p {
            margin: 0;
            opacity: 0.9;
        }
        
        .text-primary {
            color: #28a745 !important;
        }
        
        .text-success {
            color: #28a745 !important;
        }
        
        .text-warning {
            color: #ffc107 !important;
        }
        
        .text-info {
            color: #17a2b8 !important;
        }
        
        /* Styles pour l'image de profil dans le header */
        .sponsor-header .profile-image {
            transition: all 0.3s ease;
        }
        
        .sponsor-header .profile-image:hover {
            transform: scale(1.1);
            border-color: rgba(255,255,255,0.6) !important;
        }
        
        .sponsor-header .user-info {
            text-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        
        /* Styles pour les champs remplis */
        .form-control:not(:placeholder-shown) {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }
        
        .form-control:focus {
            background-color: white !important;
            border-color: #28a745 !important;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25) !important;
        }
        
        .form-control[readonly] {
            background-color: #e9ecef !important;
            border-color: #ced4da !important;
        }
        
        /* Style spécial pour les champs avec valeur */
        .form-control[value]:not([value=""]) {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }
        
        /* Style pour les textarea avec contenu */
        .form-control:not(:placeholder-shown) {
            background-color: #f8f9fa !important;
            border-color: #dee2e6 !important;
        }
        
        /* Style pour le bouton de notifications */
        .btn-notification {
            transition: all 0.3s ease;
            position: relative;
        }
        
        .btn-notification:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }
        
        .btn-notification .badge {
            font-size: 0.7rem;
            min-width: 18px;
            height: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .btn-notification.has-notifications {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }
    </style>
    
    @stack('styles')
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Sponsor Header -->
    <div class="sponsor-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="fas fa-handshake"></i> Dashboard Sponsor</h1>
                    <p class="mb-0">Bienvenue dans votre espace sponsor</p>
                </div>
                    <div class="col-md-4 text-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <div class="me-3 text-end">
                                <div class="d-flex align-items-center justify-content-end mb-1">
                                    @if(isset($user) && $user->profile_image)
                                        <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}" class="rounded-circle me-2 profile-image" style="width: 40px; height: 40px; object-fit: cover; border: 2px solid rgba(255,255,255,0.3);">
                                    @else
                                        <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center me-2 profile-image" style="width: 40px; height: 40px;">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div class="user-info">
                                        <small class="d-block">Connecté en tant que</small>
                                        <strong class="text-white">{{ isset($user) ? $user->name : 'Sponsor' }}</strong>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Bouton Notifications -->
                            <div class="me-3">
                                <a href="{{ route('sponsor.notifications') }}" 
                                   class="btn btn-outline-light btn-sm position-relative btn-notification" 
                                   id="navbar-notification-btn"
                                   data-bs-toggle="tooltip" 
                                   data-bs-placement="bottom" 
                                   title="Consulter mes notifications">
                                    <i class="fas fa-bell"></i>
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="navbar-notification-count" style="display: none;">
                                        0
                                    </span>
                                </a>
                            </div>
                            
                            <button class="btn btn-outline-light btn-sm" onclick="logout()">
                                <i class="fas fa-sign-out-alt"></i> Déconnexion
                            </button>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    <!-- Sponsor Navigation -->
    <div class="sponsor-nav">
        <div class="container">
            <nav class="nav">
                <a class="nav-link {{ request()->routeIs('sponsor.dashboard') ? 'active' : '' }}" href="{{ route('sponsor.dashboard') }}">
                    <i class="fas fa-tachometer-alt"></i> Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('sponsor.analytics') ? 'active' : '' }}" href="{{ route('sponsor.analytics') }}">
                    <i class="fas fa-chart-bar"></i> Analytics
                </a>
                <a class="nav-link {{ request()->routeIs('sponsor.ai.recommendations') ? 'active' : '' }}" href="{{ route('sponsor.ai.recommendations') }}">
                    <i class="fas fa-brain"></i> Recommander pour vous
                    <span class="badge bg-primary ms-2">Nouveau</span>
                </a>
                <a class="nav-link {{ request()->routeIs('sponsor.stories.*') ? 'active' : '' }}" href="{{ route('sponsor.stories.my-stories') }}">
                    <i class="fas fa-camera"></i> Mes Stories
                    <span class="badge bg-success ms-2">24h</span>
                </a>
                <a class="nav-link {{ request()->routeIs('sponsor.feedback') ? 'active' : '' }}" href="{{ route('sponsor.feedback') }}">
                    <i class="fas fa-comments"></i> Feedback & Commentaires
                    <span class="badge bg-success ms-2">Nouveau</span>
                </a>
                    <a class="nav-link {{ request()->routeIs('sponsor.campaigns') ? 'active' : '' }}" href="{{ route('sponsor.campaigns') }}">
                        <i class="fas fa-calendar-alt"></i> Campagnes
                    </a>
                    <a class="nav-link {{ request()->routeIs('sponsor.profile') ? 'active' : '' }}" href="{{ route('sponsor.profile') }}">
                        <i class="fas fa-user-edit"></i> Mon Profil
                    </a>
                    <a class="nav-link {{ request()->routeIs('sponsor.company') ? 'active' : '' }}" href="{{ route('sponsor.company') }}">
                        <i class="fas fa-building"></i> Mon Entreprise
                    </a>
                    <a class="nav-link {{ request()->routeIs('sponsor.sponsorships') ? 'active' : '' }}" href="{{ route('sponsor.sponsorships') }}">
                        <i class="fas fa-handshake"></i> Mes Sponsorships
                    </a>
                    <a class="nav-link {{ request()->routeIs('sponsor.statistics') ? 'active' : '' }}" href="{{ route('sponsor.statistics') }}">
                        <i class="fas fa-chart-line"></i> Statistiques
                    </a>
            </nav>
        </div>
    </div>

    <!-- Main Content -->
    <div class="sponsor-dashboard">
        <div class="container">
            @yield('content')
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-4 mt-5" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white;">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p class="mb-0">&copy; 2024 Echofy. Tous droits réservés.</p>
                </div>
                <div class="col-md-6 text-end">
                    <p class="mb-0">Plateforme de sponsoring écologique</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="{{ asset('assets/js/bootstrap.min.js') }}"></script>
    
    <!-- Notification Manager -->
    <script src="{{ asset('assets/js/notification-manager.js') }}"></script>
    
    <script>
        function logout() {
            if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
                localStorage.removeItem('jwt_token');
                document.cookie = 'jwt_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
                window.location.href = '{{ route("login") }}';
            }
        }
    </script>
    
    @stack('scripts')
</body>
</html>

