@extends('layouts.sponsor')

@section('title', 'Mes Campagnes - Echofy Sponsor')

@section('content')
<!-- Content Header -->
<div class="content-header">
    <h1 class="page-title">
        <i class="fas fa-calendar-alt text-primary"></i>
        Campagnes Disponibles
    </h1>
    <nav class="breadcrumb-nav">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">Sponsor</li>
            <li class="breadcrumb-item active">Campagnes</li>
        </ol>
    </nav>
</div>

<!-- Content -->
<div class="row">
    <div class="col-12">
        <div class="card shadow">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list"></i> Toutes les Campagnes
                </h5>
            </div>
            <div class="card-body">
                @if($campaigns->count() > 0)
                    <div class="row">
                        @foreach($campaigns as $campaign)
                            <div class="col-lg-4 col-md-6 mb-4">
                                <div class="card h-100 campaign-card">
                                    @if($campaign->media_urls && isset($campaign->media_urls['images']) && count($campaign->media_urls['images']) > 0)
                                        <img src="{{ asset('storage/' . $campaign->media_urls['images'][0]) }}" class="card-img-top" alt="{{ $campaign->title }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="fas fa-calendar-alt fa-3x text-muted"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $campaign->title }}</h5>
                                        <p class="card-text">{{ Str::limit($campaign->description, 100) }}</p>
                                        <div class="campaign-meta">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar"></i> {{ \Carbon\Carbon::parse($campaign->start_date)->format('d/m/Y') }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-tag"></i> {{ $campaign->category ?? 'Non spécifiée' }}
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-eye"></i> {{ $campaign->views_count ?? 0 }} vues
                                            </small>
                                        </div>
                                    </div>
                                    <div class="card-footer">
                                        <a href="{{ route('sponsor.campaign.details', $campaign->id) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i> Détails
                                        </a>
                                        <a href="{{ route('sponsor.campaign.details', $campaign->id) }}" class="btn btn-success btn-sm">
                                            <i class="fas fa-handshake"></i> Sponsoriser
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Pagination -->
                    @if($campaigns->hasPages())
                        <div class="row">
                            <div class="col-12">
                                <nav aria-label="Pagination des campagnes">
                                    {{ $campaigns->links() }}
                                </nav>
                            </div>
                        </div>
                    @endif
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-times fa-4x text-muted mb-4"></i>
                        <h3 class="text-muted">Aucune campagne disponible</h3>
                        <p class="text-muted">Les campagnes seront bientôt disponibles pour le sponsoring.</p>
                        <a href="{{ route('sponsor.dashboard') }}" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Retour au Dashboard
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
