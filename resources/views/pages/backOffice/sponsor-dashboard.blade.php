@extends('layouts.sponsor')

@section('title', 'Dashboard Sponsor - Echofy')

@section('content')
<!-- Welcome Section -->
<div class="welcome-section">
    <h4><i class="fas fa-handshake"></i> Bienvenue, {{ $user->name }} !</h4>
    <p>En tant que sponsor, vous pouvez découvrir et soutenir des événements écologiques. Explorez les campagnes disponibles et proposez votre soutien financier.</p>
</div>

<!-- Statistics Cards Row -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-calendar"></i>
            </div>
            <div class="stats-number text-primary">{{ $stats['total_events_available'] ?? 0 }}</div>
            <div class="stats-label">Événements disponibles</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number text-success">{{ $stats['upcoming_events'] ?? 0 }}</div>
            <div class="stats-label">À venir</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-play-circle"></i>
            </div>
            <div class="stats-number text-success">{{ $stats['ongoing_events'] ?? 0 }}</div>
            <div class="stats-label">En cours</div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-tags"></i>
            </div>
            <div class="stats-number text-warning">{{ $stats['total_categories'] ?? 0 }}</div>
            <div class="stats-label">Catégories</div>
        </div>
    </div>
</div>

<!-- Sponsoring Statistics -->
<div class="row mb-4">
    <div class="col-12">
        <h5 class="mb-3"><i class="fas fa-handshake"></i> Mes Sponsoring</h5>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #007bff 0%, #6610f2 100%);">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stats-number text-primary">{{ $sponsorshipStats['total_proposals'] ?? 0 }}</div>
            <div class="stats-label">Total Propositions</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stats-number text-warning">{{ $sponsorshipStats['pending_proposals'] ?? 0 }}</div>
            <div class="stats-label">En Attente</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stats-number text-success">{{ $sponsorshipStats['approved_proposals'] ?? 0 }}</div>
            <div class="stats-label">Approuvés</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #dc3545 0%, #e83e8c 100%);">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stats-number text-danger">{{ $sponsorshipStats['rejected_proposals'] ?? 0 }}</div>
            <div class="stats-label">Rejetés</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #17a2b8 0%, #6f42c1 100%);">
                <i class="fas fa-euro-sign"></i>
            </div>
            <div class="stats-number text-info">{{ number_format($sponsorshipStats['total_invested'] ?? 0, 0, ',', ' ') }} €</div>
            <div class="stats-label">Total Investi</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6 mb-3">
        <div class="stats-card">
            <div class="stats-icon" style="background: linear-gradient(135deg, #6c757d 0%, #495057 100%);">
                <i class="fas fa-list"></i>
            </div>
            <div class="stats-number text-secondary">Voir Tout</div>
            <div class="stats-label">
                <a href="{{ route('sponsor.sponsorships') }}" class="text-decoration-none">Mes Sponsoring</a>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions Row -->
<div class="row mb-4">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-calendar-alt fa-3x text-primary mb-3"></i>
            <h5>Campagnes</h5>
            <p class="text-muted">Découvrez les événements à sponsoriser</p>
            <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                <i class="fas fa-eye"></i> Voir les Campagnes
            </a>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-user-edit fa-3x text-success mb-3"></i>
            <h5>Mon Profil</h5>
            <p class="text-muted">Gérez vos informations personnelles</p>
            <a href="{{ route('sponsor.profile') }}" class="btn btn-success">
                <i class="fas fa-edit"></i> Modifier le Profil
            </a>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-building fa-3x text-primary mb-3"></i>
            <h5>Mon Entreprise</h5>
            <p class="text-muted">Gérez les informations de votre entreprise</p>
            <a href="{{ route('sponsor.company') }}" class="btn btn-primary">
                <i class="fas fa-building"></i> Gérer l'Entreprise
            </a>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-file-contract fa-3x text-warning mb-3"></i>
            <h5>Mes Sponsorships</h5>
            <p class="text-muted">Suivez vos accords de sponsoring</p>
                <a href="{{ route('sponsor.sponsorships') }}" class="btn btn-warning">
                    <i class="fas fa-list"></i> Voir mes Sponsorships
                </a>
        </div>
    </div>
    
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="action-card">
            <i class="fas fa-chart-line fa-3x text-info mb-3"></i>
            <h5>Statistiques</h5>
            <p class="text-muted">Analysez vos performances</p>
                <a href="{{ route('sponsor.statistics') }}" class="btn btn-info">
                    <i class="fas fa-chart-bar"></i> Voir les Stats
                </a>
        </div>
    </div>
</div>

<!-- Mes Sponsoring Acceptés -->
@if(isset($sponsorships) && $sponsorships->where('status', 'approved')->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="campaign-card">
            <div class="card-body py-4">
                <h5 class="mb-4"><i class="fas fa-check-circle text-success"></i> Mes Sponsoring Acceptés</h5>
                <div class="row">
                    @foreach($sponsorships->where('status', 'approved')->take(3) as $sponsorship)
                        <div class="col-md-4 mb-3">
                            <div class="p-3 border rounded h-100 bg-light">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0 text-success">{{ $sponsorship->event->title ?? 'Événement' }}</h6>
                                    <span class="badge bg-success">Approuvé</span>
                                </div>
                                <div class="text-muted small mb-2">
                                    <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($sponsorship->event->date ?? now())->format('d/m/Y') }}
                                </div>
                                <div class="mb-2">
                                    <strong class="text-primary">{{ $sponsorship->package_name }}</strong>
                                    <div class="text-success font-weight-bold">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</div>
                                </div>
                                @if($sponsorship->notes)
                                    <p class="text-muted small mb-2">{{ Str::limit($sponsorship->notes, 60) }}</p>
                                @endif
                                <div class="text-muted small">
                                    <i class="fas fa-clock"></i> Accepté le {{ $sponsorship->updated_at->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                @if($sponsorships->where('status', 'approved')->count() > 3)
                    <div class="text-center mt-3">
                        <a href="{{ route('sponsor.sponsorships') }}" class="btn btn-outline-success">
                            <i class="fas fa-list"></i> Voir tous mes sponsoring acceptés
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endif

<!-- Featured Campaigns Row -->
<div class="row">
    <div class="col-12">
        <div class="campaign-card">
            @if(isset($events) && count($events))
                <div class="card-body py-4">
                    <h5 class="mb-4">Événements à sponsoriser</h5>
                    <div class="row">
                        @foreach($events as $event)
                            <div class="col-md-6 col-lg-4 mb-3">
                                <div class="p-3 border rounded h-100">
                                    <h6 class="mb-1">{{ $event->title }}</h6>
                                    <small class="text-muted">{{ optional($event->category)->name }} • {{ optional($event->date)->format('d/m/Y H:i') }}</small>
                                    <p class="mt-2 mb-0 text-muted" style="font-size: 13px;">{{ Str::limit($event->description, 90) }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="text-center mt-3">
                        <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                            <i class="fas fa-eye"></i> Voir tous les événements
                        </a>
                    </div>
                </div>
            @else
                <div class="card-body text-center py-5">
                    <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Aucun événement disponible pour le moment</h4>
                    <p class="text-muted">Revenez plus tard pour découvrir de nouveaux événements à sponsoriser.</p>
                    <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                        <i class="fas fa-refresh"></i> Actualiser
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection