<!-- Admin Sidebar -->
<aside class="admin-sidebar">
    <div class="sidebar-content">
        <!-- Main Navigation -->
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link active">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-text">Dashboard</span>
                    </a>
                </li>

                <!-- Users Management -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="fas fa-users"></i>
                        <span class="nav-text">Users</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="#" class="submenu-link">All Users</a></li>
                        <li><a href="#" class="submenu-link">Add User</a></li>
                        <li><a href="#" class="submenu-link">User Roles</a></li>
                    </ul>
                </li>

                <!-- Content Management -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="fas fa-file-alt"></i>
                        <span class="nav-text">Content</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="#" class="submenu-link">All Posts</a></li>
                        <li><a href="#" class="submenu-link">Add Post</a></li>
                        <li><a href="#" class="submenu-link">Categories</a></li>
                        <li><a href="#" class="submenu-link">Tags</a></li>
                    </ul>
                </li>

                <!-- Media -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-images"></i>
                        <span class="nav-text">Media</span>
                    </a>
                </li>

                <!-- Analytics -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-chart-bar"></i>
                        <span class="nav-text">Analytics</span>
                    </a>
                </li>

                <!-- E-commerce -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="nav-text">E-commerce</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="#" class="submenu-link">Products</a></li>
                        <li><a href="#" class="submenu-link">Orders</a></li>
                        <li><a href="#" class="submenu-link">Categories</a></li>
                        <li><a href="#" class="submenu-link">Coupons</a></li>
                    </ul>
                </li>

                <!-- Messages -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-envelope"></i>
                        <span class="nav-text">Messages</span>
                        <span class="nav-badge">5</span>
                    </a>
                </li>

                <!-- Divider -->
                <li class="nav-divider">
                    <span>System</span>
                </li>

                <!-- Settings -->
                <li class="nav-item has-submenu">
                    <a href="#" class="nav-link">
                        <i class="fas fa-cog"></i>
                        <span class="nav-text">Settings</span>
                        <i class="fas fa-chevron-right submenu-arrow"></i>
                    </a>
                    <ul class="submenu">
                        <li><a href="#" class="submenu-link">General</a></li>
                        <li><a href="#" class="submenu-link">Security</a></li>
                        <li><a href="#" class="submenu-link">Email</a></li>
                        <li><a href="#" class="submenu-link">Backup</a></li>
                    </ul>
                </li>

                <!-- Tools -->
                <li class="nav-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-tools"></i>
                        <span class="nav-text">Tools</span>
                    </a>
                </li>

                <!-- Logout -->
                <li class="nav-item logout-item">
                    <a href="#" class="nav-link">
                        <i class="fas fa-sign-out-alt"></i>
                        <span class="nav-text">Logout</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="footer-info">
            <p class="version">Version 1.0.0</p>
            <p class="copyright">&copy; 2024 Echofy</p>
        </div>
    </div>
</aside>

<style>
.admin-sidebar {
    position: fixed;
    top: 70px;
    left: 0;
    width: 260px;
    height: calc(100vh - 70px);
    background: #2c3e50;
    color: white;
    overflow-y: auto;
    transition: all 0.3s ease;
    z-index: 999;
    box-shadow: 2px 0 5px rgba(0,0,0,0.1);
}

.admin-sidebar.collapsed {
    width: 70px;
}

.sidebar-content {
    height: calc(100% - 60px);
    padding: 20px 0;
}

.sidebar-nav {
    height: 100%;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin-bottom: 2px;
}

.nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #bdc3c7;
    text-decoration: none;
    transition: all 0.3s ease;
    position: relative;
}

.nav-link:hover {
    background: #34495e;
    color: #fff;
    text-decoration: none;
}

.nav-link.active {
    background: #3498db;
    color: white;
}

.nav-link.active::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    height: 100%;
    width: 4px;
    background: #fff;
}

.nav-link i {
    width: 20px;
    margin-right: 15px;
    text-align: center;
    font-size: 16px;
}

.nav-text {
    flex: 1;
    font-weight: 500;
}

.submenu-arrow {
    margin-left: auto;
    font-size: 12px;
    transition: transform 0.3s ease;
}

.nav-item.has-submenu.open .submenu-arrow {
    transform: rotate(90deg);
}

.nav-badge {
    background: #e74c3c;
    color: white;
    border-radius: 10px;
    padding: 2px 8px;
    font-size: 12px;
    margin-left: auto;
}

.submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    background: #1a252f;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease;
}

.nav-item.has-submenu.open .submenu {
    max-height: 200px;
}

.submenu-link {
    display: block;
    padding: 10px 20px 10px 55px;
    color: #95a5a6;
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 14px;
}

.submenu-link:hover {
    background: #34495e;
    color: #fff;
    text-decoration: none;
}

.nav-divider {
    margin: 20px 0 10px 0;
    padding: 0 20px;
}

.nav-divider span {
    color: #7f8c8d;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 1px;
}

.logout-item {
    margin-top: auto;
    border-top: 1px solid #34495e;
    padding-top: 10px;
}

.sidebar-footer {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    height: 60px;
    background: #1a252f;
    padding: 15px 20px;
    border-top: 1px solid #34495e;
}

.footer-info {
    text-align: center;
}

.footer-info p {
    margin: 0;
    font-size: 12px;
    color: #7f8c8d;
}

.version {
    font-weight: 600;
}

/* Collapsed sidebar styles */
.admin-sidebar.collapsed .nav-text,
.admin-sidebar.collapsed .submenu-arrow,
.admin-sidebar.collapsed .nav-badge,
.admin-sidebar.collapsed .nav-divider,
.admin-sidebar.collapsed .sidebar-footer {
    display: none;
}

.admin-sidebar.collapsed .nav-link {
    padding: 12px;
    justify-content: center;
}

.admin-sidebar.collapsed .nav-link i {
    margin-right: 0;
}

.admin-sidebar.collapsed .submenu {
    display: none;
}

/* Responsive */
@media (max-width: 768px) {
    .admin-sidebar {
        transform: translateX(-100%);
    }
    
    .admin-sidebar.mobile-open {
        transform: translateX(0);
    }
}

/* Scrollbar styling */
.admin-sidebar::-webkit-scrollbar {
    width: 6px;
}

.admin-sidebar::-webkit-scrollbar-track {
    background: #1a252f;
}

.admin-sidebar::-webkit-scrollbar-thumb {
    background: #34495e;
    border-radius: 3px;
}

.admin-sidebar::-webkit-scrollbar-thumb:hover {
    background: #4a5f7a;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle sidebar toggle
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.admin-sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });
    }
    
    // Handle submenu toggle
    const submenuItems = document.querySelectorAll('.nav-item.has-submenu');
    
    submenuItems.forEach(function(item) {
        const link = item.querySelector('.nav-link');
        
        link.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Close other submenus
            submenuItems.forEach(function(otherItem) {
                if (otherItem !== item) {
                    otherItem.classList.remove('open');
                }
            });
            
            // Toggle current submenu
            item.classList.toggle('open');
        });
    });
    
    // Mobile sidebar toggle
    if (window.innerWidth <= 768) {
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                sidebar.classList.toggle('mobile-open');
            });
        }
    }
    
    // Close mobile sidebar when clicking outside
    document.addEventListener('click', function(e) {
        if (window.innerWidth <= 768) {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                sidebar.classList.remove('mobile-open');
            }
        }
    });
});
</script>