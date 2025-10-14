<div class="simple-recommendations">
    <div class="recommendations-header">
        <h3 class="recommendations-title">
            <i class="fas fa-lightbulb text-warning me-2"></i>
            Communautés Écologiques Populaires 💚
        </h3>
        <p class="recommendations-subtitle">Découvrez les communautés les plus actives</p>
    </div>

    <div class="recommendations-container">
        <!-- Popular Communities -->
        <div class="row">
            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-fire text-danger me-2"></i>
                    Communautés populaires
                </h5>
                <div id="popular-communities">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <h5 class="mb-3">
                    <i class="fas fa-clock text-info me-2"></i>
                    Communautés récentes
                </h5>
                <div id="recent-communities">
                    <div class="text-center">
                        <div class="spinner-border spinner-border-sm text-primary" role="status">
                            <span class="visually-hidden">Chargement...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sample Communities -->
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="mb-3">
                    <i class="fas fa-star text-warning me-2"></i>
                    Communautés recommandées
                </h5>
                <div class="row" id="sample-communities">
                    <!-- Sample communities will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.simple-recommendations {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 15px;
    padding: 2rem;
    margin: 2rem 0;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
}

.recommendations-header {
    text-align: center;
    margin-bottom: 2rem;
}

.recommendations-title {
    color: #2c5530;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.recommendations-subtitle {
    color: #6c757d;
    font-size: 0.95rem;
}

.community-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-left: 4px solid #28a745;
}

.community-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.community-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 1rem;
}

.community-info h6 {
    color: #2c5530;
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.community-category {
    background: #e8f5e8;
    color: #2c5530;
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
}

.community-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.member-count {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.member-count i {
    color: #28a745;
}

.btn-join {
    background: linear-gradient(45deg, #28a745, #20c997);
    border: none;
    color: white;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-join:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    color: white;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadSampleCommunities();
    loadPopularCommunities();
    loadRecentCommunities();
});

function loadSampleCommunities() {
    const sampleCommunities = [
        {
            id: 1,
            name: 'Énergies Vertes Paris',
            category: 'Énergies Renouvelables',
            description: 'Communauté dédiée aux énergies renouvelables et à la transition écologique',
            memberCount: 245,
            keywords: ['énergie', 'solaire', 'renouvelable'],
            location: 'Paris'
        },
        {
            id: 2,
            name: 'Zéro Déchet Lyon',
            category: 'Recyclage & Zéro Déchet',
            description: 'Réduction des déchets et mode de vie durable',
            memberCount: 189,
            keywords: ['recyclage', 'zéro déchet', 'compostage'],
            location: 'Lyon'
        },
        {
            id: 3,
            name: 'Biodiversité Marseille',
            category: 'Biodiversité & Nature',
            description: 'Protection de la biodiversité et de la nature',
            memberCount: 156,
            keywords: ['biodiversité', 'nature', 'protection'],
            location: 'Marseille'
        }
    ];

    const container = document.getElementById('sample-communities');

    container.innerHTML = sampleCommunities.map(community => `
        <div class="col-md-4">
            <div class="community-card">
                <div class="community-header">
                    <div class="community-info">
                        <h6>${community.name}</h6>
                        <span class="community-category">${community.category}</span>
                    </div>
                </div>

                <p class="text-muted small mb-2">${community.description}</p>

                <div class="community-meta">
                    <span class="member-count">
                        <i class="fas fa-users"></i>
                        ${community.memberCount} membres
                    </span>
                    <span class="text-muted small">${community.location}</span>
                </div>

                <div class="mt-3">
                    <button class="btn btn-join w-100" onclick="joinCommunity(${community.id})">
                        <i class="fas fa-plus me-1"></i>
                        Rejoindre
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

function loadPopularCommunities() {
    // Simulate loading popular communities
    setTimeout(() => {
        const container = document.getElementById('popular-communities');
        container.innerHTML = `
            <div class="community-card">
                <h6>Énergies Vertes Paris</h6>
                <p class="text-muted small">245 membres • Paris</p>
            </div>
            <div class="community-card">
                <h6>Zéro Déchet Lyon</h6>
                <p class="text-muted small">189 membres • Lyon</p>
            </div>
            <div class="community-card">
                <h6>Biodiversité Marseille</h6>
                <p class="text-muted small">156 membres • Marseille</p>
            </div>
        `;
    }, 1000);
}

function loadRecentCommunities() {
    // Simulate loading recent communities
    setTimeout(() => {
        const container = document.getElementById('recent-communities');
        container.innerHTML = `
            <div class="community-card">
                <h6>Transport Durable Toulouse</h6>
                <p class="text-muted small">Créée il y a 2 jours • Toulouse</p>
            </div>
            <div class="community-card">
                <h6>Agriculture Bio Nantes</h6>
                <p class="text-muted small">Créée il y a 5 jours • Nantes</p>
            </div>
            <div class="community-card">
                <h6>Climat Bordeaux</h6>
                <p class="text-muted small">Créée il y a 1 semaine • Bordeaux</p>
            </div>
        `;
    }, 1500);
}

function joinCommunity(communityId) {
    alert(`Fonctionnalité de rejoindre la communauté ${communityId} - À implémenter`);
    console.log('Joining community:', communityId);
}
</script>
