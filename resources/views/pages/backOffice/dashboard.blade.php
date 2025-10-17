@extends('layouts.admin')

@section('title', 'Admin Dashboard - Echofy')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">Dashboard</h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Home</li>
            <li class="breadcrumb-item active">Dashboard</li>
        </ol>
    </nav>
</div>

<!-- Statistics Cards Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['general']['total_users'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Sponsoring
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['sponsoring']['total_amount'] ?? 0, 0, ',', ' ') }} €</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">Sponsoring en Attente</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sponsoring']['pending_sponsorships'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-handshake fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Sponsors Actifs
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['general']['total_sponsors'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gestion des Sponsoring -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="card-title">Gestion des Sponsoring</h6>
                <div class="btn-group">
                    <a href="{{ route('admin.sponsors.pending-sponsorships') }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-handshake"></i> Propositions en Attente
                        @if(($stats['sponsoring']['pending_sponsorships'] ?? 0) > 0)
                            <span class="badge bg-danger ms-1">{{ $stats['sponsoring']['pending_sponsorships'] }}</span>
                        @endif
                    </a>
                    <a href="{{ route('admin.sponsors.index') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-users"></i> Gestion Sponsors
                    </a>
                </div>
            </div>
            <div class="card-body">
                <!-- Statistiques des Sponsoring -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-primary mb-1">{{ $stats['sponsoring']['total_sponsorships'] ?? 0 }}</h4>
                            <small class="text-muted">Total Propositions</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-warning mb-1">{{ $stats['sponsoring']['pending_sponsorships'] ?? 0 }}</h4>
                            <small class="text-muted">En Attente</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-success mb-1">{{ $stats['sponsoring']['approved_sponsorships'] ?? 0 }}</h4>
                            <small class="text-muted">Approuvées</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center p-3 bg-light rounded">
                            <h4 class="text-info mb-1">{{ $stats['sponsoring']['recent_sponsorships'] ?? 0 }}</h4>
                            <small class="text-muted">Cette Semaine</small>
                        </div>
                    </div>
                </div>

                <!-- Actions Rapides -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-left-warning">
                            <div class="card-body">
                                <h6 class="card-title text-warning">
                                    <i class="fas fa-exclamation-triangle"></i> Attention Requise
                                </h6>
                                <p class="card-text">
                                    Vous avez <strong>{{ $stats['sponsoring']['pending_sponsorships'] ?? 0 }}</strong> 
                                    propositions de sponsoring en attente d'approbation.
                                </p>
                                <a href="{{ route('admin.sponsors.pending-sponsorships') }}" class="btn btn-warning btn-sm">
                                    <i class="fas fa-eye"></i> Voir les Propositions
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-left-success">
                            <div class="card-body">
                                <h6 class="card-title text-success">
                                    <i class="fas fa-chart-line"></i> Performance
                                </h6>
                                <p class="card-text">
                                    Montant total des sponsoring approuvés : 
                                    <strong>{{ number_format($stats['sponsoring']['approved_amount'] ?? 0, 0, ',', ' ') }} €</strong>
                                </p>
                                <a href="{{ route('admin.sponsors.index') }}" class="btn btn-success btn-sm">
                                    <i class="fas fa-users"></i> Gérer les Sponsors
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Propositions Récentes -->
                @if(isset($stats['sponsoring']['pending_sponsorships']) && $stats['sponsoring']['pending_sponsorships'] > 0)
                <div class="mt-4">
                    <h6 class="text-warning">
                        <i class="fas fa-clock"></i> Propositions Récentes en Attente
                    </h6>
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        Il y a actuellement <strong>{{ $stats['sponsoring']['pending_sponsorships'] }}</strong> 
                        propositions de sponsoring qui attendent votre validation. 
                        <a href="{{ route('admin.sponsors.pending-sponsorships') }}" class="alert-link">
                            Cliquez ici pour les examiner
                        </a>.
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Gestion des Campagnes -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="card-title">Gestion des Campagnes</h6>
                <div>
                    <button class="btn btn-success btn-sm me-2" onclick="createCampaign()">
                        <i class="fas fa-plus"></i> Nouvelle Campagne
                    </button>
                    <button class="btn btn-primary btn-sm" onclick="loadCampaigns()">
                        <i class="fas fa-sync-alt"></i> Actualiser
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtres -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <input type="text" id="searchCampaign" class="form-control" placeholder="Rechercher campagne...">
                    </div>
                    <div class="col-md-2">
                        <select id="statusCampaignFilter" class="form-control">
                            <option value="">Tous les statuts</option>
                            <option value="draft">Brouillon</option>
                            <option value="active">Active</option>
                            <option value="paused">En pause</option>
                            <option value="completed">Terminée</option>
                            <option value="cancelled">Annulée</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select id="typeCampaignFilter" class="form-control">
                            <option value="">Tous les types</option>
                            <option value="event">Événement</option>
                            <option value="festival">Festival</option>
                            <option value="conference">Conférence</option>
                            <option value="sport">Sport</option>
                            <option value="culture">Culture</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-outline-primary" onclick="filterCampaigns()">
                            <i class="fas fa-search"></i> Filtrer
                        </button>
                    </div>
                </div>
                
                <!-- Liste des Campagnes -->
                <div class="table-responsive">
                    <table class="table table-bordered" id="campaignsTable">
                        <thead>
                            <tr>
                                <th>Nom</th>
                                <th>Type</th>
                                <th>Statut</th>
                                <th>Date Début</th>
                                <th>Date Fin</th>
                                <th>Budget</th>
                                <th>Sponsors</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="campaignsTableBody">
                            <!-- Les données seront chargées via AJAX -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Recent Contracts Widget -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="card-title mb-0"><i class="fas fa-file-contract"></i> Contrats récents</h6>
                <a href="{{ route('admin.contracts.index') }}" class="btn btn-sm btn-outline-primary">Voir tout</a>
            </div>
            <div class="card-body">
                @if(isset($recentContracts) && count($recentContracts) > 0)
                    <div class="list-group">
                        @foreach($recentContracts as $c)
                            <div class="list-group-item d-flex justify-content-between align-items-start">
                                <div class="ms-2 me-auto">
                                    <div class="fw-bold">{{ $c->user->name ?? 'Sponsor' }} <span class="text-muted">·</span> {{ $c->event->title ?? 'Événement' }}</div>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($c->updated_at)->format('d/m/Y H:i') }} · {{ $c->package_name }} · {{ number_format($c->amount, 0, ',', ' ') }} €</small>
                                </div>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('contracts.sponsorship.view', $c->id) }}" class="btn btn-outline-info" target="_blank" title="Voir"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('contracts.sponsorship.download', $c->id) }}" class="btn btn-outline-primary" title="Télécharger"><i class="fas fa-download"></i></a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-file-contract fa-2x mb-2"></i>
                        <div>Aucun contrat récent</div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="card-title">Revenue Overview</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in">
                        <div class="dropdown-header">Actions:</div>
                        <a class="dropdown-item" href="#">View Details</a>
                        <a class="dropdown-item" href="#">Export Data</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Settings</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="myAreaChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="card-title">Revenue Sources</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="myPieChart" width="100%" height="50"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    <span class="mr-2">
                        <i class="fas fa-circle text-primary"></i> Direct
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-success"></i> Social
                    </span>
                    <span class="mr-2">
                        <i class="fas fa-circle text-info"></i> Referral
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Tables Row -->
<div class="row">
    <!-- Recent Orders -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="card-title">Recent Orders</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>#12345</td>
                                <td>John Doe</td>
                                <td>$125.00</td>
                                <td><span class="badge badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>#12346</td>
                                <td>Jane Smith</td>
                                <td>$89.50</td>
                                <td><span class="badge badge-warning">Pending</span></td>
                            </tr>
                            <tr>
                                <td>#12347</td>
                                <td>Mike Johnson</td>
                                <td>$234.75</td>
                                <td><span class="badge badge-info">Processing</span></td>
                            </tr>
                            <tr>
                                <td>#12348</td>
                                <td>Sarah Wilson</td>
                                <td>$67.25</td>
                                <td><span class="badge badge-success">Completed</span></td>
                            </tr>
                            <tr>
                                <td>#12349</td>
                                <td>David Brown</td>
                                <td>$156.00</td>
                                <td><span class="badge badge-danger">Cancelled</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <a class="btn btn-primary" href="#">View All Orders</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="card-title">Recent Activities</h6>
            </div>
            <div class="card-body">
                <div class="activity-feed">
                    <div class="activity-item">
                        <div class="activity-icon bg-primary">
                            <i class="fas fa-user"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">New user <strong>John Doe</strong> registered</p>
                            <small class="activity-time">2 minutes ago</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-success">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">Order <strong>#12350</strong> completed</p>
                            <small class="activity-time">5 minutes ago</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">System maintenance scheduled</p>
                            <small class="activity-time">1 hour ago</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-info">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">New message from <strong>Support Team</strong></p>
                            <small class="activity-time">2 hours ago</small>
                        </div>
                    </div>
                    <div class="activity-item">
                        <div class="activity-icon bg-danger">
                            <i class="fas fa-ban"></i>
                        </div>
                        <div class="activity-content">
                            <p class="activity-text">User <strong>spammer@email.com</strong> blocked</p>
                            <small class="activity-time">3 hours ago</small>
                        </div>
                    </div>
                </div>
                <div class="text-center mt-3">
                    <a class="btn btn-primary" href="#">View All Activities</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Row -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="card-title">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="#" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-plus-circle"></i><br>
                            Add New User
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="#" class="btn btn-success btn-lg btn-block">
                            <i class="fas fa-file-alt"></i><br>
                            Create Post
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="#" class="btn btn-warning btn-lg btn-block">
                            <i class="fas fa-cog"></i><br>
                            System Settings
                        </a>
                    </div>
                    <div class="col-md-3 col-sm-6 mb-3">
                        <a href="#" class="btn btn-info btn-lg btn-block">
                            <i class="fas fa-chart-bar"></i><br>
                            View Analytics
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Dashboard specific styles */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-xs {
    font-size: 0.7rem;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.progress-sm {
    height: 0.5rem;
}

.chart-area {
    position: relative;
    height: 300px;
    width: 100%;
}

.chart-pie {
    position: relative;
    height: 250px;
    width: 100%;
}

/* Activity Feed Styles */
.activity-feed {
    position: relative;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid #f1f1f1;
}

.activity-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.activity-icon {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-right: 15px;
    flex-shrink: 0;
}

.activity-content {
    flex: 1;
}

.activity-text {
    margin: 0;
    color: #5a5c69;
    font-size: 14px;
}

.activity-time {
    color: #858796;
    font-size: 12px;
}

/* Badge Styles */
.badge {
    font-size: 0.75em;
    padding: 0.25em 0.6em;
    border-radius: 0.35rem;
}

.badge-success {
    background-color: #1cc88a;
}

.badge-warning {
    background-color: #f6c23e;
    color: #fff;
}

.badge-info {
    background-color: #36b9cc;
}

.badge-danger {
    background-color: #e74a3b;
}

/* Quick Action Buttons */
.btn-lg {
    padding: 1rem 1.5rem;
    font-size: 1.1rem;
    line-height: 1.5;
    border-radius: 0.5rem;
    text-align: center;
}

.btn-block {
    display: block;
    width: 100%;
}

.btn-lg i {
    font-size: 2rem;
    margin-bottom: 0.5rem;
    display: block;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .btn-lg {
        padding: 0.75rem 1rem;
        font-size: 1rem;
    }
    
    .btn-lg i {
        font-size: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Area Chart
    const areaChart = document.getElementById('myAreaChart');
    if (areaChart) {
        const ctx = areaChart.getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Revenue',
                    data: [10000, 15000, 12000, 18000, 20000, 25000, 22000, 28000, 30000, 32000, 35000, 40000],
                    backgroundColor: 'rgba(78, 115, 223, 0.1)',
                    borderColor: 'rgba(78, 115, 223, 1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Pie Chart
    const pieChart = document.getElementById('myPieChart');
    if (pieChart) {
        const ctx = pieChart.getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Direct', 'Social', 'Referral'],
                datasets: [{
                    data: [55, 30, 15],
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }

    // Add some interactive functionality
    const statCards = document.querySelectorAll('.card');
    statCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'all 0.3s ease';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // ==================== GESTION DES SPONSORS ====================
    
    // Fonction pour charger les sponsors - Fonction globale
    window.loadSponsors = async function() {
        try {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                console.error('Token JWT manquant');
                return;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value ||
                             '{{ csrf_token() }}';
            
            const response = await fetch('/admin/sponsors', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            if (response.ok) {
                const data = await response.text();
                // Extraire les données des sponsors depuis la réponse HTML
                const parser = new DOMParser();
                const doc = parser.parseFromString(data, 'text/html');
                
                // Essayer de récupérer les données JSON si disponibles
                const jsonScript = doc.querySelector('script[type="application/json"]');
                if (jsonScript) {
                    const sponsors = JSON.parse(jsonScript.textContent);
                    displaySponsors(sponsors);
                } else {
                    // Fallback: utiliser l'API directe
                    await loadSponsorsFromAPI();
                }
            } else {
                console.error('Erreur lors du chargement des sponsors:', response.status);
                await loadSponsorsFromAPI();
            }
        } catch (error) {
            console.error('Erreur:', error);
            await loadSponsorsFromAPI();
        }
    }

    // Charger les sponsors depuis l'API
    async function loadSponsorsFromAPI() {
        // Aller directement à loadSponsorsDirectly car /api/sponsors n'existe pas
        await loadSponsorsDirectly();
    };

    // Charger les sponsors directement depuis la base
    async function loadSponsorsDirectly() {
        try {
            const token = localStorage.getItem('jwt_token');
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                             document.querySelector('input[name="_token"]')?.value ||
                             '{{ csrf_token() }}';
            
            const response = await fetch('/admin/sponsors/data', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Authorization': 'Bearer ' + token,
                    'X-CSRF-TOKEN': csrfToken,
                }
            });

            if (response.ok) {
                const sponsors = await response.json();
                console.log('Sponsors chargés:', sponsors);
                displaySponsors(sponsors);
            } else {
                console.error('Impossible de charger les sponsors:', response.status);
                displayTestSponsors();
            }
        } catch (error) {
            console.error('Erreur directe:', error);
            displayTestSponsors();
        }
    }

    // Afficher les sponsors dans le tableau
    function displaySponsors(sponsors) {
        const tbody = document.getElementById('sponsorsTableBody');
        tbody.innerHTML = '';

        sponsors.forEach(sponsor => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${sponsor.name}</td>
                <td>${sponsor.email}</td>
                <td>${sponsor.company_name || 'N/A'}</td>
                <td><span class="badge badge-${window.getStatusClass(sponsor.status)}">${window.getStatusText(sponsor.status)}</span></td>
                <td>${window.formatDate(sponsor.created_at)}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary" onclick="viewSponsor(${sponsor.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    ${sponsor.status === 'pending' ? `
                        <button class="btn btn-sm btn-outline-success" onclick="approveSponsor(${sponsor.id})">
                            <i class="fas fa-check"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="rejectSponsor(${sponsor.id})">
                            <i class="fas fa-times"></i>
                        </button>
                    ` : ''}
                    <button class="btn btn-sm btn-outline-${sponsor.status === 'active' ? 'warning' : 'success'}" onclick="toggleStatus(${sponsor.id}, '${sponsor.status}')">
                        <i class="fas fa-${sponsor.status === 'active' ? 'pause' : 'play'}"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSponsor(${sponsor.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // Données de test
    function displayTestSponsors() {
        const testSponsors = [
            { id: 1, name: 'Test Sponsor 1', email: 'sponsor1@test.com', company_name: 'Test Company 1', status: 'pending', created_at: new Date() },
            { id: 2, name: 'Test Sponsor 2', email: 'sponsor2@test.com', company_name: 'Test Company 2', status: 'approved', created_at: new Date() },
            { id: 3, name: 'Test Sponsor 3', email: 'sponsor3@test.com', company_name: 'Test Company 3', status: 'active', created_at: new Date() }
        ];
        displaySponsors(testSponsors);
    }


});


    // Charger les sponsors au chargement de la page
    window.loadSponsors();
</script>

<script>
// ==================== FONCTIONS GLOBALES POUR LES SPONSORS ====================

// Actions sur les sponsors - Fonctions globales
window.approveSponsor = async function(id) {
    if (!confirm('Approuver ce sponsor ?')) return;
    
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/sponsors/${id}/approve`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            alert('Sponsor approuvé avec succès !');
            window.loadSponsors();
        } else {
            alert('Erreur lors de l\'approbation: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

window.rejectSponsor = async function(id) {
    if (!confirm('Rejeter ce sponsor ?')) return;
    
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/sponsors/${id}/reject`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            alert('Sponsor rejeté avec succès !');
            window.loadSponsors();
        } else {
            alert('Erreur lors du rejet: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

window.toggleStatus = async function(id, currentStatus) {
    const action = currentStatus === 'active' ? 'désactiver' : 'activer';
    if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} ce sponsor ?`)) return;
    
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/sponsors/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            alert(`Sponsor ${action} avec succès !`);
            window.loadSponsors();
        } else {
            alert('Erreur lors du changement de statut: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

window.deleteSponsor = async function(id) {
    if (!confirm('Supprimer définitivement ce sponsor ?\n\nCette action supprimera :\n• Le compte sponsor\n• Tous ses sponsorships\n• Ses fichiers')) return;
    
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        console.log('Token CSRF:', csrfToken);
        console.log('JWT Token:', token ? 'Présent' : 'Absent');
        
        // Créer un FormData pour éviter les problèmes de CORS
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', csrfToken);
        
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
        
        // Ajouter l'authorization seulement si le token existe
        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }
        
        const response = await fetch(`/admin/sponsors/${id}`, {
            method: 'POST',
            headers: headers,
            body: formData
        });

        console.log('Response status:', response.status);
        
        if (response.ok) {
            alert('Sponsor supprimé avec succès !');
            window.loadSponsors();
        } else {
            const errorData = await response.text();
            console.error('Erreur response:', errorData);
            alert('Erreur lors de la suppression: ' + response.status + ' - ' + errorData);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

window.viewSponsor = async function(id) {
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/sponsors/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (response.ok) {
            const sponsor = await response.json();
            showSponsorDetailsModal(sponsor);
        } else {
            alert('Erreur lors du chargement des détails: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

function showSponsorDetailsModal(sponsor) {
    // Créer la modal si elle n'existe pas
    let modal = document.getElementById('sponsorDetailsModal');
    if (!modal) {
        modal = createSponsorDetailsModal();
    }
    
    // Remplir les données du sponsor
    document.getElementById('sponsorId').textContent = sponsor.id;
    document.getElementById('sponsorName').textContent = sponsor.name || 'N/A';
    document.getElementById('sponsorEmail').textContent = sponsor.email || 'N/A';
    document.getElementById('sponsorPhone').textContent = sponsor.phone || 'N/A';
    document.getElementById('sponsorCompany').textContent = sponsor.company_name || 'N/A';
    document.getElementById('sponsorWebsite').textContent = sponsor.website || 'N/A';
    document.getElementById('sponsorAddress').textContent = sponsor.address || 'N/A';
    document.getElementById('sponsorCity').textContent = sponsor.city || 'N/A';
    document.getElementById('sponsorBio').textContent = sponsor.bio || 'N/A';
    document.getElementById('sponsorStatus').textContent = window.getStatusText(sponsor.status);
    document.getElementById('sponsorStatus').className = `badge badge-${window.getStatusClass(sponsor.status)}`;
    document.getElementById('sponsorCreatedAt').textContent = window.formatDate(sponsor.created_at);
    document.getElementById('sponsorUpdatedAt').textContent = window.formatDate(sponsor.updated_at);
    
    // Gérer l'image de profil
    const profileImage = document.getElementById('sponsorProfileImage');
    if (sponsor.profile_image) {
        profileImage.src = `/storage/${sponsor.profile_image}`;
        profileImage.style.display = 'block';
    } else {
        profileImage.style.display = 'none';
    }
    
    // Gérer le logo
    const logoImage = document.getElementById('sponsorLogo');
    if (sponsor.logo) {
        logoImage.src = `/storage/${sponsor.logo}`;
        logoImage.style.display = 'block';
    } else {
        logoImage.style.display = 'none';
    }
    
    // Afficher la modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function createSponsorDetailsModal() {
    const modalHtml = `
        <div class="modal fade" id="sponsorDetailsModal" tabindex="-1" aria-labelledby="sponsorDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="sponsorDetailsModalLabel">
                            <i class="fas fa-user"></i> Détails du Sponsor
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- Informations personnelles -->
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-user"></i> Informations Personnelles</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <img id="sponsorProfileImage" src="" alt="Photo de profil" class="img-thumbnail" style="width: 100px; height: 100px; object-fit: cover; display: none;">
                                        </div>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>ID:</strong></td>
                                                <td id="sponsorId">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Nom:</strong></td>
                                                <td id="sponsorName">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Email:</strong></td>
                                                <td id="sponsorEmail">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Téléphone:</strong></td>
                                                <td id="sponsorPhone">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Statut:</strong></td>
                                                <td><span id="sponsorStatus" class="badge">-</span></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Informations entreprise -->
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-building"></i> Informations Entreprise</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="text-center mb-3">
                                            <img id="sponsorLogo" src="" alt="Logo entreprise" class="img-thumbnail" style="width: 80px; height: 80px; object-fit: contain; display: none;">
                                        </div>
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Entreprise:</strong></td>
                                                <td id="sponsorCompany">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Site Web:</strong></td>
                                                <td id="sponsorWebsite">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Adresse:</strong></td>
                                                <td id="sponsorAddress">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Ville:</strong></td>
                                                <td id="sponsorCity">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Description -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Description</h6>
                            </div>
                            <div class="card-body">
                                <p id="sponsorBio" class="mb-0">-</p>
                            </div>
                        </div>
                        
                        <!-- Dates -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-calendar"></i> Informations Temporelles</h6>
                            </div>
                            <div class="card-body">
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Inscrit le:</strong></td>
                                        <td id="sponsorCreatedAt">-</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Modifié le:</strong></td>
                                        <td id="sponsorUpdatedAt">-</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modal au body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    return document.getElementById('sponsorDetailsModal');
}

window.filterSponsors = function() {
    const search = document.getElementById('searchSponsor').value;
    const status = document.getElementById('statusFilter').value;
    
    // Filtrer les lignes du tableau
    const rows = document.querySelectorAll('#sponsorsTableBody tr');
    rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        const email = row.cells[1].textContent.toLowerCase();
        const company = row.cells[2].textContent.toLowerCase();
        const statusCell = row.cells[3].textContent.toLowerCase();
        
        const matchesSearch = !search || name.includes(search.toLowerCase()) || email.includes(search.toLowerCase()) || company.includes(search.toLowerCase());
        const matchesStatus = !status || statusCell.includes(status.toLowerCase());
        
        row.style.display = matchesSearch && matchesStatus ? '' : 'none';
    });
};

// Fonctions utilitaires globales
window.getStatusClass = function(status) {
    const classes = {
        'pending': 'warning',
        'approved': 'success',
        'rejected': 'danger',
        'active': 'primary',
        'inactive': 'secondary'
    };
    return classes[status] || 'secondary';
};

window.getStatusText = function(status) {
    const texts = {
        'pending': 'En attente',
        'approved': 'Approuvé',
        'rejected': 'Rejeté',
        'active': 'Actif',
        'inactive': 'Inactif'
    };
    return texts[status] || status;
};

window.formatDate = function(dateString) {
    return new Date(dateString).toLocaleDateString('fr-FR');
};

// ==================== GESTION DES CAMPAGNES ====================

// Charger les campagnes au chargement de la page
window.loadCampaigns = async function() {
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const response = await fetch('/admin/campaigns/data', {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (response.ok) {
            const campaigns = await response.json();
            console.log('Campagnes chargées:', campaigns);
            displayCampaigns(campaigns);
        } else {
            console.error('Erreur lors du chargement des campagnes:', response.status);
            displayTestCampaigns();
        }
    } catch (error) {
        console.error('Erreur:', error);
        displayTestCampaigns();
    }
};

// Afficher les campagnes dans le tableau
function displayCampaigns(campaigns) {
    const tbody = document.getElementById('campaignsTableBody');
    tbody.innerHTML = '';

    campaigns.forEach(campaign => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td><strong>${campaign.title || campaign.name}</strong></td>
            <td><span class="badge badge-info">${getCampaignTypeText(campaign.type || 'event')}</span></td>
            <td><span class="badge badge-${getCampaignStatusClass(campaign.status)}">${getCampaignStatusText(campaign.status)}</span></td>
            <td>${window.formatDate(campaign.start_date || campaign.created_at)}</td>
            <td>${window.formatDate(campaign.end_date || campaign.created_at)}</td>
            <td>${formatCurrency(campaign.budget || 0)}</td>
            <td><span class="badge badge-primary">${getSponsorsCount(campaign.id)}</span></td>
            <td>
                <button class="btn btn-sm btn-outline-primary" onclick="viewCampaign(${campaign.id})" title="Voir détails">
                    <i class="fas fa-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-warning" onclick="editCampaign(${campaign.id})" title="Modifier">
                    <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-outline-info" onclick="managePackages(${campaign.id})" title="Gérer packages">
                    <i class="fas fa-box"></i>
                </button>
                ${campaign.status !== 'completed' && campaign.status !== 'cancelled' ? `
                    <button class="btn btn-sm btn-outline-${campaign.status === 'active' ? 'warning' : 'success'}" onclick="toggleCampaignStatus(${campaign.id}, '${campaign.status}')" title="${campaign.status === 'active' ? 'Pauser' : 'Activer'}">
                        <i class="fas fa-${campaign.status === 'active' ? 'pause' : 'play'}"></i>
                    </button>
                ` : ''}
                <button class="btn btn-sm btn-outline-danger" onclick="deleteCampaign(${campaign.id})" title="Supprimer">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

// Données de test pour les campagnes
function displayTestCampaigns() {
    const testCampaigns = [
        { 
            id: 1, 
            title: 'Festival Écologique 2024', 
            type: 'festival', 
            status: 'active', 
            start_date: '2024-06-15', 
            end_date: '2024-06-18', 
            budget: 50000,
            created_at: '2024-01-15'
        },
        { 
            id: 2, 
            title: 'Conférence Tech 2024', 
            type: 'conference', 
            status: 'draft', 
            start_date: '2024-09-20', 
            end_date: '2024-09-22', 
            budget: 25000,
            created_at: '2024-02-10'
        },
        { 
            id: 3, 
            title: 'Tournoi de Football', 
            type: 'sport', 
            status: 'completed', 
            start_date: '2024-03-01', 
            end_date: '2024-03-03', 
            budget: 15000,
            created_at: '2024-01-20'
        }
    ];
    displayCampaigns(testCampaigns);
}

// Fonctions utilitaires pour les campagnes
function getCampaignTypeText(type) {
    const types = {
        'event': 'Événement',
        'festival': 'Festival',
        'conference': 'Conférence',
        'sport': 'Sport',
        'culture': 'Culture'
    };
    return types[type] || 'Événement';
}

function getCampaignStatusClass(status) {
    const classes = {
        'draft': 'secondary',
        'active': 'success',
        'paused': 'warning',
        'completed': 'info',
        'cancelled': 'danger'
    };
    return classes[status] || 'secondary';
}

function getCampaignStatusText(status) {
    const texts = {
        'draft': 'Brouillon',
        'active': 'Active',
        'paused': 'En pause',
        'completed': 'Terminée',
        'cancelled': 'Annulée'
    };
    return texts[status] || status;
}

function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'TND'
    }).format(amount);
}

function getSponsorsCount(campaignId) {
    // Pour l'instant, retourner un nombre aléatoire
    // Plus tard, on fera un appel API pour récupérer le vrai nombre
    return Math.floor(Math.random() * 10);
}

// Actions sur les campagnes
window.viewCampaign = async function(id) {
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/campaigns/${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            }
        });

        if (response.ok) {
            const campaign = await response.json();
            showCampaignDetailsModal(campaign);
        } else {
            alert('Erreur lors du chargement des détails: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

window.editCampaign = function(id) {
    alert(`Modifier la campagne ID: ${id}\n\nFonctionnalité à implémenter.`);
};

window.managePackages = function(id) {
    alert(`Gérer les packages pour la campagne ID: ${id}\n\nFonctionnalité à implémenter.`);
};

window.toggleCampaignStatus = async function(id, currentStatus) {
    const action = currentStatus === 'active' ? 'pauser' : 'activer';
    if (!confirm(`${action.charAt(0).toUpperCase() + action.slice(1)} cette campagne ?`)) return;
    
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const response = await fetch(`/admin/campaigns/${id}/toggle-status`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Authorization': 'Bearer ' + token,
                'X-CSRF-TOKEN': csrfToken,
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            alert(`Campagne ${action} avec succès !`);
            window.loadCampaigns();
        } else {
            alert('Erreur lors du changement de statut: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

window.deleteCampaign = async function(id) {
    if (!confirm('Supprimer définitivement cette campagne ?\n\nCette action supprimera :\n• La campagne\n• Tous ses packages\n• Toutes les propositions de sponsoring')) return;
    
    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';
        
        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', csrfToken);
        
        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        };
        
        if (token) {
            headers['Authorization'] = 'Bearer ' + token;
        }
        
        const response = await fetch(`/admin/campaigns/${id}`, {
            method: 'POST',
            headers: headers,
            body: formData
        });

        if (response.ok) {
            alert('Campagne supprimée avec succès !');
            window.loadCampaigns();
        } else {
            const errorData = await response.text();
            console.error('Erreur response:', errorData);
            alert('Erreur lors de la suppression: ' + response.status);
        }
    } catch (error) {
        console.error('Erreur:', error);
        alert('Erreur de connexion: ' + error.message);
    }
};

// ==================== Création d'une nouvelle campagne ====================
window.createCampaign = function() {
    // Créer/afficher la modal de création
    let modal = document.getElementById('createCampaignModal');
    if (!modal) {
        modal = createCampaignModal();
    }
    // Reset du formulaire
    const form = document.getElementById('createCampaignForm');
    if (form) form.reset();
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
};

function createCampaignModal() {
    const modalHtml = `
        <div class="modal fade" id="createCampaignModal" tabindex="-1" aria-labelledby="createCampaignModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="createCampaignModalLabel">
                            <i class="fas fa-plus"></i> Nouvelle Campagne
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="createCampaignForm">
                            <div class="mb-3">
                                <label class="form-label">Titre <span class="text-danger">*</span></label>
                                <input name="title" type="text" class="form-control" placeholder="Titre de la campagne" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="Décrivez brièvement la campagne"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Type <span class="text-danger">*</span></label>
                                <select name="type" class="form-control" required>
                                    <option value="event">Événement</option>
                                    <option value="festival">Festival</option>
                                    <option value="conference">Conférence</option>
                                    <option value="sport">Sport</option>
                                    <option value="culture">Culture</option>
                                </select>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date début <span class="text-danger">*</span></label>
                                    <input name="start_date" type="date" class="form-control" placeholder="AAAA-MM-JJ" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Date fin <span class="text-danger">*</span></label>
                                    <input name="end_date" type="date" class="form-control" placeholder="AAAA-MM-JJ" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Budget (TND)</label>
                                    <input name="budget" type="number" step="0.01" min="0" class="form-control" placeholder="ex: 10000">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Statut <span class="text-danger">*</span></label>
                                    <select name="status" class="form-control" required>
                                        <option value="draft">Brouillon</option>
                                        <option value="active">Active</option>
                                        <option value="paused">En pause</option>
                                        <option value="completed">Terminée</option>
                                        <option value="cancelled">Annulée</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Localisation</label>
                                <input name="location" type="text" class="form-control" placeholder="Ville, lieu, adresse...">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Image</label>
                                <input name="image" type="file" accept="image/*" class="form-control">
                            </div>
                        </form>
                        <div id="createCampaignErrors" class="alert alert-danger d-none"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button id="submitCreateCampaignBtn" type="button" class="btn btn-success">
                            <i class="fas fa-save"></i> Créer
                        </button>
                    </div>
                </div>
            </div>
        </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Binder le submit
    document.getElementById('submitCreateCampaignBtn').addEventListener('click', submitCreateCampaign);
    return document.getElementById('createCampaignModal');
}

async function submitCreateCampaign() {
    const form = document.getElementById('createCampaignForm');
    const errorBox = document.getElementById('createCampaignErrors');
    errorBox.classList.add('d-none');
    errorBox.innerHTML = '';

    const formData = new FormData(form);

    try {
        const token = localStorage.getItem('jwt_token');
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
                         document.querySelector('input[name="_token"]')?.value ||
                         '{{ csrf_token() }}';

        const headers = {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest'
        };
        if (token) headers['Authorization'] = 'Bearer ' + token;

        const response = await fetch('/admin/campaigns', {
            method: 'POST',
            headers,
            body: formData
        });

        if (response.ok) {
            // Succès
            const modalEl = document.getElementById('createCampaignModal');
            const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
            modal.hide();
            alert('Campagne créée avec succès !');
            window.loadCampaigns();
        } else {
            // Afficher erreurs
            let message = 'Erreur lors de la création (' + response.status + ')';
            try {
                const data = await response.json();
                if (data?.errors) {
                    message = Object.values(data.errors).flat().join('<br>');
                } else if (data?.message) {
                    message = data.message;
                }
            } catch (e) {}
            errorBox.innerHTML = message;
            errorBox.classList.remove('d-none');
        }
    } catch (error) {
        errorBox.innerHTML = 'Erreur de connexion: ' + error.message;
        errorBox.classList.remove('d-none');
    }
}

window.filterCampaigns = function() {
    const search = document.getElementById('searchCampaign').value;
    const status = document.getElementById('statusCampaignFilter').value;
    const type = document.getElementById('typeCampaignFilter').value;
    
    // Filtrer les lignes du tableau
    const rows = document.querySelectorAll('#campaignsTableBody tr');
    rows.forEach(row => {
        const name = row.cells[0].textContent.toLowerCase();
        const typeCell = row.cells[1].textContent.toLowerCase();
        const statusCell = row.cells[2].textContent.toLowerCase();
        
        const matchesSearch = !search || name.includes(search.toLowerCase());
        const matchesStatus = !status || statusCell.includes(status.toLowerCase());
        const matchesType = !type || typeCell.includes(type.toLowerCase());
        
        row.style.display = matchesSearch && matchesStatus && matchesType ? '' : 'none';
    });
};

// Modal pour afficher les détails d'une campagne
function showCampaignDetailsModal(campaign) {
    // Créer la modal si elle n'existe pas
    let modal = document.getElementById('campaignDetailsModal');
    if (!modal) {
        modal = createCampaignDetailsModal();
    }
    
    // Remplir les données de la campagne
    document.getElementById('campaignTitle').textContent = campaign.title || campaign.name || 'N/A';
    document.getElementById('campaignType').textContent = getCampaignTypeText(campaign.type);
    document.getElementById('campaignStatus').textContent = getCampaignStatusText(campaign.status);
    document.getElementById('campaignStatus').className = `badge badge-${getCampaignStatusClass(campaign.status)}`;
    document.getElementById('campaignDescription').textContent = campaign.description || 'N/A';
    document.getElementById('campaignStartDate').textContent = window.formatDate(campaign.start_date);
    document.getElementById('campaignEndDate').textContent = window.formatDate(campaign.end_date);
    document.getElementById('campaignBudget').textContent = formatCurrency(campaign.budget || 0);
    document.getElementById('campaignLocation').textContent = campaign.location || 'N/A';
    document.getElementById('campaignCreatedAt').textContent = window.formatDate(campaign.created_at);
    document.getElementById('campaignUpdatedAt').textContent = window.formatDate(campaign.updated_at);
    
    // Afficher la modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

function createCampaignDetailsModal() {
    const modalHtml = `
        <div class="modal fade" id="campaignDetailsModal" tabindex="-1" aria-labelledby="campaignDetailsModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title" id="campaignDetailsModalLabel">
                            <i class="fas fa-bullhorn"></i> Détails de la Campagne
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-info-circle"></i> Informations Générales</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Titre:</strong></td>
                                                <td id="campaignTitle">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Type:</strong></td>
                                                <td id="campaignType">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Statut:</strong></td>
                                                <td><span id="campaignStatus" class="badge">-</span></td>
                                            </tr>
                                            <tr>
                                                <td><strong>Budget:</strong></td>
                                                <td id="campaignBudget">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Localisation:</strong></td>
                                                <td id="campaignLocation">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-calendar"></i> Dates</h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td><strong>Date début:</strong></td>
                                                <td id="campaignStartDate">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Date fin:</strong></td>
                                                <td id="campaignEndDate">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Créée le:</strong></td>
                                                <td id="campaignCreatedAt">-</td>
                                            </tr>
                                            <tr>
                                                <td><strong>Modifiée le:</strong></td>
                                                <td id="campaignUpdatedAt">-</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-align-left"></i> Description</h6>
                            </div>
                            <div class="card-body">
                                <p id="campaignDescription" class="mb-0">-</p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fas fa-times"></i> Fermer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    // Ajouter la modal au body
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    
    return document.getElementById('campaignDetailsModal');
}

// Charger les campagnes au chargement de la page
window.loadCampaigns();
</script>
@endpush
@endsection