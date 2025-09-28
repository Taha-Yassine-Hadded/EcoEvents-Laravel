@extends('layouts.app')

@section('title', '{{ $campaign->title }} - Echofy')

@section('content')
    <!--==================================================-->
    <!-- Start Echofy Campaign Detail Area -->
    <!--==================================================-->
    <section class="campaign-detail-area home-six">
        <div class="container">
            <div class="campaign-hero">
                <div class="campaign-image">
                    <img src="{{ !empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']) && \Illuminate\Support\Facades\Storage::disk('public')->exists($campaign->media_urls['images'][0]) ? \Illuminate\Support\Facades\Storage::url($campaign->media_urls['images'][0]) : asset('assets/images/home6/placeholder.jpg') }}" alt="{{ $campaign->title }}">
                    <div class="campaign-status">{{ ucfirst($campaign->status) }}</div>
                </div>

                <div class="campaign-info">
                    <div class="campaign-meta">
                        <span class="campaign-category">{{ ucfirst($campaign->category) }}</span>
                        <div class="campaign-dates">
                            <div class="date-item">
                                <i class="bi bi-calendar2-event"></i>
                                <span>Début: {{ $campaign->start_date->format('d/m/Y') }}</span>
                            </div>
                            <div class="date-item">
                                <i class="bi bi-calendar2-check"></i>
                                <span>Fin: {{ $campaign->end_date->format('d/m/Y') }}</span>
                            </div>
                        </div>
                    </div>

                    <h1 class="campaign-title">{{ $campaign->title }}</h1>

                    <p class="campaign-description">
                        {{ strip_tags($campaign->content) }}
                    </p>

                    <div class="campaign-stats">
                        <div class="stat-item">
                            <i class="bi bi-eye"></i>
                            <div class="stat-number">{{ $campaign->views_count ?? 0 }}</div>
                            <div class="stat-label">Vues</div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-heart"></i>
                            <div class="stat-number">{{ $campaign->likes_count ?? 0 }}</div>
                            <div class="stat-label">J'aime</div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-chat"></i>
                            <div class="stat-number">{{ $campaign->comments_count ?? 0 }}</div>
                            <div class="stat-label">Commentaires</div>
                        </div>
                        <div class="stat-item">
                            <i class="bi bi-share"></i>
                            <div class="stat-number">{{ $campaign->shares_count ?? 0 }}</div>
                            <div class="stat-label">Partages</div>
                        </div>
                    </div>

                    <div class="campaign-actions">
                        <button class="action-btn btn-primary {{ $user && $user->likes()->where('campaign_id', $campaign->id)->exists() ? 'liked' : '' }}" onclick="toggleLike(this, {{ $campaign->id }})">
                            <i class="bi bi-heart" style="{{ $user && $user->likes()->where('campaign_id', $campaign->id)->exists() ? 'color: #dc3545' : '' }}"></i>
                            <span>{{ $user && $user->likes()->where('campaign_id', $campaign->id)->exists() ? 'Ne plus aimer' : 'J\'aime cette campagne' }}</span>
                        </button>
                        <button class="action-btn btn-secondary" onclick="openShareModal()">
                            <i class="bi bi-share"></i>
                            Partager
                        </button>
                    </div>
                </div>
            </div>

            <div class="campaign-content">
                <div class="content-section">
                    <h3>Objectifs et Actions</h3>
                    @if (!empty($campaign->objectives))
                        <h4>Objectifs de la campagne</h4>
                        <ul>
                            @foreach ($campaign->objectives as $objective)
                                <li>{{ $objective }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if (!empty($campaign->actions))
                        <h4>Actions concrètes</h4>
                        <ul>
                            @foreach ($campaign->actions as $action)
                                <li>{{ $action }}</li>
                            @endforeach
                        </ul>
                    @endif
                    @if (empty($campaign->objectives) && empty($campaign->actions))
                        <p>Aucune information disponible.</p>
                    @endif
                </div>

                <div class="content-section">
                    <h3>Médias supplémentaires</h3>
                    @if (!empty($campaign->media_urls['videos']) || !empty($campaign->media_urls['website']))
                        @if (!empty($campaign->media_urls['videos']))
                            <h4>Vidéos</h4>
                            @foreach ($campaign->media_urls['videos'] as $video)
                                <div class="video-container">
                                    @if (preg_match('/youtube\.com|youtu\.be/', $video))
                                        <iframe width="100%" height="315" src="{{ preg_replace('/watch\?v=/', 'embed/', $video) }}" frameborder="0" allowfullscreen></iframe>
                                    @elseif (preg_match('/vimeo\.com/', $video))
                                        <iframe width="100%" height="315" src="{{ preg_replace('/vimeo\.com\/(\d+)/', 'player.vimeo.com/video/$1', $video) }}" frameborder="0" allowfullscreen></iframe>
                                    @else
                                        <a href="{{ $video }}" target="_blank">Voir la vidéo</a>
                                    @endif
                                </div>
                            @endforeach
                        @endif
                        @if (!empty($campaign->media_urls['website']))
                            <h4>Site web</h4>
                            <p><a href="{{ $campaign->media_urls['website'] }}" target="_blank">{{ $campaign->media_urls['website'] }}</a></p>
                        @endif
                    @else
                        <p>Aucun média supplémentaire disponible.</p>
                    @endif
                </div>

                <div class="content-section">
                    <h3>Contact</h3>
                    @if ($campaign->contact_info)
                        <p>{{ $campaign->contact_info }}</p>
                    @else
                        <p>Aucune information de contact disponible.</p>
                    @endif
                </div>

                <div class="content-section">
                    <h3>Galerie photos</h3>
                    <div class="media-gallery">
                        @if (!empty($campaign->media_urls['images']) && is_array($campaign->media_urls['images']))
                            @foreach ($campaign->media_urls['images'] as $image)
                                @if (\Illuminate\Support\Facades\Storage::disk('public')->exists($image))
                                    <div class="media-item">
                                        <img src="{{ \Illuminate\Support\Facades\Storage::url($image) }}" alt="Image de la campagne">
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <p>Aucune image disponible.</p>
                        @endif
                    </div>
                </div>
            </div>

            <div class="comments-section">
                <div class="comments-header">
                    <h3>Commentaires ({{ $campaign->comments_count ?? 0 }})</h3>
                </div>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                @if ($errors->has('error'))
                    <div class="alert alert-danger">
                        {{ $errors->first('error') }}
                    </div>
                @endif

                @if ($user)
                    <div class="comment-form">
                        <form action="{{ route('front.campaigns.comments.store', $campaign->id) }}" method="POST">
                            @csrf
                            <textarea name="content" class="comment-input" placeholder="Partagez votre expérience ou posez une question..." required></textarea>
                            <button type="submit" class="comment-submit">
                                <i class="bi bi-send"></i> Publier le commentaire
                            </button>
                        </form>
                    </div>
                @else
                    <p class="text-center">
                        <a href="{{ route('login') }}">Connectez-vous</a> pour ajouter un commentaire.
                    </p>
                @endif

                <div class="comments-list">
                    @forelse ($campaign->comments as $comment)
                        <div class="comment" id="comment-{{ $comment->id }}">
                            <div class="comment-header">
                                <span class="comment-author">{{ $comment->user->name }}</span>
                                <span class="comment-date">{{ $comment->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="comment-content" id="comment-content-{{ $comment->id }}">
                                {{ $comment->content }}
                            </div>
                            @if ($user && $comment->user_id === $user->id)
                                <div class="comment-actions">
                                    <button class="comment-action-btn edit-btn" onclick="showEditForm({{ $comment->id }})" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('front.campaigns.comments.delete', [$campaign->id, $comment->id]) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="comment-action-btn delete-btn" onclick="return confirm('Voulez-vous vraiment supprimer ce commentaire ?')" title="Supprimer">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                <div class="comment-edit-form" id="edit-form-{{ $comment->id }}" style="display: none;">
                                    <form action="{{ route('front.campaigns.comments.update', [$campaign->id, $comment->id]) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <textarea name="content" class="comment-input" required>{{ $comment->content }}</textarea>
                                        <div class="comment-edit-actions">
                                            <button type="submit" class="comment-submit">
                                                <i class="bi bi-save"></i> Enregistrer
                                            </button>
                                            <button type="button" class="comment-cancel-btn" onclick="hideEditForm({{ $comment->id }})">
                                                <i class="bi bi-x"></i> Annuler
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endif
                        </div>
                    @empty
                        <p class="text-center">Aucun commentaire pour le moment.</p>
                    @endforelse
                </div>
            </div>

            <!-- Modal de partage -->
            <div class="share-modal" id="shareModal">
                <div class="share-content">
                    <div class="share-header">
                        <h3>Partager cette campagne</h3>
                        <button class="close-modal" onclick="closeShareModal()">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="share-options">
                        <a href="#" class="share-option facebook" onclick="shareOnFacebook()">
                            <i class="bi bi-facebook"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="#" class="share-option twitter" onclick="shareOnTwitter()">
                            <i class="bi bi-twitter"></i>
                            <span>Twitter</span>
                        </a>
                        <a href="#" class="share-option linkedin" onclick="shareOnLinkedIn()">
                            <i class="bi bi-linkedin"></i>
                            <span>LinkedIn</span>
                        </a>
                        <a href="#" class="share-option whatsapp" onclick="shareOnWhatsApp()">
                            <i class="bi bi-whatsapp"></i>
                            <span>WhatsApp</span>
                        </a>
                        <a href="#" class="share-option copy" onclick="copyLink()">
                            <i class="bi bi-link"></i>
                            <span>Copier le lien</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--==================================================-->
    <!-- End Echofy Campaign Detail Area -->
    <!--==================================================-->
@endsection

@push('styles')
    <style>
        .campaign-detail-area.home-six {
            padding: 20px 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .campaign-hero {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .campaign-image {
            width: 100%;
            max-height: 400px;
            position: relative;
            overflow: visible;
        }

        .campaign-image img {
            width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: cover;
            display: block;
        }

        .campaign-status {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            color: white;
            background: rgba(40, 167, 69, 0.9);
        }

        .campaign-info {
            padding: 2rem;
        }

        .campaign-meta {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .campaign-category {
            display: inline-block;
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
        }

        .campaign-dates {
            display: flex;
            gap: 2rem;
            color: #666;
            font-size: 0.95rem;
        }

        .date-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .date-item i {
            color: #28a745;
        }

        .campaign-title {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 1rem;
            font-weight: 700;
            line-height: 1.2;
        }

        .campaign-description {
            font-size: 1.1rem;
            color: #666;
            margin-bottom: 2rem;
            line-height: 1.7;
        }

        .campaign-stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-item i {
            font-size: 1.5rem;
            color: #28a745;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }

        .stat-label {
            font-size: 0.9rem;
            color: #666;
        }

        .campaign-actions {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .action-btn {
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 25px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(40, 167, 69, 0.4);
        }

        .btn-primary.liked {
            background: linear-gradient(135deg, #dc3545, #c82333);
        }

        .btn-primary.liked:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(220, 53, 69, 0.4);
        }

        .btn-secondary {
            background: white;
            color: #28a745;
            border: 2px solid #28a745;
        }

        .btn-secondary:hover {
            background: #28a745;
            color: white;
        }

        .campaign-content {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .content-section {
            margin-bottom: 2rem;
        }

        .content-section h3 {
            color: #28a745;
            margin-bottom: 1rem;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .content-section h4 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .content-section p, .content-section ul {
            color: #555;
            line-height: 1.7;
            margin-bottom: 1rem;
        }

        .content-section ul {
            padding-left: 1.5rem;
        }

        .video-container {
            margin-bottom: 1rem;
        }

        .video-container iframe {
            border-radius: 10px;
        }

        .media-gallery {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .media-item {
            border-radius: 10px;
            overflow: hidden;
            aspect-ratio: 16/9;
        }

        .media-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .comments-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .comments-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .comments-header h3 {
            color: #333;
            font-size: 1.3rem;
        }

        .comment-form {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .comment-input {
            width: 100%;
            border: none;
            padding: 1rem;
            border-radius: 8px;
            resize: vertical;
            min-height: 100px;
            margin-bottom: 1rem;
            font-family: inherit;
        }

        .comment-input:focus {
            outline: 2px solid #28a745;
        }

        .comment-submit {
            background: #28a745;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .comment-submit:hover {
            background: #218838;
        }

        .comment-cancel-btn {
            background: #6c757d;
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .comment-cancel-btn:hover {
            background: #5a6268;
        }

        .comments-list {
            max-height: 500px;
            overflow-y: auto;
        }

        .comment {
            background: #f8f9fa;
            padding: 1.5rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .comment-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .comment-author {
            font-weight: 600;
            color: #333;
        }

        .comment-date {
            color: #888;
            font-size: 0.9rem;
        }

        .comment-content {
            color: #555;
            line-height: 1.6;
        }

        .comment-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .comment-action-btn {
            background: none;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .edit-btn i {
            color: #28a745;
        }

        .edit-btn:hover i {
            color: #218838;
        }

        .delete-btn i {
            color: #dc3545;
        }

        .delete-btn:hover i {
            color: #c82333;
        }

        .comment-edit-form {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 8px;
            margin-top: 1rem;
        }

        .comment-edit-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 0.5rem;
        }

        .share-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .share-modal.active {
            display: flex;
        }

        .share-content {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            max-width: 500px;
            width: 90%;
        }

        .share-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .share-header h3 {
            color: #333;
        }

        .close-modal {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }

        .share-options {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
        }

        .share-option {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.5rem;
            padding: 1rem;
            border: 2px solid #eee;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
        }

        .share-option:hover {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.05);
        }

        .share-option i {
            font-size: 2rem;
        }

        .share-option.facebook i { color: #1877f2; }
        .share-option.twitter i { color: #1da1f2; }
        .share-option.linkedin i { color: #0077b5; }
        .share-option.whatsapp i { color: #25d366; }
        .share-option.copy i { color: #28a745; }

        .alert {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 8px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        @media (max-width: 768px) {
            .campaign-title {
                font-size: 2rem;
            }

            .campaign-meta {
                flex-direction: column;
                align-items: flex-start;
            }

            .campaign-dates {
                flex-direction: column;
                gap: 0.5rem;
            }

            .campaign-stats {
                justify-content: space-around;
                gap: 1rem;
            }

            .campaign-actions {
                flex-direction: column;
            }

            .action-btn {
                justify-content: center;
            }

            .video-container iframe {
                height: 200px;
            }

            .campaign-image {
                max-height: 300px;
            }

            .campaign-image img {
                max-height: 300px;
            }
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Toggle like
        function toggleLike(button, campaignId) {
            const token = localStorage.getItem('jwt_token');
            if (!token) {
                alert('Vous devez être connecté pour aimer une campagne.');
                window.location.href = '{{ route("login") }}';
                return;
            }

            fetch('{{ route("api.campaigns.like", ["campaign" => $campaign->id]) }}', {
                method: 'POST',
                headers: {
                    'Authorization': `Bearer ${token}`,
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.classList.toggle('liked');
                        const icon = button.querySelector('i');
                        const text = button.querySelector('span');
                        const stats = button.closest('.campaign-actions').previousElementSibling.querySelector('.stat-item:nth-child(2) .stat-number');
                        stats.textContent = data.likes_count;

                        if (data.action === 'liked') {
                            button.classList.add('liked');
                            icon.style.color = '#dc3545';
                            text.textContent = 'Ne plus aimer';
                        } else {
                            button.classList.remove('liked');
                            icon.style.color = '';
                            text.textContent = 'J\'aime cette campagne';
                        }
                    } else {
                        alert(data.error || 'Erreur lors de l\'action');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue');
                });
        }

        // Gestion du formulaire de modification des commentaires
        function showEditForm(commentId) {
            document.getElementById('comment-content-' + commentId).style.display = 'none';
            document.getElementById('edit-form-' + commentId).style.display = 'block';
        }

        function hideEditForm(commentId) {
            document.getElementById('comment-content-' + commentId).style.display = 'block';
            document.getElementById('edit-form-' + commentId).style.display = 'none';
        }

        // Modal de partage
        function openShareModal() {
            document.getElementById('shareModal').classList.add('active');
        }

        function closeShareModal() {
            document.getElementById('shareModal').classList.remove('active');
        }

        // Fonctions de partage
        function shareOnFacebook() {
            const url = encodeURIComponent(window.location.href);
            const title = encodeURIComponent('{{ $campaign->title }} - Echofy');
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank');
            closeShareModal();
        }

        function shareOnTwitter() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('Découvrez cette campagne environnementale sur Echofy : {{ $campaign->title }}');
            window.open(`https://twitter.com/intent/tweet?url=${url}&text=${text}`, '_blank');
            closeShareModal();
        }

        function shareOnLinkedIn() {
            const url = encodeURIComponent(window.location.href);
            window.open(`https://www.linkedin.com/sharing/share-offsite/?url=${url}`, '_blank');
            closeShareModal();
        }

        function shareOnWhatsApp() {
            const url = encodeURIComponent(window.location.href);
            const text = encodeURIComponent('Découvrez cette campagne environnementale : {{ $campaign->title }}');
            window.open(`https://wa.me/?text=${text} ${url}`, '_blank');
            closeShareModal();
        }

        function copyLink() {
            navigator.clipboard.writeText(window.location.href).then(() => {
                alert('Lien copié dans le presse-papiers !');
                closeShareModal();
            });
        }

        // Fermer le modal en cliquant à l'extérieur
        document.getElementById('shareModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeShareModal();
            }
        });
    </script>
@endpush
