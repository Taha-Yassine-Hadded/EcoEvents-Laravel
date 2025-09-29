<!-- Sponsor Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-content">
        <!-- User Info -->
        <div class="sidebar-user">
            <div class="user-avatar">
                @if($user->profile_image)
                    <img src="{{ asset('storage/' . $user->profile_image) }}" alt="{{ $user->name }}">
                @else
                    <i class="fas fa-user-circle"></i>
                @endif
            </div>
            <div class="user-info">
                <h6>{{ $user->name }}</h6>
                <span class="badge badge-primary">Sponsor</span>
            </div>
        </div>

        <!-- Main Navigation -->
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('sponsor.dashboard') }}" class="nav-link {{ request()->routeIs('sponsor.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <!-- Campaigns -->
                <li class="nav-item">
                    <a href="{{ route('sponsor.campaigns') }}" class="nav-link {{ request()->routeIs('sponsor.campaigns') ? 'active' : '' }}">
                        <i class="fas fa-calendar-alt"></i>
                        <span class="nav-text">Campagnes</span>
                    </a>
                </li>

                <!-- My Sponsorships -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-handshake"></i>
                        <span class="nav-text">Mes Sponsorships</span>
                    </a>
                </li>

                <!-- Profile -->
                <li class="nav-item">
                    <a href="{{ route('sponsor.profile') }}" class="nav-link {{ request()->routeIs('sponsor.profile') ? 'active' : '' }}">
                        <i class="fas fa-user-edit"></i>
                        <span class="nav-text">Mon Profil</span>
                    </a>
                </li>

                <!-- Statistics -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-line"></i>
                        <span class="nav-text">Statistiques</span>
                    </a>
                </li>

                <!-- Messages -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span class="nav-text">Messages</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span class="nav-text">Paramètres</span>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item logout-item">
                    <a href="#" class="nav-link" onclick="logout()">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-text">Déconnexion</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>

<script>
function logout() {
    if (confirm('Êtes-vous sûr de vouloir vous déconnecter ?')) {
        // Supprimer le token du localStorage
        localStorage.removeItem('jwt_token');
        
        // Supprimer le cookie
        document.cookie = 'jwt_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
        
        // Rediriger vers la page de connexion
        window.location.href = '{{ route("login") }}';
    }
}
</script>
