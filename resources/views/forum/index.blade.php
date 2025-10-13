<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Forum – Communauté</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .thread-card:hover { background:#fdfdfd; box-shadow: 0 2px 10px rgba(0,0,0,.05); }
        .badge-pin { background:#ffc107; }
        .badge-locked { background:#6c757d; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h2 class="mb-0">Forum de la communauté</h2>
        <a class="btn btn-outline-secondary" href="/communities">← Retour</a>
    </div>

    <div class="row g-3 align-items-end mb-4">
        <div class="col-md-6">
            <label class="form-label">Rechercher</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Mots-clés..." />
        </div>
        <div class="col-md-3">
            <button id="btnSearch" class="btn btn-primary w-100">Rechercher</button>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-body">
            <h5 class="card-title">Nouveau sujet</h5>
            <div class="alert alert-info small mb-3">Vous devez être membre approuvé et connecté (JWT) pour publier. Le serveur vérifiera vos droits.</div>
            <form id="newThreadForm">
                <div class="mb-2">
                    <label class="form-label">Titre</label>
                    <input type="text" name="title" class="form-control" required maxlength="255" />
                </div>
                <div class="mb-2">
                    <label class="form-label">Contenu</label>
                    <textarea name="content" class="form-control" rows="4" required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tags (séparés par des virgules)</label>
                    <input type="text" id="tags" class="form-control" placeholder="ex: recyclage, eau" />
                </div>
                <button type="submit" class="btn btn-success">Publier</button>
                <span id="newThreadMsg" class="ms-2 small"></span>
            </form>
        </div>
    </div>

    <div id="threadsContainer" class="vstack gap-3"></div>

    <nav class="mt-4" id="paginationNav"></nav>
</div>

<script>
(function(){
    // Déduit l'ID communauté depuis l'URL: /communities/{id}/forum
    const parts = window.location.pathname.split('/').filter(Boolean);
    // Cherche l'index de 'communities'
    const idx = parts.indexOf('communities');
    const communityId = idx >= 0 ? parts[idx+1] : null;

    const threadsContainer = document.getElementById('threadsContainer');
    const paginationNav = document.getElementById('paginationNav');

    async function fetchThreads(q = '', pageUrl = null){
        const url = pageUrl || `/communities/${communityId}/forum` + (q?`?q=${encodeURIComponent(q)}`:'');
        const res = await fetch(url, { headers: { 'Accept':'application/json' } });
        if(!res.ok){ threadsContainer.innerHTML = `<div class="alert alert-danger">Erreur chargement (${res.status})</div>`; return; }
        const data = await res.json();
        renderThreads(data.threads);
    }

    function renderThreads(paginated){
        const items = (paginated && Array.isArray(paginated.data)) ? paginated.data : (paginated?.data || []);
        threadsContainer.innerHTML = items.map(t => {
            const pin = t.is_pinned ? '<span class="badge badge-pin me-2">Épinglé</span>' : '';
            const locked = t.is_locked ? '<span class="badge badge-locked me-2">Verrouillé</span>' : '';
            const tags = Array.isArray(t.tags) ? t.tags.map(x=>`<span class="badge text-bg-light me-1">${x}</span>`).join(' ') : '';
            return `
            <a class="card thread-card text-decoration-none text-body" href="/ui/communities/${t.community_id}/forum/${t.id}">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-1">
                        ${pin}${locked}<h5 class="card-title mb-0">${escapeHtml(t.title)}</h5>
                    </div>
                    <p class="text-muted mb-2 small">par #${t.user_id} • ${new Date(t.created_at).toLocaleString()}</p>
                    ${tags ? `<div class="mb-2">${tags}</div>` : ''}
                    <div class="text-truncate">${escapeHtml((t.content||'').slice(0,180))}</div>
                </div>
            </a>`;
        }).join('');

        // Pagination simple
        const prev = paginated.prev_page_url ? `<li class="page-item"><a class="page-link" data-url="${paginated.prev_page_url}" href="#">Précédent</a></li>` : '';
        const next = paginated.next_page_url ? `<li class="page-item"><a class="page-link" data-url="${paginated.next_page_url}" href="#">Suivant</a></li>` : '';
        paginationNav.innerHTML = `<ul class="pagination">${prev}${next}</ul>`;
        paginationNav.querySelectorAll('a.page-link').forEach(a=>{
            a.addEventListener('click', (e)=>{ e.preventDefault(); fetchThreads(document.getElementById('searchInput').value, a.dataset.url); });
        });
    }

    document.getElementById('btnSearch').addEventListener('click', ()=>{
        fetchThreads(document.getElementById('searchInput').value);
    });

    document.getElementById('newThreadForm').addEventListener('submit', async (e)=>{
        e.preventDefault();
        const form = e.currentTarget;
        const jwt = localStorage.getItem('jwt_token');
        const msg = document.getElementById('newThreadMsg');
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        msg.textContent = 'Envoi...'; msg.className = 'ms-2 small text-muted';
        const tagsStr = document.getElementById('tags').value.trim();
        const payload = {
            title: form.title.value.trim(),
            content: form.content.value.trim(),
            tags: tagsStr ? tagsStr.split(',').map(s=>s.trim()).filter(Boolean) : []
        };
        try{
            const res = await fetch(`/communities/${communityId}/forum`,{
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
            if(!res.ok){
                const t = await safeJson(res);
                if(res.status === 422 && t && t.errors){
                    const firstField = Object.keys(t.errors)[0];
                    const firstMsg = t.errors[firstField]?.[0] || 'Validation invalide';
                    msg.textContent = `Erreur 422: ${firstMsg}`;
                } else {
                    msg.textContent = `Erreur ${res.status}: ${t?.error||'Action interdite'}`;
                }
                msg.className = 'ms-2 small text-danger';
                return;
            }
            form.reset();
            msg.textContent = 'Sujet créé.';
            msg.className = 'ms-2 small text-success';
            fetchThreads();
        }catch(err){
            msg.textContent = 'Erreur réseau';
            msg.className = 'ms-2 small text-danger';
        }
    });

    function escapeHtml(s){ return (s||'').replace(/[&<>"]+/g, c=>({"&":"&amp;","<":"&lt;",">":"&gt;","\"":"&quot;"}[c])); }
    async function safeJson(res){ try{ return await res.json(); }catch{ return null; } }

    if(communityId){ fetchThreads(); }
})();
</script>
</body>
</html>
