{{-- resources/views/pages/frontOffice/profile-edit.blade.php --}}
    <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le profil | Echofy</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <style>
        :root{
            --primary:#667eea;--primary-2:#764ba2;--bg:#f6f7fb;--card:#ffffff;--text:#2d2d2d;
            --muted:#6b7280;--danger:#ef4444;--ring:rgba(102,126,234,.35)
        }
        *{box-sizing:border-box} body{margin:0;background:var(--bg);font-family:Inter,system-ui,Segoe UI,Roboto,Arial,sans-serif;color:var(--text)}
        .container{max-width:1100px;margin:40px auto;padding:0 16px}
        .page-title{font-size:28px;font-weight:800;margin:0 0 18px}
        .breadcrumb{font-size:13px;color:var(--muted);margin-bottom:22px}
        .grid{display:grid;grid-template-columns:330px 1fr;gap:22px}
        @media (max-width: 992px){.grid{grid-template-columns:1fr}}
        .card{background:var(--card);border-radius:16px;padding:18px;box-shadow:0 10px 30px rgba(10,10,10,.05);border:1px solid #eef0f6}
        .card h3{margin:0 0 12px;font-size:18px}
        .divider{height:1px;background:#eef0f6;margin:16px 0}

        .avatar-wrap{display:flex;flex-direction:column;align-items:center;gap:14px}
        .avatar{width:120px;height:120px;border-radius:50%;overflow:hidden;box-shadow:0 8px 25px rgba(0,0,0,.1);border:3px solid #fff;background:linear-gradient(135deg,#28a745,#20c997);display:flex;align-items:center;justify-content:center}
        .avatar img{width:100%;height:100%;object-fit:cover;display:none}
        .avatar-initials{color:#fff;font-weight:800;font-size:36px;letter-spacing:1px}
        .file-row{display:flex;gap:10px;align-items:center;justify-content:center}
        .file-row input[type=file]{display:none}
        .btn{appearance:none;border:0;border-radius:12px;padding:10px 14px;font-weight:600;cursor:pointer}
        .btn-primary{background:linear-gradient(135deg,var(--primary),var(--primary-2));color:#fff;box-shadow:0 8px 18px rgba(102,126,234,.25)}
        .btn-outline{background:#fff;border:2px solid var(--primary);color:var(--primary)}
        .btn:disabled{opacity:.6;cursor:not-allowed}
        .ghost{opacity:.8}

        .form{display:grid;grid-template-columns:1fr 1fr;gap:14px}
        .form .col-span-2{grid-column:span 2}
        .field{display:flex;flex-direction:column;gap:8px}
        .label{font-size:13px;color:#4b5563;font-weight:600}
        .input,.textarea,select{border:1px solid #e5e7eb;border-radius:10px;padding:11px 12px;font-size:15px;background:#fff;outline:none;transition:border .2s, box-shadow .2s}
        .textarea{min-height:110px;resize:vertical}
        .input:focus,.textarea:focus,select:focus{border-color:var(--primary);box-shadow:0 0 0 3px var(--ring)}
        .error{color:var(--danger);font-size:12px;margin-top:-4px}
        .chips{display:flex;flex-wrap:wrap;gap:10px}
        .chip{display:inline-flex;align-items:center;gap:6px;border:1px solid #e5e7eb;border-radius:999px;padding:6px 10px;font-size:13px}
        .chip input{accent-color:var(--primary)}
        .form-actions{display:flex;justify-content:flex-end;margin-top:6px}

        .toast{position:fixed;right:18px;bottom:18px;display:flex;flex-direction:column;gap:8px;z-index:9999}
        .toast .item{background:#111827;color:#fff;border-radius:10px;padding:10px 14px;font-size:14px;box-shadow:0 8px 20px rgba(0,0,0,.25);opacity:.98}

        .banner{background:#fff4e5;border:1px solid #ffd7a8;color:#8a5a00;border-radius:10px;padding:10px 12px;font-size:14px}
        .muted{color:var(--muted);font-size:13px}
        /* Barre titre + bouton Accueil */
        .header-row{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            margin-bottom:12px;   /* espace au-dessus de la breadcrumb */
        }
        .btn-home{ white-space:nowrap; }  /* évite le retour à la ligne du libellé */
        @media (max-width:640px){
            .header-row{ flex-direction:column; align-items:flex-start; }
        }


    </style>
</head>
<body>

<div class="container">
    <div class="header-row">
        <h1 class="page-title">Modifier le profil</h1>
        <a href="{{ route('home') }}" class="btn btn-outline btn-home">Retour à l’accueil</a>
    </div>
    <div class="breadcrumb">Accueil / Mon compte / <strong>Modifier le profil</strong></div>

    <div id="no-token" class="banner" style="display:none;">
        Vous n’êtes pas connecté. Veuillez <a href="{{ route('login') }}">vous connecter</a>.
    </div>

    <div class="grid" id="profile-grid" style="display:none;">
        <!-- Avatar -->
        <div class="card">
            <h3>Photo de profil</h3>
            <div class="avatar-wrap">
                <div class="avatar" id="avatar">
                    <img id="avatar-img" alt="Avatar">
                    <div class="avatar-initials" id="avatar-initials">NN</div>
                </div>
                <div class="file-row">
                    <label class="btn btn-outline" for="avatar-file">Choisir une image</label>
                    <input id="avatar-file" type="file" accept="image/*">
                    <button id="btn-upload-avatar" class="btn btn-primary" disabled>Mettre à jour</button>
                </div>
                <div class="muted">JPG / PNG / WEBP — 2 Mo max.</div>
            </div>
        </div>

        <!-- Infos -->
        <div class="card">
            <h3>Informations du profil</h3>
            <form id="form-profile" class="form" autocomplete="off">
                <div class="field">
                    <label class="label">Nom complet</label>
                    <input class="input" id="name" name="name" placeholder="Votre nom">
                    <div class="error" data-error="name"></div>
                </div>

                <div class="field">
                    <label class="label">Email</label>
                    <input class="input" id="email" name="email" type="email" placeholder="you@example.com">
                    <div class="error" data-error="email"></div>
                </div>

                <div class="field">
                    <label class="label">Téléphone</label>
                    <input class="input" id="phone" name="phone" placeholder="+216 ...">
                    <div class="error" data-error="phone"></div>
                </div>

                <div class="field">
                    <label class="label">Ville</label>
                    <input class="input" id="city" name="city" placeholder="Votre ville">
                    <div class="error" data-error="city"></div>
                </div>

                <div class="field col-span-2">
                    <label class="label">Adresse</label>
                    <input class="input" id="address" name="address" placeholder="Adresse complète">
                    <div class="error" data-error="address"></div>
                </div>

                <div class="field col-span-2">
                    <label class="label">Bio</label>
                    <textarea class="textarea" id="bio" name="bio" placeholder="Parlez un peu de vous..."></textarea>
                    <div class="error" data-error="bio"></div>
                </div>

                <div class="field col-span-2">
                    <label class="label">Centres d’intérêt</label>
                    <div class="chips" id="interests-chips"></div>
                    <div class="error" data-error="interests"></div>
                </div>

                <div class="form-actions col-span-2">
                    <button id="btn-save" class="btn btn-primary">Enregistrer les modifications</button>
                </div>
            </form>

            <div class="divider"></div>

            <h3>Changer le mot de passe</h3>
            <form id="form-password" class="form" autocomplete="off">
                <div class="field">
                    <label class="label">Mot de passe actuel</label>
                    <input class="input" id="current_password" name="current_password" type="password" placeholder="••••••••">
                    <div class="error" data-error="current_password"></div>
                </div>
                <div class="field">
                    <label class="label">Nouveau mot de passe</label>
                    <input class="input" id="password" name="password" type="password" placeholder="••••••••">
                    <div class="error" data-error="password"></div>
                </div>
                <div class="field col-span-2">
                    <label class="label">Confirmer le mot de passe</label>
                    <input class="input" id="password_confirmation" name="password_confirmation" type="password" placeholder="••••••••">
                </div>
                <div class="form-actions col-span-2">
                    <button id="btn-change-pass" class="btn btn-outline">Mettre à jour le mot de passe</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="toast" id="toast"></div>

<script>
    (function () {
        const token = localStorage.getItem('jwt_token');
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        const $  = (s)=>document.querySelector(s);
        const $$ = (s)=>Array.from(document.querySelectorAll(s));
        const toast = $('#toast');
        const interestsOptions = ['books','technology','art','music','sports','cinema','science','travel','food'];

        function notify(msg, type='ok'){
            const el = document.createElement('div');
            el.className='item';
            el.style.background = type==='err' ? '#b91c1c' : (type==='warn' ? '#8a5a00' : '#065f46');
            el.textContent = msg; toast.appendChild(el); setTimeout(()=>el.remove(), 3500);
        }
        function clearErrors(){ $$('[data-error]').forEach(e=>e.textContent=''); }
        function renderInterests(selected){
            const wrap = $('#interests-chips'); wrap.innerHTML='';
            interestsOptions.forEach(opt=>{
                const chip=document.createElement('label'); chip.className='chip';
                chip.innerHTML = `<input type="checkbox" value="${opt}" ${selected?.includes(opt)?'checked':''}><span>${opt[0].toUpperCase()+opt.slice(1)}</span>`;
                wrap.appendChild(chip);
            });
        }
        function setAvatar(user){
            const img=$('#avatar-img'), init=$('#avatar-initials');
            const src = user.profile_image_url || user.profile_image || '';
            if ((user.has_image && user.profile_image) || user.profile_image_url){
                img.src=src; img.style.display='block'; init.style.display='none';
            }else{
                init.textContent=(user.initials||'NN').slice(0,2).toUpperCase();
                init.style.display='block'; img.style.display='none';
            }
        }
        function getCheckedInterests(){ return $$('#interests-chips input[type=checkbox]').filter(i=>i.checked).map(i=>i.value); }

        // ✅ API avec "method spoofing" automatique pour FormData + PATCH/PUT/DELETE
        async function api(url, {method='GET', body=null, isForm=false} = {}){
            const headers = {'Accept':'application/json','X-Requested-With':'XMLHttpRequest','X-CSRF-TOKEN':csrf};
            if (token) headers['Authorization'] = `Bearer ${token}`;

            if (isForm && body instanceof FormData && ['PATCH','PUT','DELETE'].includes(method)) {
                body.append('_method', method);     // indique la méthode réelle à Laravel
                method = 'POST';                    // on envoie en POST
            }

            if (!isForm && body && !(body instanceof FormData)) {
                headers['Content-Type'] = 'application/json';
                body = JSON.stringify(body);
            }

            const res = await fetch(url, {method, headers, body});
            const ct = res.headers.get('content-type') || '';
            const data = ct.includes('application/json') ? await res.json() : {ok:res.ok, status:res.status, message:await res.text()};
            if (!res.ok) throw {status:res.status, data, message:data?.message || `Erreur ${res.status}`};
            return data;
        }

        // Init
        (async function init(){
            if(!token){ document.getElementById('no-token').style.display=''; return; }
            document.getElementById('profile-grid').style.display='';

            renderInterests([]);

            try{
                const me = await api(`{{ route('user.get') }}`);
                const user = me.user || me.data || me;
                $('#name').value    = user.name   ?? '';
                $('#email').value   = user.email  ?? '';
                $('#phone').value   = user.phone  ?? '';
                $('#city').value    = user.city   ?? '';
                $('#address').value = user.address?? '';
                $('#bio').value     = user.bio    ?? '';
                renderInterests(user.interests || []);
                setAvatar(user);
            }catch(e){ notify('Impossible de charger le profil','err'); console.error(e); }
        })();

        // Avatar: preview + upload
        const fileInput = $('#avatar-file'), btnUpload = $('#btn-upload-avatar');
        fileInput.addEventListener('change', ()=>{
            const f = fileInput.files?.[0]; btnUpload.disabled = !f;
            if(!f) return;
            const reader = new FileReader();
            reader.onload = e => { $('#avatar-img').src = e.target.result; $('#avatar-img').style.display='block'; $('#avatar-initials').style.display='none'; };
            reader.readAsDataURL(f);
        });
        btnUpload.addEventListener('click', async (ev)=>{
            ev.preventDefault(); if(!fileInput.files?.[0]) return;
            const fd = new FormData(); fd.append('avatar', fileInput.files[0]);
            btnUpload.disabled=true; btnUpload.classList.add('ghost');
            try{ const r = await api(`{{ route('profile.avatar.update') }}`, {method:'POST', body:fd, isForm:true}); notify(r?.message || 'Avatar mis à jour'); }
            catch(e){ notify('Échec de mise à jour de l’avatar','err'); console.error(e); }
            finally{ btnUpload.disabled=false; btnUpload.classList.remove('ghost'); fileInput.value=''; }
        });

        // Save profile
        $('#form-profile').addEventListener('submit', async (ev)=>{
            ev.preventDefault(); clearErrors();
            const fd = new FormData();
            fd.append('name', $('#name').value.trim());
            fd.append('email', $('#email').value.trim());
            fd.append('phone', $('#phone').value.trim());
            fd.append('city', $('#city').value.trim());
            fd.append('address', $('#address').value.trim());
            fd.append('bio', $('#bio').value.trim());
            getCheckedInterests().forEach(v => fd.append('interests[]', v));

            const btn=$('#btn-save'); btn.disabled=true; btn.classList.add('ghost');
            try{ const r = await api(`{{ route('profile.update') }}`, {method:'PATCH', body:fd, isForm:true}); notify(r?.message || 'Profil mis à jour'); }
            catch(e){
                if (e?.data?.errors){ Object.entries(e.data.errors).forEach(([k,arr])=>{ const slot=document.querySelector(`[data-error="${k}"]`); if(slot) slot.textContent = Array.isArray(arr)?arr[0]:String(arr); }); }
                notify('Vérifiez les champs du formulaire','err'); console.error(e);
            }finally{ btn.disabled=false; btn.classList.remove('ghost'); }
        });

        // Change password
        $('#form-password').addEventListener('submit', async (ev)=>{
            ev.preventDefault(); clearErrors();
            const fd = new FormData();
            fd.append('current_password', $('#current_password').value);
            fd.append('password', $('#password').value);
            fd.append('password_confirmation', $('#password_confirmation').value);

            const btn=$('#btn-change-pass'); btn.disabled=true; btn.classList.add('ghost');
            try{ const r = await api(`{{ route('profile.password.update') }}`, {method:'PUT', body:fd, isForm:true}); notify(r?.message || 'Mot de passe mis à jour'); $('#current_password').value=''; $('#password').value=''; $('#password_confirmation').value=''; }
            catch(e){
                if (e?.data?.errors){ Object.entries(e.data.errors).forEach(([k,arr])=>{ const slot=document.querySelector(`[data-error="${k}"]`); if(slot) slot.textContent = Array.isArray(arr)?arr[0]:String(arr); }); }
                notify('Impossible de changer le mot de passe','err'); console.error(e);
            }finally{ btn.disabled=false; btn.classList.remove('ghost'); }
        });
    })();
</script>
</body>
</html>
