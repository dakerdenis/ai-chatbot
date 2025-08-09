// resources/js/widget.js
(function () {
  try {
    var scriptEl = document.currentScript || (function(){
      var s = document.getElementsByTagName('script'); return s[s.length-1];
    })();
    var apiToken = scriptEl.getAttribute('data-api-token');
    if (!apiToken) { console.warn('[AI Widget] data-api-token missing'); return; }

    // Кнопка открытия
    var btn = document.createElement('button');
    btn.textContent = 'Чат';
    Object.assign(btn.style, {
      position:'fixed', right:'16px', bottom:'16px', zIndex: 2147483647,
      padding:'10px 14px', borderRadius:'12px', border:'1px solid #d1d5db',
      background:'#fff', cursor:'pointer', boxShadow:'0 8px 24px rgba(0,0,0,.18)'
    });
    document.body.appendChild(btn);

    // Окно
    var wrap = document.createElement('div');
    Object.assign(wrap.style, {
      position:'fixed', right:'16px', bottom:'64px', width:'380px', height:'560px',
      background:'#fff', border:'1px solid #e5e7eb', borderRadius:'14px',
      boxShadow:'0 24px 48px rgba(0,0,0,.22)', overflow:'hidden',
      display:'none', zIndex:2147483647
    });
    document.body.appendChild(wrap);

    var iframe = document.createElement('iframe');
    iframe.setAttribute('title','AI Chat');
    Object.assign(iframe.style, {border:'0', width:'100%', height:'100%'});
    wrap.appendChild(iframe);

    // HTML виджета (srcdoc)
    var html = `
<!doctype html>
<html lang="ru">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<style>
  :root{
    --bg:#ffffff; --muted:#f5f7fb; --text:#111827; --sub:#6b7280; --line:#e5e7eb;
    --me:#e6f0ff; --bot:#f3f4f6; --brand:#111827;
  }
  @media (prefers-color-scheme: dark){
    :root{
      --bg:#0b0c10; --muted:#111318; --text:#e5e7eb; --sub:#9ca3af; --line:#1f232b;
      --me:#1b2a48; --bot:#151821; --brand:#e5e7eb;
    }
  }
  *{box-sizing:border-box}
  html,body{height:100%}
  body{margin:0;background:var(--bg);color:var(--text);font:14px/1.45 system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Ubuntu,Arial,sans-serif}
  .head{display:flex;align-items:center;justify-content:space-between;padding:12px 14px;background:var(--brand);color:#fff}
  .title{font-weight:600}
  .close{appearance:none;background:transparent;border:0;color:#fff;opacity:.8;cursor:pointer}
  .list{padding:12px;height:430px;overflow:auto;background:var(--bg)}
  .item{margin:8px 0;display:flex}
  .q{justify-content:flex-end}
  .a{justify-content:flex-start}
  .bubble{max-width:78%;padding:10px 12px;border-radius:14px;border:1px solid var(--line)}
  .bubble.q{background:var(--me)}
  .bubble.a{background:var(--bot)}
  .typing{display:inline-block;min-width:36px}
  .dot{display:inline-block;width:6px;height:6px;border-radius:50%;background:#9ca3af;margin:0 2px;animation:blink 1.2s infinite}
  .dot:nth-child(2){animation-delay:.2s}.dot:nth-child(3){animation-delay:.4s}
  @keyframes blink{0%,80%,100%{opacity:.2}40%{opacity:1}}
  .form{display:flex;gap:8px;padding:10px;border-top:1px solid var(--line);background:var(--muted)}
  input{flex:1;padding:10px;border:1px solid var(--line);border-radius:12px;background:#fff;color:#111}
  @media (prefers-color-scheme: dark){ input{background:#0f1218;color:#e5e7eb;border-color:#252a34} }
  button{padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:#fff;cursor:pointer}
  .disabled{opacity:.6;pointer-events:none}
</style>
</head>
<body>
  <div class="head">
    <div class="title">AI помощник</div>
    <button class="close" id="closeBtn" title="Свернуть">×</button>
  </div>
  <div class="list" id="list"></div>
  <form class="form" id="form" autocomplete="off">
    <input id="msg" placeholder="Ваш вопрос… (Enter — отправить, Shift+Enter — перенос)" />
    <button type="submit" id="sendBtn">Отправить</button>
  </form>
  <script>
    const apiToken = ${JSON.stringify(apiToken)};
    const list = document.getElementById('list');
    const form = document.getElementById('form');
    const input = document.getElementById('msg');
    const sendBtn = document.getElementById('sendBtn');
    const closeBtn = document.getElementById('closeBtn');
    const history = [];

    function addItem(text, who){
      const row = document.createElement('div'); row.className = 'item ' + who;
      const b = document.createElement('div'); b.className = 'bubble ' + who;
      b.textContent = text;
      row.appendChild(b); list.appendChild(row);
      list.scrollTop = list.scrollHeight;
      return b;
    }

    function addTyping(){
      const row = document.createElement('div'); row.className = 'item a';
      const b = document.createElement('div'); b.className = 'bubble a typing';
      b.innerHTML = '<span class="dot"></span><span class="dot"></span><span class="dot"></span>';
      row.appendChild(b); list.appendChild(row);
      list.scrollTop = list.scrollHeight;
      return row; // контейнер для удаления
    }

    function setSending(sending){
      if(sending){ form.classList.add('disabled'); sendBtn.classList.add('disabled'); }
      else{ form.classList.remove('disabled'); sendBtn.classList.remove('disabled'); }
      input.disabled = !!sending; sendBtn.disabled = !!sending;
    }

    // приветствие
    addItem('Здравствуйте! Я помогу с вопросами по сайту. Чем могу быть полезен?', 'a');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const q = input.value.trim(); if(!q) return;
      input.value = '';
      addItem(q, 'q');
      const typing = addTyping();
      setSending(true);

      const body = { message: q, history: history.slice(-3) };
      try{
        const res = await fetch('/api/public-chat', {
          method:'POST',
          headers:{ 'Content-Type':'application/json', 'X-API-TOKEN': apiToken },
          body: JSON.stringify(body),
          credentials: 'omit'
        });
        const data = await res.json();
        typing.remove();
        if(!res.ok){ addItem(data.error || 'Ошибка', 'a'); setSending(false); return; }
        const a = data.answer || '…';
        addItem(a, 'a');
        history.push({q, a});
        if(history.length>3) history.splice(0, history.length-3);
      }catch(err){
        typing.remove();
        addItem('Сеть недоступна', 'a');
      }finally{
        setSending(false);
        input.focus();
      }
    });

    // Enter — отправить, Shift+Enter — перенос строки
    input.addEventListener('keydown', (ev) => {
      if(ev.key === 'Enter' && !ev.shiftKey){ ev.preventDefault(); form.dispatchEvent(new Event('submit')); }
    });

    closeBtn.addEventListener('click', () => {
      parent.postMessage({ type:'aiw-close' }, '*');
    });
  <` + `/script>
</body></html>
    `;

    // Рендерим
    iframe.srcdoc = html;

    // кнопка открыть/закрыть
    btn.addEventListener('click', function(){
      wrap.style.display = (wrap.style.display==='none') ? 'block' : 'none';
    });
    window.addEventListener('message', function(e){
      if(e.data && e.data.type === 'aiw-close'){
        wrap.style.display = 'none';
      }
    });

  } catch (e) {
    console.error('[AI Widget] fatal', e);
  }
})();
