<div class="community-recommendations">
    <div class="recommendations-header">
        <h3 class="recommendations-title">
            <i class="fas fa-lightbulb text-warning me-2"></i>
            Communautés que vous pourriez aimer 💚
        </h3>
        <p class="recommendations-subtitle">Basé sur vos centres d'intérêt écologiques</p>
    </div>

    <div class="recommendations-container">
        <!-- Loading state -->
        <div id="recommendations-loading" class="text-center py-4">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Chargement...</span>
            </div>
            <p class="mt-2 text-muted">Analyse de vos préférences...</p>
        </div>

        <!-- Recommendations content -->
        <div id="recommendations-content" class="d-none">
            <div class="row" id="recommendations-list">
                <!-- Recommendations will be loaded here -->
            </div>
        </div>

        <!-- Error state -->
        <div id="recommendations-error" class="d-none">
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Impossible de charger les recommandations pour le moment.
            </div>
        </div>
    </div>

    <!-- Popular and Recent Communities -->
    <div class="additional-communities mt-4">
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
    </div>
</div>

<style>
.community-recommendations {
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

.recommendation-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 1rem;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border-left: 4px solid #28a745;
}

.recommendation-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
}

.recommendation-header {
    display: flex;
    justify-content: between;
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

.match-score {
    background: linear-gradient(45deg, #28a745, #20c997);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 600;
    font-size: 0.9rem;
    text-align: center;
    min-width: 80px;
}

.recommendation-reasons {
    margin-top: 1rem;
}

.reasons-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
}

.reason-tag {
    background: #f8f9fa;
    color: #495057;
    padding: 0.25rem 0.5rem;
    border-radius: 15px;
    font-size: 0.8rem;
    margin-right: 0.5rem;
    margin-bottom: 0.25rem;
    display: inline-block;
}

.community-actions {
    margin-top: 1rem;
    display: flex;
    gap: 0.5rem;
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

.btn-details {
    background: transparent;
    border: 2px solid #28a745;
    color: #28a745;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-details:hover {
    background: #28a745;
    color: white;
}

.popular-community, .recent-community {
    background: white;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 0.75rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.06);
    transition: all 0.3s ease;
}

.popular-community:hover, .recent-community:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    loadRecommendations();
    loadPopularCommunities();
    loadRecentCommunities();
});

function loadRecommendations() {
    fetch('/organizer/communities/recommendations', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('recommendations-loading').classList.add('d-none');

        if (data.success && data.recommendations.length > 0) {
            displayRecommendations(data.recommendations);
            document.getElementById('recommendations-content').classList.remove('d-none');
        } else {
            document.getElementById('recommendations-error').classList.remove('d-none');
        }
    })
    .catch(error => {
        console.error('Error loading recommendations:', error);
        document.getElementById('recommendations-loading').classList.add('d-none');
        document.getElementById('recommendations-error').classList.remove('d-none');
    });
}

function displayRecommendations(recommendations) {
    const container = document.getElementById('recommendations-list');

    recommendations.forEach(rec => {
        const community = rec.community;
        const reasons = rec.reasons || [];

        const card = document.createElement('div');
        card.className = 'col-md-6 col-lg-4';
        card.innerHTML = `
            <div class="recommendation-card">
                <div class="recommendation-header">
                    <div class="community-info">
                        <h6>${community.name}</h6>
                        <span class="community-category">${community.category}</span>
                    </div>
                    <div class="match-score">
                        ${rec.match_percentage}% match
                    </div>
                </div>

                <p class="text-muted small mb-2">${community.description || 'Communauté écologique active'}</p>

                ${reasons.length > 0 ? `
                    <div class="recommendation-reasons">
                        <div class="reasons-title">Pourquoi cette recommandation :</div>
                        ${reasons.map(reason => `<span class="reason-tag">${reason}</span>`).join('')}
                    </div>
                ` : ''}

                <div class="community-actions">
                    <button class="btn btn-join" onclick="joinCommunity(${community.id})">
                        <i class="fas fa-plus me-1"></i>
                        Rejoindre
                    </button>
                    <button class="btn btn-details" onclick="viewCommunity(${community.id})">
                        <i class="fas fa-eye me-1"></i>
                        Détails
                    </button>
                </div>
            </div>
        `;

        container.appendChild(card);
    });
}

function loadPopularCommunities() {
    fetch('/organizer/communities/popular', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPopularCommunities(data.communities);
        }
    })
    .catch(error => console.error('Error loading popular communities:', error));
}

function loadRecentCommunities() {
    fetch('/organizer/communities/recent', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Authorization': 'Bearer ' + localStorage.getItem('jwt_token')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayRecentCommunities(data.communities);
        }
    })
    .catch(error => console.error('Error loading recent communities:', error));
}

function displayPopularCommunities(communities) {
    const container = document.getElementById('popular-communities');

    if (communities.length === 0) {
        container.innerHTML = '<p class="text-muted">Aucune communauté populaire trouvée</p>';
        return;
    }

    container.innerHTML = communities.map(community => `
        <div class="popular-community">
            <h6 class="mb-1">${community.name}</h6>
            <p class="text-muted small mb-2">${community.category}</p>
            <div class="community-meta">
                <span class="member-count">
                    <i class="fas fa-users"></i>
                    ${community.members_count || 0} membres
                </span>
                <button class="btn btn-sm btn-outline-success" onclick="viewCommunity(${community.id})">
                    Voir
                </button>
            </div>
        </div>
    `).join('');
}

function displayRecentCommunities(communities) {
    const container = document.getElementById('recent-communities');

    if (communities.length === 0) {
        container.innerHTML = '<p class="text-muted">Aucune communauté récente trouvée</p>';
        return;
    }

    container.innerHTML = communities.map(community => `
        <div class="recent-community">
            <h6 class="mb-1">${community.name}</h6>
            <p class="text-muted small mb-2">${community.category}</p>
            <div class="community-meta">
                <span class="text-muted small">
                    Créée ${new Date(community.created_at).toLocaleDateString()}
                </span>
                <button class="btn btn-sm btn-outline-success" onclick="viewCommunity(${community.id})">
                    Voir
                </button>
            </div>
        </div>
    `).join('');
}

function joinCommunity(communityId) {
    // Implementation for joining a community
    console.log('Joining community:', communityId);
    // Add your join logic here
}

function viewCommunity(communityId) {
    // Implementation for viewing community details
    console.log('Viewing community:', communityId);
    // Add your view logic here
}
</script>
