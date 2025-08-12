(function() {
  var script = document.currentScript || (function(){var s=document.getElementsByTagName('script');return s[s.length-1];})();
  var token  = (script.getAttribute('data-api-token') || '').trim();
  if(!token){ console.error('[Widget] API token not provided'); return; }

  // кнопка
  var button = document.createElement('div');
  button.innerHTML = '<img src="'+(script.getAttribute('data-icon') || 'https://gpt.daker.az/public/assets/images/dai.png')+'" alt="chat" style="width:30px;height:30px;" />';
  Object.assign(button.style, {
    position:'fixed', bottom:'20px', right:'20px',
    width:'60px', height:'60px', borderRadius:'50%',
    backgroundColor:'#1F9D96', color:'#fff',
    display:'flex', justifyContent:'center', alignItems:'center',
    cursor:'pointer', boxShadow:'0 4px 12px rgba(0,0,0,.3)',
    zIndex:'2147483647', transition:'background-color .3s'
  });
  button.addEventListener('mouseover', ()=> button.style.backgroundColor = '#188a84');
  button.addEventListener('mouseout',  ()=> button.style.backgroundColor = '#1F9D96');
  document.body.appendChild(button);

  // iframe (абсолютный URL)
  var origin = (script.getAttribute('data-origin') || window.location.origin).replace(/\/+$/,'');
  var iframe = document.createElement('iframe');
  iframe.src = origin + '/chat-widget/' + encodeURIComponent(token);
  Object.assign(iframe.style, {
    position:'fixed', bottom:'90px', right:'20px',
    width:'370px', height:'520px', border:'none',
    borderRadius:'12px', boxShadow:'0 8px 20px rgba(0,0,0,.2)',
    zIndex:'2147483646', display:'none', overflow:'hidden',
    transform:'translateY(100px)', opacity:'0',
    transition:'transform .3s, opacity .3s'
  });
  document.body.appendChild(iframe);

  function showIframe(){ iframe.style.display='block'; setTimeout(()=>{ iframe.style.transform='translateY(0)'; iframe.style.opacity='1'; }, 10); }
  function hideIframe(){ iframe.style.transform='translateY(100px)'; iframe.style.opacity='0'; setTimeout(()=>{ iframe.style.display='none'; }, 300); }

  button.addEventListener('click', function(){ (iframe.style.display==='none') ? showIframe() : hideIframe(); });
  window.addEventListener('message', function(e){ if(e.data === 'close-chat'){ hideIframe(); } });
})();
