<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat – Salon #{{ $room->id }}</title>
    <!-- Reverb config via meta -->
    <meta name="reverb-key" content="{{ env('VITE_REVERB_APP_KEY', env('REVERB_APP_KEY')) }}">
    <meta name="reverb-host" content="{{ env('VITE_REVERB_HOST', env('REVERB_HOST', 'localhost')) }}">
    <meta name="reverb-port" content="{{ env('VITE_REVERB_PORT', env('REVERB_PORT', 8080)) }}">
    <meta name="reverb-scheme" content="{{ env('VITE_REVERB_SCHEME', env('REVERB_SCHEME', 'http')) }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f8f9fa; }
        .msg { padding:.5rem .75rem; border-radius:.5rem; background:#fff; margin-bottom:.5rem; }
        .msg.me { background:#e7f5ff; }
        .messages { height: 60vh; overflow-y:auto; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Messages</h5>
        <a href="/" class="btn btn-outline-secondary btn-sm">Accueil</a>
    </div>

    <div id="messages" class="card">
        <div class="card-body messages" id="messagesBody">
            <div class="text-muted">Chargement…</div>
        </div>
    </div>

    <form id="sendForm" class="mt-3">
        <div class="input-group">
            <input type="text" class="form-control" name="content" id="content" placeholder="Votre message…" maxlength="2000" required />
        </div>
        <div id="sendMsg" class="small ms-1 mt-1 text-muted"></div>
    </form>
</div>

    <!-- Axios -->
    <script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"
        onerror="this.onerror=null; this.src='https://unpkg.com/axios@1.6.8/dist/axios.min.js';"></script>
    <!-- Echo IIFE (inclut compat) avec fallback CDN -->
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@2/dist/echo.iife.js"
        onerror="this.onerror=null; this.src='https://unpkg.com/laravel-echo@2/dist/echo.iife.js';"></script>
    <!-- Reverb browser client (registre le connecteur 'reverb') avec fallback CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@laravel/reverb@1/dist/reverb.iife.js"
        onerror="this.onerror=null; this.src='https://unpkg.com/@laravel/reverb@1/dist/reverb.iife.js';"></script>
<script>
(function(){
    const roomId = {{ $room->id }};
    const jwt = localStorage.getItem('jwt_token');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
{{ ... }}
    // Initialiser Echo pour Reverb (lire depuis meta)
    const REVERB_KEY = document.querySelector('meta[name="reverb-key"]').getAttribute('content');
    const REVERB_HOST = document.querySelector('meta[name="reverb-host"]').getAttribute('content');
    const REVERB_PORT = Number(document.querySelector('meta[name="reverb-port"]').getAttribute('content') || '8080');
    const REVERB_SCHEME = document.querySelector('meta[name="reverb-scheme"]').getAttribute('content') || 'http';

    const EchoCtor = (typeof window.Echo === 'function') ? window.Echo : (window.EchoLib && window.EchoLib.Echo ? window.EchoLib.Echo : null);
    if (!EchoCtor) {
        console.error('Laravel Echo library not loaded');
        return;
    }
    const echo = new EchoCtor({
        broadcaster: 'reverb',
        key: REVERB_KEY,
        wsHost: REVERB_HOST,
        wsPort: REVERB_PORT,
        wssPort: REVERB_PORT,
        forceTLS: (REVERB_SCHEME === 'https'),
        enabledTransports: ['ws','wss'],
        authorizer: (channel) => ({
            authorize: (socketId, callback) => {
                axios.post('/broadcasting/auth', { socket_id: socketId, channel_name: channel.name }, {
                    headers: { 'X-CSRF-TOKEN': csrf, 'X-Requested-With': 'XMLHttpRequest', ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) },
                    withCredentials: true,
                }).then(r=>callback(false, r.data)).catch(err=>callback(true, err));
            },
        }),
    });

    const messagesBody = document.getElementById('messagesBody');
    function appendMsg(m){
        const me = Number(m.user?.id) === Number({{ auth()->id() ?? 'null' }});
        const el = document.createElement('div');
        el.innerHTML = `<strong>${(m.user?.name||'#'+m.user?.id||'user')}</strong> · <small class="text-muted">${new Date(m.created_at||Date.now()).toLocaleString()}</small><br>${escapeHtml(m.content||'')}`;
        messagesBody.appendChild(el);
        messagesBody.scrollTop = messagesBody.scrollHeight;
    }
    function escapeHtml(s){
        const map = { '&':'&amp;','<':'&lt;','>':'&gt;','\"':'&quot;','\'':'&#039;' };
        return String(s).replace(/[&<>"']/g, m => map[m] || m);
    }

    // Charger l'historique
    async function loadHistory(){
        try{
            const res = await axios.get(`/chat/rooms/${roomId}/messages`, { headers: { ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) } });
            messagesBody.innerHTML = '';
            const items = res.data?.messages?.data || [];
            items.reverse().forEach(appendMsg);
        }catch(e){
            messagesBody.innerHTML = `<div class="alert alert-danger">Erreur chargement (${e?.response?.status||''})</div>`;
        }
    }

    // Écoute temps réel
    echo.private(`chat.room.${roomId}`)
        .listen('.MessageSent', (e) => {
            appendMsg(e);
        });

    // Envoi message
    document.getElementById('sendForm').addEventListener('submit', async (ev)=>{
        ev.preventDefault();
        const content = document.getElementById('content').value.trim();
        const info = document.getElementById('sendMsg');
        if(!content){ return; }
        info.textContent = 'Envoi…';
        try{
            const res = await axios.post(`/chat/rooms/${roomId}/messages`, { content }, {
                headers: { 'X-CSRF-TOKEN': csrf, ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) },
                withCredentials: true,
            });
            document.getElementById('content').value = '';
            info.textContent = 'Envoyé';
        }catch(e){
            info.textContent = `Erreur ${e?.response?.status||''}`;
            info.className = 'small text-danger';
        }
    });

    loadHistory();
})();
</script>
</body>
</html>
