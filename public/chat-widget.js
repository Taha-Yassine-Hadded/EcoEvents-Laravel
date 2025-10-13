(function(){
  const WIDGET_ID = 'eco-chat-widget';
  if (document.getElementById(WIDGET_ID)) return; // avoid duplicates

  const JWT = localStorage.getItem('jwt_token');
  const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
  const API_ROOMS = '/chat/my-rooms';
  const API_ONE_TO_ONE = '/chat/one-to-one';
  const UI_ROOM = (id)=>`/ui/chat/rooms/${id}`;
  const SUPPORT_ID = Number(document.querySelector('meta[name="support-user-id"]')?.getAttribute('content') || '1');

  const styles = `
    #${WIDGET_ID} { position: fixed; right: 20px; bottom: 20px; z-index: 2147483000; }
    #${WIDGET_ID} .btn { width:56px; height:56px; border-radius:50%; background:#0d6efd; color:#fff; display:flex; align-items:center; justify-content:center; box-shadow:0 8px 24px rgba(13,110,253,.35); cursor:pointer; transition:.2s; }
    #${WIDGET_ID} .btn:hover { transform: translateY(-2px); box-shadow:0 10px 28px rgba(13,110,253,.45); }
    #${WIDGET_ID} .badge { position:absolute; right:14px; top:14px; background:#dc3545; color:#fff; border-radius:999px; padding:2px 6px; font-size:11px; line-height:1; display:none; }
    #${WIDGET_ID}-panel { position: fixed; top: 0; right: 0; height: 100vh; width: min(420px, 100vw); max-width: 100%; background: #fff; border-left: 1px solid #e5e7eb; box-shadow: -12px 0 30px rgba(0,0,0,.08); z-index: 2147483001; transform: translateX(100%); transition: transform .25s ease; overflow: hidden; }
    #${WIDGET_ID}-panel.open { transform: translateX(0); }
    #${WIDGET_ID}-panel .topbar { height: 48px; display:flex; align-items:center; justify-content:space-between; padding:0 12px; background:#0d6efd; color:#fff; font: 500 14px/1 system-ui, -apple-system, Segoe UI, Roboto, sans-serif; }
    #${WIDGET_ID}-panel .close { cursor:pointer; padding:8px 10px; border-radius:6px; }
    #${WIDGET_ID}-panel .close:hover { background: rgba(255,255,255,.15); }
    #${WIDGET_ID}-panel iframe { width: 100%; height: calc(100vh - 48px); border: 0; }
  `;

  const styleEl = document.createElement('style');
  styleEl.textContent = styles;
  document.head.appendChild(styleEl);

  const root = document.createElement('div');
  root.id = WIDGET_ID;
  root.innerHTML = `
    <div class="btn" aria-label="Open Messenger">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <path d="M12 2C6.477 2 2 6.02 2 10.98c0 2.69 1.358 5.1 3.54 6.772V22l3.241-1.782c1.01.28 2.085.43 3.219.43 5.523 0 10-4.02 10-8.98S17.523 2 12 2Z" fill="white" fill-opacity=".15"/>
        <path d="M12 3.5c-4.695 0-8.5 3.459-8.5 7.73 0 2.318 1.169 4.474 3.045 5.94l.455.354v2.303l2.003-1.102.33-.182 .38.1c.95.252 1.976.384 3.287.384 4.695 0 8.5-3.458 8.5-7.73C21.5 6.959 16.695 3.5 12 3.5Zm3.707 6.793-2.81 2.81a.997.997 0 0 1-1.414 0l-1.086-1.086a.5.5 0 0 0-.707 0L7.5 13.207l-.707-.707 2.19-2.19a1.5 1.5 0 0 1 2.121 0l1.086 1.086.086.086.086-.086 2.81-2.81.707.707Z" fill="#fff"/>
      </svg>
    </div>
    <span class="badge" id="${WIDGET_ID}-badge">0</span>
  `;
  document.body.appendChild(root);

  const panel = document.createElement('div');
  panel.id = `${WIDGET_ID}-panel`;
  panel.innerHTML = `
    <div class="topbar">
      <span>Messages</span>
      <span class="close" id="${WIDGET_ID}-close" title="Fermer">✕</span>
    </div>
    <iframe src="/ui/chat/my-rooms" referrerpolicy="no-referrer-when-downgrade"></iframe>
  `;
  document.body.appendChild(panel);

  async function openDirectDM(){
    if(!JWT){ togglePanel(); return; }
    try{
      const res = await fetch(API_ONE_TO_ONE, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', ...(JWT? { 'Authorization': `Bearer ${JWT}` } : {}), ...(CSRF? { 'X-CSRF-TOKEN': CSRF } : {}) },
        credentials: 'include',
        body: JSON.stringify({ user_id: SUPPORT_ID })
      });
      if(!res.ok) throw new Error('http '+res.status);
      const data = await res.json();
      const roomId = data?.room?.id;
      if(roomId){
        panel.querySelector('iframe').src = UI_ROOM(roomId);
      }
    }catch(e){ /* silent */ }
    togglePanel();
  }
  function togglePanel(){ panel.classList.toggle('open'); }
  root.querySelector('.btn').addEventListener('click', openDirectDM);
  document.getElementById(`${WIDGET_ID}-close`).addEventListener('click', togglePanel);

  // Badge updater
  async function refreshBadge(){
    try{
      const res = await fetch(API_ROOMS, { headers: { ...(JWT? { 'Authorization': `Bearer ${JWT}` } : {}) } });
      if(!res.ok) throw new Error('http '+res.status);
      const data = await res.json();
      const sum = (data.rooms||[]).reduce((acc, r)=> acc + (r.unread_count||0), 0);
      const badge = document.getElementById(`${WIDGET_ID}-badge`);
      if(sum > 0){ badge.style.display='inline-block'; badge.textContent = (sum>99? '99+': String(sum)); }
      else { badge.style.display='none'; }
    }catch(e){ /* silent */ }
  }
  refreshBadge();
  setInterval(refreshBadge, 15000);
})();
