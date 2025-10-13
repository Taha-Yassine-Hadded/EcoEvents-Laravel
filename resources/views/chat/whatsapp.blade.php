<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Messages</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        html, body {
            height: 100%;
            background: linear-gradient(135deg, #075e54 0%, #128c7e 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .chat-app { height: 100vh; }
        .chat-left {
            border-right: 1px solid #ddd;
            background: #f0f0f0;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }
        .chat-search {
            padding: 15px;
            background: white;
            border-bottom: 1px solid #e0e0e0;
        }
        .chat-search input {
            border-radius: 20px;
            border: 1px solid #ddd;
            padding: 8px 15px;
            font-size: 14px;
        }
        .list {
            height: calc(100vh - 140px);
            overflow-y: auto;
            background: white;
        }
        .conv-item, .contact-item {
            cursor: pointer;
            border-bottom: 1px solid #f0f0f0;
            padding: 12px 15px;
            transition: all 0.2s ease;
        }
        .conv-item:hover, .contact-item:hover {
            background: #f8f9fa;
        }
        .conv-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 2px;
        }
        .conv-snippet {
            font-size: .85rem;
            color: #666;
            margin-top: 2px;
        }
        .conv-time {
            font-size: .75rem;
            color: #999;
            margin-left: auto;
        }
        .conv-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 12px;
            border: 2px solid #e0e0e0;
        }
        .badge-unread {
            background: #25d366;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            min-width: 18px;
            text-align: center;
        }
        .chat-right iframe {
            width: 100%;
            height: 100vh;
            border: 0;
            background: #fff;
        }
        .empty {
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            background: #e5ddd5;
            background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23f0f0f0' fill-opacity='0.1'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }
        .nav-tabs .nav-link {
            border: none;
            border-radius: 0;
            color: #666;
            font-weight: 500;
        }
        .nav-tabs .nav-link.active {
            color: #25d366;
            border-bottom: 2px solid #25d366;
            background: none;
        }
        .nav-tabs {
            border-bottom: 1px solid #e0e0e0;
            background: white;
        }
        .header-whatsapp {
            background: #075e54;
            color: white;
            padding: 15px;
        }
    </style>
</head>
<body>
<div class="container-fluid chat-app">
    <div class="row h-100">
        <!-- Left column: Conversations / Contacts -->
        <div class="col-12 col-md-4 col-lg-4 chat-left d-flex flex-column p-0">
            <div class="header-whatsapp d-flex justify-content-between align-items-center">
                <div class="fw-semibold" style="font-size: 1.1em;">Discussions</div>
                <div class="small">EcoEvents</div>
            </div>

            <ul class="nav nav-tabs px-3 pt-3" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="tab-conv" data-bs-toggle="tab" data-bs-target="#pane-conv" type="button" role="tab">Conversations</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="tab-contacts" data-bs-toggle="tab" data-bs-target="#pane-contacts" type="button" role="tab">Contacts</button>
                </li>
            </ul>

            <div class="chat-search">
                <input id="search" type="text" class="form-control form-control-sm" placeholder="Rechercher…">
            </div>

            <div class="tab-content flex-grow-1">
                <!-- Conversations list -->
                <div class="tab-pane fade show active" id="pane-conv" role="tabpanel">
                    <div id="convList" class="list list-group list-group-flush small">
                        <div class="p-3 text-secondary">Chargement…</div>
                    </div>
                </div>
                <!-- Contacts list -->
                <div class="tab-pane fade" id="pane-contacts" role="tabpanel">
                    <div id="contactList" class="list list-group list-group-flush small">
                        <div class="p-3 text-secondary">Chargement…</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right column: Room -->
        <div class="col-12 col-md-8 col-lg-8 chat-right p-0">
            <div id="roomContainer" class="h-100">
                <div class="empty">Choisissez une conversation ou un contact…</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function(){
    const jwt = localStorage.getItem('jwt_token');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const convList = document.getElementById('convList');
    const contactList = document.getElementById('contactList');
    const roomContainer = document.getElementById('roomContainer');
    const searchInput = document.getElementById('search');
    const tabContacts = document.getElementById('tab-contacts');

    function openRoom(roomId){
        roomContainer.innerHTML = `<iframe src="/ui/chat/rooms/${roomId}" referrerpolicy="no-referrer-when-downgrade"></iframe>`;
    }

    // Load conversations
    async function loadConversations(){
        try{
            const res = await axios.get('/chat/my-rooms', { headers: { ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) } });
            const rooms = res.data?.rooms || [];
            if(rooms.length === 0){
                convList.innerHTML = `
                    <div class="text-center p-4">
                        <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                        <div class="text-muted">Aucune conversation</div>
                        <small class="text-muted">Vos conversations apparaîtront ici</small>
                    </div>
                `;
                return;
            }
            convList.innerHTML = '';
            rooms.forEach(r=>{
                const a = document.createElement('div');
                a.className = 'conv-item d-flex align-items-center';

                // Déterminer le nom et l'avatar
                let displayName = r.name || 'Conversation';
                let avatarSrc = '/storage/profiles/default.jpg';

                // Si c'est une communauté, essayer d'obtenir l'image
                if (r.target_type === 'community' && r.target_id) {
                    displayName = `Chat - ${r.community_name || 'Communauté'}`;
                    avatarSrc = r.community_image || '/storage/profiles/default.jpg';
                }

                // Formatage de l'heure
                let timeDisplay = '';
                if (r.last && r.last.created_at) {
                    const date = new Date(r.last.created_at);
                    const now = new Date();
                    const diff = now - date;

                    if (diff < 24 * 60 * 60 * 1000) { // Aujourd'hui
                        timeDisplay = date.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
                    } else if (diff < 7 * 24 * 60 * 60 * 1000) { // Cette semaine
                        timeDisplay = date.toLocaleDateString('fr-FR', { weekday: 'short' });
                    } else {
                        timeDisplay = date.toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit' });
                    }
                }

                a.innerHTML = `
                    <img src="${avatarSrc}" class="conv-avatar" onerror="this.src='/storage/profiles/default.jpg'">
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="conv-title">${escapeHtml(displayName)}</div>
                            <div class="d-flex align-items-center">
                                ${timeDisplay ? `<span class="conv-time">${timeDisplay}</span>` : ''}
                                ${r.unread_count ? `<span class="badge-unread ms-2">${r.unread_count}</span>` : ''}
                            </div>
                        </div>
                        <div class="conv-snippet text-truncate">
                            ${escapeHtml(r.last?.user?.name ? (r.last.user.name + ': ') : '')}${escapeHtml(r.last?.content || 'Aucun message')}
                        </div>
                    </div>
                `;
                a.addEventListener('click', ()=> openRoom(r.id));
                convList.appendChild(a);
            });
        }catch(e){
            convList.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                    <div class="text-danger">Erreur de chargement</div>
                    <small class="text-muted">Code: ${e?.response?.status || 'Inconnu'}</small>
                </div>
            `;
        }
    }

    // Load contacts
    async function loadContacts(scope, q){
        try{
            const url = new URL(location.origin + '/chat/contacts');
            if(scope) url.searchParams.set('scope', scope);
            if(q) url.searchParams.set('search', q);
            const res = await axios.get(url.toString(), { headers: { ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) } });
            const contacts = res.data?.contacts || [];
            if(contacts.length === 0){
                contactList.innerHTML = `
                    <div class="text-center p-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <div class="text-muted">Aucun contact</div>
                        <small class="text-muted">Vos contacts apparaîtront ici</small>
                    </div>
                `;
                return;
            }
            contactList.innerHTML = '';
            contacts.forEach(c=>{
                const a = document.createElement('div');
                a.className = 'contact-item d-flex align-items-center';
                a.innerHTML = `
                    <img src="${c.profile_image || '/storage/profiles/default.jpg'}" class="conv-avatar" onerror="this.src='/storage/profiles/default.jpg'">
                    <div class="flex-grow-1">
                        <div class="conv-title">${escapeHtml(c.name || ('Utilisateur #' + c.id))}</div>
                        <div class="conv-snippet">
                            ${c.email ? escapeHtml(c.email) : 'Membre de la communauté'}
                        </div>
                    </div>
                `;
                a.addEventListener('click', ()=> startOneToOne(c.id));
                contactList.appendChild(a);
            });
        }catch(e){
            contactList.innerHTML = `
                <div class="text-center p-4">
                    <i class="fas fa-exclamation-triangle fa-2x text-danger mb-3"></i>
                    <div class="text-danger">Erreur de chargement</div>
                    <small class="text-muted">Code: ${e?.response?.status || 'Inconnu'}</small>
                </div>
            `;
        }
    }

    // Start or open 1-1
    async function startOneToOne(userId){
        try{
            const res = await axios.post('/chat/one-to-one', { user_id: userId }, {
                headers: { 'X-CSRF-TOKEN': csrf, ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) },
                withCredentials: true,
            });
            const roomId = res.data?.room?.id;
            if(roomId){ openRoom(roomId); }
        }catch(e){
            alert('Erreur ouverture conversation: '+(e?.response?.status||''));
        }
    }

    function escapeHtml(s){
        const map = { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#039;' };
        return String(s||'').replace(/[&<>"']/g, m => map[m] || m);
    }

    // Search behavior: in Conversations, filter client-side; in Contacts, switch to scope=all when query not empty
    searchInput.addEventListener('input', (e)=>{
        const q = e.target.value.trim();
        const activePane = document.querySelector('.tab-pane.active');
        if(activePane && activePane.id === 'pane-contacts'){
            loadContacts(q? 'all':'community', q);
        } else {
            // For conversations, we reload list (server doesn’t filter by search here)
            loadConversations();
            if(q){
                // quick client-side filter
                Array.from(convList.children).forEach(el=>{
                    const t = el.textContent || '';
                    el.style.display = t.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
                });
            }
        }
    });

    // First loads
    loadConversations();
    tabContacts.addEventListener('shown.bs.tab', ()=> loadContacts('community',''));
})();
</script>
</body>
</html>
