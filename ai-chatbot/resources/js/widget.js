(function(){
    const script = document.currentScript;
    const apiToken = script?.dataset?.apiToken;
    if(!apiToken){ console.warn('[AI Widget] data-api-token missing'); return; }
  
    const btn = document.createElement('button');
    btn.textContent = 'Чат';
    Object.assign(btn.style, { position:'fixed', right:'16px', bottom:'16px', zIndex:2147483647,
      padding:'10px 14px', borderRadius:'10px', border:'1px solid #ccc', background:'#fff',
      cursor:'pointer', boxShadow:'0 6px 18px rgba(0,0,0,.12)' });
    document.body.appendChild(btn);
  
    const wrap = document.createElement('div');
    Object.assign(wrap.style, { position:'fixed', right:'16px', bottom:'64px', width:'360px', height:'520px',
      background:'#fff', border:'1px solid #e5e7eb', borderRadius:'12px', boxShadow:'0 20px 40px rgba(0,0,0,.18)',
      overflow:'hidden', display:'none', zIndex:2147483647 });
    document.body.appendChild(wrap);
  
    const iframe = document.createElement('iframe');
    Object.assign(iframe.style, {border:'0', width:'100%', height:'100%'}); wrap.appendChild(iframe);
  
    iframe.addEventListener('load', () => {
      const doc = iframe.contentDocument;
      doc.open();
      doc.write(`
        <html><head><meta charset="utf-8">
          <style>
            body{font:14px/1.4 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;margin:0;background:#f9fafb}
            .head{padding:12px 14px;background:#111827;color:#fff}
            .list{padding:12px;height:380px;overflow:auto;background:#fff}
            .item{margin-bottom:10px}
            .q{background:#eef2ff;padding:8px 10px;border-radius:10px;display:inline-block;max-width:85%}
            .a{background:#f3f4f6;padding:8px 10px;border-radius:10px;display:inline-block;max-width:85%}
            .form{display:flex;gap:6px;padding:10px;background:#f9fafb;border-top:1px solid #e5e7eb}
            input{flex:1;padding:10px;border:1px solid #d1d5db;border-radius:10px}
            button{padding:10px 12px;border:1px solid #d1d5db;border-radius:10px;background:#fff;cursor:pointer}
          </style>
        </head><body>
          <div class="head">AI помощник</div>
          <div class="list" id="list"></div>
          <form class="form" id="form">
            <input id="msg" placeholder="Ваш вопрос..." autocomplete="off"/>
            <button type="submit">Отправить</button>
          </form>
          <script>
            const apiToken = ${JSON.stringify(apiToken)};
            const list = document.getElementById('list');
            const form = document.getElementById('form');
            const input = document.getElementById('msg');
            const history = [];
            function addBubble(text, cls){
              const w = document.createElement('div'); w.className='item';
              const b = document.createElement('div'); b.className=cls; b.textContent=text;
              w.appendChild(b); list.appendChild(w); list.scrollTop = list.scrollHeight;
            }
            form.addEventListener('submit', async (e) => {
              e.preventDefault();
              const q = input.value.trim(); if(!q) return;
              input.value=''; addBubble(q,'q');
              const body = { message: q, history: history.slice(-3) };
              try{
                const res = await fetch('/api/public-chat', {
                  method:'POST',
                  headers:{ 'Content-Type':'application/json', 'X-API-TOKEN': apiToken },
                  body: JSON.stringify(body)
                });
                const data = await res.json();
                if(!res.ok){ addBubble(data.error || 'Ошибка', 'a'); return; }
                const a = data.answer || '...';
                addBubble(a,'a');
                history.push({q, a});
                if(history.length>3) history.splice(0, history.length-3);
              }catch{ addBubble('Сеть недоступна','a'); }
            });
          <\/script>
        </body></html>
      `);
      doc.close();
    });
  
    btn.addEventListener('click', ()=> { wrap.style.display = (wrap.style.display==='none') ? 'block':'none'; });
  })();
  