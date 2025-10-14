@extends('layouts.app')

@section('title', 'Recommandations de Communautés - EcoEvents')

@section('content')
<div class="container-fluid py-5">
    <div class="container">
        <!-- Header Section -->
        <div class="row mb-5">
            <div class="col-12 text-center">
                <h1 class="display-4 fw-bold text-success mb-3">
                    <i class="fas fa-lightbulb me-3"></i>
                    Découvrez vos Communautés Idéales
                </h1>
                <p class="lead text-muted">
                    Notre IA analyse vos centres d'intérêt pour vous recommander les communautés écologiques parfaites
                </p>
            </div>
        </div>

        <!-- Recommendations Component -->
        <div class="row">
            <div class="col-12">
                @include('components.simple-recommendations')
            </div>
        </div>

        <!-- Additional Info Section -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h4 class="card-title text-center mb-4">
                            <i class="fas fa-robot text-primary me-2"></i>
                            Comment fonctionne notre IA ?
                        </h4>

                        <div class="row">
                            <div class="col-md-4 text-center mb-4">
                                <div class="feature-icon bg-primary bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-search"></i>
                                </div>
                                <h5>Analyse des Intérêts</h5>
                                <p class="text-muted small">Notre IA analyse vos messages et activités pour identifier vos centres d'intérêt écologiques.</p>
                            </div>

                            <div class="col-md-4 text-center mb-4">
                                <div class="feature-icon bg-success bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h5>Matching Intelligent</h5>
                                <p class="text-muted small">Nous comparons vos préférences avec les mots-clés et caractéristiques des communautés.</p>
                            </div>

                            <div class="col-md-4 text-center mb-4">
                                <div class="feature-icon bg-info bg-gradient text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                    <i class="fas fa-star"></i>
                                </div>
                                <h5>Recommandations Personnalisées</h5>
                                <p class="text-muted small">Recevez des suggestions adaptées à votre profil et vos objectifs écologiques.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Call to Action -->
        <div class="row mt-5">
            <div class="col-12 text-center">
                <div class="bg-light rounded-3 p-5">
                    <h3 class="mb-3">Prêt à rejoindre une communauté ?</h3>
                    <p class="text-muted mb-4">Explorez nos recommandations et trouvez votre communauté écologique idéale</p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="/organizer/communities" class="btn btn-success btn-lg">
                            <i class="fas fa-users me-2"></i>
                            Voir toutes les communautés
                        </a>
                        <a href="/organizer/communities/create" class="btn btn-outline-success btn-lg">
                            <i class="fas fa-plus me-2"></i>
                            Créer une communauté
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Custom Styles -->
<style>
.hero-section {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    padding: 4rem 0;
    margin-bottom: 3rem;
}

.feature-icon {
    font-size: 1.5rem;
}

.recommendation-card {
    transition: all 0.3s ease;
}

.recommendation-card:hover {
    transform: translateY(-5px);
}

.btn-lg {
    padding: 0.75rem 2rem;
    font-weight: 600;
}

@media (max-width: 768px) {
    .display-4 {
        font-size: 2.5rem;
    }

    .lead {
        font-size: 1.1rem;
    }
}
</style>

<!-- JavaScript for enhanced interactions -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            document.querySelector(this.getAttribute('href')).scrollIntoView({
                behavior: 'smooth'
            });
        });
    });

    // Add animation on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.card, .feature-icon').forEach(el => {
        el.style.opacity = '0';
        el.style.transform = 'translateY(20px)';
        el.style.transition = 'all 0.6s ease';
        observer.observe(el);
    });
});
</script>
@endsection
