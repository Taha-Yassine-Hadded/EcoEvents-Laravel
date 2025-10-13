<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Détail du sujet – Forum</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .post-card { background:#fff; }
        .post-card:hover { background:#fdfdfd; }
        .locked { color:#6c757d; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <div>
            <a class="btn btn-outline-secondary btn-sm" id="backBtn" href="#">← Retour</a>
        </div>
        <span class="small text-muted">Forum Communauté</span>
    </div>

    <div id="threadHeader" class="mb-3"></div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Répondre</h5>
            <div class="alert alert-info small mb-3">Vous devez être connecté (JWT) et membre approuvé pour répondre. Le serveur vérifiera vos droits.</div>
            <form id="replyForm">
                <div class="mb-2">
                    <label class="form-label">Message</label>
                    <textarea name="content" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-success">Envoyer</button>
                <span id="replyMsg" class="ms-2 small"></span>
            </form>
        </div>
    </div>

    <div id="postsContainer" class="vstack gap-3"></div>

    <nav class="mt-4" id="paginationNav"></nav>
</div>

<script>
(function(){
    // URL attendue: /communities/{communityId}/forum/{threadId}
    const parts = window.location.pathname.split('/').filter(Boolean);
    const idx = parts.indexOf('communities');
    const communityId = idx >= 0 ? parts[idx+1] : null;
    const threadId = idx >= 0 ? parts[idx+3] : null; // communities/{id}/forum/{thread}

    const threadHeader = document.getElementById('threadHeader');
    const postsContainer = document.getElementById('postsContainer');
    const paginationNav = document.getElementById('paginationNav');
    const backBtn = document.getElementById('backBtn');

    if (communityId) {
        backBtn.href = `/communities/${communityId}/forum`;
    }

    async function fetchThread(){
        const res = await fetch(`/communities/${communityId}/forum/${threadId}`, { headers:{'Accept':'application/json'} });
        if(!res.ok){ threadHeader.innerHTML = `<div class="alert alert-danger">Erreur chargement sujet (${res.status})</div>`; return; }
        const data = await res.json();
        renderThread(data.thread);
    }

    function renderThread(t){
        const locked = t.is_locked ? '<span class="badge text-bg-secondary ms-2">Verrouillé</span>' : '';
        const pinned = t.is_pinned ? '<span class="badge text-bg-warning ms-2">Épinglé</span>' : '';
        const tags = Array.isArray(t.tags) ? t.tags.map(x=>`<span class="badge text-bg-light me-1">${x}</span>`).join(' ') : '';
        threadHeader.innerHTML = `
            <div class="card">
                <div class="card-body">
                    <h3 class="h4 d-inline">${escapeHtml(t.title)}</h3>
                    ${pinned} ${locked}
                    <div class="text-muted small mt-1">par #${t.user_id} • ${new Date(t.created_at).toLocaleString()}</div>
                    ${tags ? `<div class="mt-2">${tags}</div>` : ''}
                    <hr/>
                    <div>${escapeHtml(t.content)}</div>
                </div>
            </div>`;
        // Si verrouillé, désactiver le formulaire réponse (affichage côté client uniquement; le serveur garde l’autorité)
        if (t.is_locked) {
            const form = document.getElementById('replyForm');
            form.querySelector('textarea[name="content"]').disabled = true;
            form.querySelector('button[type="submit"]').disabled = true;
            document.getElementById('replyMsg').textContent = 'Ce sujet est verrouillé.';
            document.getElementById('replyMsg').className = 'ms-2 small text-muted locked';
        }
    }

    async function fetchPosts(pageUrl=null){
        const url = pageUrl || `/communities/${communityId}/forum/${threadId}/posts`;
        const res = await fetch(url, { headers: { 'Accept':'application/json' } });
        if(!res.ok){ postsContainer.innerHTML = `<div class="alert alert-danger">Erreur chargement messages (${res.status})</div>`; return; }
        const data = await res.json();
        renderPosts(data.posts);
    }

    function renderPosts(paginated){
        const items = paginated.data || [];
        postsContainer.innerHTML = items.map(p => `
            <div class="card post-card">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="text-muted small">#${p.user_id} • ${new Date(p.created_at).toLocaleString()}</div>
                    </div>
                    <div class="mt-2">${escapeHtml(p.content)}</div>
                </div>
            </div>
        `).join('');

        const prev = paginated.prev_page_url ? `<li class="page-item"><a class="page-link" data-url="${paginated.prev_page_url}" href="#">Précédent</a></li>` : '';
        const next = paginated.next_page_url ? `<li class="page-item"><a class="page-link" data-url="${paginated.next_page_url}" href="#">Suivant</a></li>` : '';
        paginationNav.innerHTML = `<ul class="pagination">${prev}${next}</ul>`;
        paginationNav.querySelectorAll('a.page-link').forEach(a=>{
            a.addEventListener('click', (e)=>{ e.preventDefault(); fetchPosts(a.dataset.url); });
        });
    }

    document.getElementById('replyForm').addEventListener('submit', async (e)=>{
        e.preventDefault();
        const form = e.currentTarget;
        const jwt = localStorage.getItem('jwt_token');
        const msg = document.getElementById('replyMsg');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        msg.textContent = 'Envoi...'; msg.className = 'ms-2 small text-muted';
        const payload = { content: form.content.value.trim() };
        try{
            const res = await fetch(`/communities/${communityId}/forum/${threadId}/posts`,{
                method:'POST',
                headers:{
                    'Content-Type':'application/json',
                    'Accept':'application/json',
                    'X-CSRF-TOKEN': csrf,
                    ...(jwt?{ 'Authorization':`Bearer ${jwt}` }:{}),
                },
                credentials: 'same-origin',
                body: JSON.stringify(payload)
            });
            const body = await safeJson(res);
            if(!res.ok){
                msg.textContent = `Erreur ${res.status}: ${body?.error||'Action interdite'}`;
                msg.className = 'ms-2 small text-danger';
                return;
            }
            form.reset();
            msg.textContent = 'Message publié.';
            msg.className = 'ms-2 small text-success';
            fetchPosts();
        }catch(err){
            msg.textContent = 'Erreur réseau';
            msg.className = 'ms-2 small text-danger';
        }
    });

    function escapeHtml(s){ return (s||'').replace(/[&<>"]+/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;"}[c])); }
    async function safeJson(res){ try{ return await res.json(); }catch{ return null; } }

    if (communityId && threadId) { fetchThread(); fetchPosts(); }
})();
</script>
</body>
</html>
