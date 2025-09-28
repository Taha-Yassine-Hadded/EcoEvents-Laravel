<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'EcoEvents - Interface Organisateur')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #2d5a27 0%, #4a7c59 100%);
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            border-radius: 8px;
            margin: 5px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .btn-eco {
            background: linear-gradient(135deg, #4a7c59 0%, #2d5a27 100%);
            border: none;
            color: white;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }
        .btn-eco:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0,0,0,0.2);
            color: white;
        }
        .stats-card {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            border-left: 4px solid #4a7c59;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-4">
                    <h4 class="text-center mb-4">
                        <i class="fas fa-leaf me-2"></i>EcoEvents
                    </h4>
                    <div class="text-center mb-4">
                        <div class="bg-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            @if(Auth::user()->hasProfileImage())
                                <img src="{{ Auth::user()->profile_image_url }}" alt="Profile" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <span class="text-success fw-bold fs-5">{{ Auth::user()->initials }}</span>
                            @endif
                        </div>
                        <div class="mt-2">
                            <small class="text-light">{{ Auth::user()->name }}</small>
                            <br>
                            <span class="badge bg-success">Organisateur</span>
                        </div>
                    </div>
                </div>
                
                <nav class="nav flex-column px-3">
                    <a class="nav-link {{ request()->routeIs('organizer.communities.index') ? 'active' : '' }}" href="{{ route('organizer.communities.index') }}">
                        <i class="fas fa-users me-2"></i>Mes Communautés
                    </a>
                    <a class="nav-link {{ request()->routeIs('organizer.communities.create') ? 'active' : '' }}" href="{{ route('organizer.communities.create') }}">
                        <i class="fas fa-plus-circle me-2"></i>Créer Communauté
                    </a>
                    <a class="nav-link {{ request()->routeIs('organizer.membership-requests') ? 'active' : '' }}" href="{{ route('organizer.membership-requests') }}">
                        <i class="fas fa-clipboard-list me-2"></i>Demandes d'adhésion
                        @php
                            $pendingCount = \App\Models\CommunityMember::whereHas('community', function($query) {
                                $query->where('organizer_id', Auth::id());
                            })->where('status', 'pending')->count();
                        @endphp
                        @if($pendingCount > 0)
                            <span class="badge bg-warning text-dark ms-2">{{ $pendingCount }}</span>
                        @endif
                    </a>
                    <hr class="text-light">
                    <a class="nav-link" href="{{ route('home') }}">
                        <i class="fas fa-home me-2"></i>Retour Accueil
                    </a>
                    <a class="nav-link" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt me-2"></i>Déconnexion
                    </a>
                </nav>
                
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                    @csrf
                </form>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
                <!-- Header -->
                <div class="bg-white shadow-sm p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-0">@yield('page-title', 'Interface Organisateur')</h2>
                            <small class="text-muted">@yield('page-subtitle', 'Gérez vos communautés écologiques')</small>
                        </div>
                        <div>
                            @yield('header-actions')
                        </div>
                    </div>
                </div>

                <!-- Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Content -->
                <div class="px-4 pb-4">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
