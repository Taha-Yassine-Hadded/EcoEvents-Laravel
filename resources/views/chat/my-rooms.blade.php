<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Mes salons</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background:#f8f9fa; }
    </style>
</head>
<body>
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Mes salons</h4>
        <div>
            @auth
                @if(auth()->user()->role === 'organizer')
                    <button id="btnCreate" class="btn btn-primary btn-sm">Créer un salon</button>
                @endif
            @endauth
        </div>
    </div>

    <div id="alert" class="alert d-none" role="alert"></div>

    <div class="card">
        <div class="card-body">
            <div id="rooms" class="list-group small">
                <div class="text-muted">Chargement…</div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios@1.6.8/dist/axios.min.js"></script>
<script>
(function(){
    const jwt = localStorage.getItem('jwt_token');
    const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const roomsEl = document.getElementById('rooms');
    const alertEl = document.getElementById('alert');

    function showAlert(type, text){
        alertEl.className = `alert alert-${type}`;
        alertEl.textContent = text;
        alertEl.classList.remove('d-none');
        setTimeout(()=>alertEl.classList.add('d-none'), 3000);
    }

    function render(list){
        if(!list || list.length === 0){
            roomsEl.innerHTML = '<div class="text-muted">Aucun salon.</div>';
            return;
        }
        roomsEl.innerHTML = '';
        list.forEach(r=>{
            const a = document.createElement('a');
            a.href = `/ui/chat/rooms/${r.id}`;
            a.className = 'list-group-item list-group-item-action d-flex justify-content-between align-items-center';
            a.innerHTML = `<span>#${r.id} ${r.name ? '· '+escapeHtml(r.name) : ''} <small class="text-muted">(${r.target_type||'custom'})</small></span><span class="badge text-bg-secondary">Ouvrir</span>`;
            roomsEl.appendChild(a);
        });
    }

    function escapeHtml(s){
        const map = { '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#039;' };
        return String(s||'').replace(/[&<>"']/g, m => map[m] || m);
    }

    async function load(){
        try{
            const res = await axios.get('/chat/my-rooms', { headers: { ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) } });
            render(res.data?.rooms || []);
        }catch(e){
            roomsEl.innerHTML = `<div class="alert alert-danger">Erreur chargement (${e?.response?.status||''})</div>`;
        }
    }

    const btnCreate = document.getElementById('btnCreate');
    if(btnCreate){
        btnCreate.addEventListener('click', async ()=>{
            const name = prompt('Nom du salon (optionnel):', 'Salon privé');
            try{
                const res = await axios.post('/chat/rooms', { name, member_ids: [] }, {
                    headers: { 'X-CSRF-TOKEN': csrf, ...(jwt?{ Authorization: `Bearer ${jwt}` }: {}) },
                    withCredentials: true,
                });
                showAlert('success','Salon créé');
                location.href = `/ui/chat/rooms/${res.data.room.id}`;
            }catch(e){
                const msg = e?.response?.data?.error || `Erreur ${e?.response?.status||''}`;
                showAlert('danger', msg);
            }
        });
    }

    load();
})();
</script>
</body>
</html>
