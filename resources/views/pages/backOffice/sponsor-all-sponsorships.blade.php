@extends('layouts.sponsor')

@section('title', 'Toutes mes Propositions - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-list text-primary"></i>
        Toutes mes Propositions
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Toutes mes Propositions</li>
        </ol>
    </nav>
</div>

<!-- Description -->
<div class="alert alert-warning">
    <i class="fas fa-info-circle"></i>
    <strong>Historique complet :</strong> Cette page affiche toutes vos propositions de sponsoring avec tous leurs statuts (en attente, approuvées, rejetées, etc.).
    <a href="{{ route('sponsor.sponsorships') }}" class="alert-link">Voir uniquement les sponsoring acceptés</a>
</div>

@if($sponsorships->count() > 0)
    <!-- Sponsorships List -->
    <div class="row">
        @foreach($sponsorships as $sponsorship)
            <div class="col-lg-6 col-xl-4 mb-4">
                <div class="card shadow h-100">
                    <div class="card-header py-3 d-flex justify-content-between align-items-center">
                        <h6 class="card-title mb-0">
                            <i class="fas fa-calendar-alt text-primary"></i>
                            {{ $sponsorship->event->title ?? 'Événement supprimé' }}
                        </h6>
                        <span class="badge badge-{{ 
                            $sponsorship->status === 'approved' ? 'success' : 
                            ($sponsorship->status === 'pending' ? 'warning' : 
                            ($sponsorship->status === 'rejected' ? 'danger' : 
                            ($sponsorship->status === 'completed' ? 'info' : 'secondary'))) 
                        }}">
                            {{ ucfirst($sponsorship->status) }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="sponsorship-details">
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Package</small>
                                    <div class="font-weight-bold text-primary">{{ $sponsorship->package_name }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Montant</small>
                                    <div class="font-weight-bold text-success">{{ number_format($sponsorship->amount, 0, ',', ' ') }} €</div>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Date de l'événement</small>
                                    <div>{{ \Carbon\Carbon::parse($sponsorship->event->date ?? now())->format('d/m/Y') }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Date de proposition</small>
                                    <div>{{ \Carbon\Carbon::parse($sponsorship->created_at)->format('d/m/Y') }}</div>
                                </div>
                            </div>
                            
                            @if($sponsorship->notes)
                                <div class="notes-section mb-3">
                                    <small class="text-muted">Notes</small>
                                    <div class="p-2 bg-light rounded small">
                                        {{ $sponsorship->notes }}
                                    </div>
                                </div>
                            @endif
                            
                            <div class="event-preview mb-3">
                                <strong><i class="fas fa-info-circle text-primary"></i> Événement :</strong>
                                <div class="mt-1 p-2 bg-light rounded small">
                                    {{ Str::limit($sponsorship->event->description ?? 'Aucune description disponible', 100) }}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('sponsor.campaign.details', $sponsorship->event->id ?? '#') }}" 
                               class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-eye"></i> Voir l'Événement
                            </a>
                            
                            @if($sponsorship->status === 'approved')
                                <span class="text-success">
                                    <i class="fas fa-check-circle"></i> Accepté le {{ $sponsorship->updated_at->format('d/m/Y') }}
                                </span>
                            @elseif($sponsorship->status === 'pending')
                                <span class="text-warning">
                                    <i class="fas fa-clock"></i> En attente
                                </span>
                            @elseif($sponsorship->status === 'rejected')
                                <span class="text-danger">
                                    <i class="fas fa-times-circle"></i> Rejeté le {{ $sponsorship->updated_at->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-secondary">
                                    <i class="fas fa-info-circle"></i> {{ ucfirst($sponsorship->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    @if($sponsorships->hasPages())
        <div class="d-flex justify-content-center mt-4">
            {{ $sponsorships->links() }}
        </div>
    @endif
@else
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body text-center py-5">
                    <i class="fas fa-handshake fa-4x text-muted mb-4"></i>
                    <h4 class="text-muted">Aucune proposition pour le moment</h4>
                    <p class="text-muted">Vous n'avez pas encore proposé de sponsoring. Explorez les événements disponibles et proposez votre soutien !</p>
                    <a href="{{ route('sponsor.campaigns') }}" class="btn btn-primary">
                        <i class="fas fa-calendar-alt"></i> Explorer les Événements
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
