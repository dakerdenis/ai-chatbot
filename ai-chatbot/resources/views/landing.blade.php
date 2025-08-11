<!doctype html>
<html lang="ru" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>D.A.I. — AI Widget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/landing.css') }}">
</head>

<body>
    <div class="main__container">
        <div class="main__wrapper">

        </div>
        <div class="main__wrapper-content">

            <!--MAIN LOGO---->
            <div class="main__logo">
                <img src="{{ asset('/assets/images/daker_logo.svg') }}" alt="" srcset="">
            </div>
            <!---main block - white--->
            <div class="main__block">
                <div class="main__block-wrapper">
                    <!---VERSION--->
                    <div class="main__block-version">
                        Version 1.0
                    </div>

                    <!---chat BOT logo and desc----->
                    <div class="main__block-desc">
                        <div class="chat_logo">
                            <img src="{{ asset('/assets/images/dai-logo.svg') }}" alt="" srcset="">
                        </div>
                        <div class="chat__desc">
                            <h1>
                                Hey! <br> You are welcome to the
                                <span class="gradient-text">D.A.I. Assistant.</span>
                            </h1>
                            <div class="chat_desc-text">
                                How can I help you today?
                            </div>
                        </div>
                    </div>


                    <!----chat bot about CARDS disappear when user writes something----->
                    <div class="chat__cards-wrapper">
                        <!------->
                        <div class="chat__card-element">
                            <div class="chat__card-container">
                                <div class="chat__card-logo">

                                </div>
                                <div class="chat__card-name">
                                    Stock market updates
                                </div>
                                <div class="chat__card-text">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus pulvinar arcu
                                    lacus, a maximus purus imperdiet auctor. Integer nisi neque, placerat id suscipit
                                    non, convallis eget ipsum.
                                </div>
                            </div>
                        </div>
                        <!------->
                        <!------->
                        <div class="chat__card-element">
                            <div class="chat__card-container">
                                <div class="chat__card-logo">

                                </div>
                                <div class="chat__card-name">
                                    Stock market updates
                                </div>
                                <div class="chat__card-text">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus pulvinar arcu
                                    lacus, a maximus purus imperdiet auctor. Integer nisi neque, placerat id suscipit
                                    non, convallis eget ipsum.
                                </div>
                            </div>
                        </div>
                        <!------->
                        <!------->
                        <div class="chat__card-element">
                            <div class="chat__card-container">
                                <div class="chat__card-logo">

                                </div>
                                <div class="chat__card-name">
                                    Stock market updates
                                </div>
                                <div class="chat__card-text">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus pulvinar arcu
                                    lacus, a maximus purus imperdiet auctor. Integer nisi neque, placerat id suscipit
                                    non, convallis eget ipsum.
                                </div>
                            </div>
                        </div>
                        <!------->

                    </div>
                    <!----Here will be messages of chatbot---->
                    <!-- ----Here will be messages of chatbot---->
                    <div class="chat__form-answers" aria-live="polite">
                        <div class="chat__thread" id="chatThread">
                            <!-- пример приветствия бота -->
                            <div class="msg bot">
                                <div class="avatar">AI</div>
                                <div class="bubble">
                                    Привет! Я помогу с вопросами о вашем сервисе. Сформулируйте запрос и нажмите Enter.
                                </div>
                            </div>
                        </div>

                        <!-- индикатор набора -->
                        <div class="typing" id="typing" hidden>
                            <div class="dot"></div>
                            <div class="dot"></div>
                            <div class="dot"></div>
                        </div>
                    </div>

                    <!----chat bot input field--------->
                    <div class="chat__form-container">

                        <!---form with which user write questions--->
                        <form action="" class="chat__form-form">
                            <input type="text" placeholder="Type and press ENTER...">

                            <button>
                                <img src="{{ asset('/assets/images/Vector.svg') }}" alt="" srcset="">
                            </button>
                        </form>


                    </div>

                    <!----CREATED BY----->
                    <div class="chat_created">
                        <p>D.A.I. — Developed by Daker, powered by AI</p>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <script>
(function(){
  const form = document.querySelector('.chat__form-form');
  const input = form?.querySelector('input[type="text"]');
  const thread = document.getElementById('chatThread');
  const typing = document.getElementById('typing');
  const sendBtn = form?.querySelector('button');
  const container = document.querySelector('.main__block-wrapper'); // сюда повесим класс is-chatting

  if(!form || !input || !thread) return;

  function scrollToBottom(){
    thread.scrollTop = thread.scrollHeight;
  }

  function addMsg(role, text){
    const wrap = document.createElement('div');
    wrap.className = 'msg ' + (role === 'user' ? 'user' : 'bot');

    if(role !== 'user'){
      const av = document.createElement('div');
      av.className = 'avatar';
      av.textContent = 'AI';
      wrap.appendChild(av);
    }

    const bubble = document.createElement('div');
    bubble.className = 'bubble';
    bubble.textContent = text;
    wrap.appendChild(bubble);

    thread.appendChild(wrap);
    scrollToBottom();
  }

  function toggleTyping(show){
    typing.hidden = !show;
    if(show) scrollToBottom();
  }

  async function handleSend(){
    const q = (input.value || '').trim();
    if(!q) return;

    // Включаем "режим чата": прячем карточки, показываем ленту
    container?.classList.add('is-chatting');

    // добавляем сообщение пользователя
    addMsg('user', q);
    input.value = '';

    // показываем индикатор
    toggleTyping(true);

    try{
      // TODO: здесь можно звать реальный API:
      // const res = await fetch('/api/public-chat', { ... });
      // const data = await res.json(); const answer = data.answer;
      // Для демо имитируем ответ:
      await new Promise(r => setTimeout(r, 700));
      const answer = 'Это заглушка ответа. Здесь будет ответ нейросети на ваш вопрос.';

      addMsg('bot', answer);
    }catch{
      addMsg('bot', 'Ошибка сети. Повторите попытку позже.');
    }finally{
      toggleTyping(false);
    }
  }

  // обработчики
  form.addEventListener('submit', (e)=>{ e.preventDefault(); handleSend(); });
  sendBtn?.addEventListener('click', (e)=>{ e.preventDefault(); handleSend(); });

  // Enter в поле
  input.addEventListener('keydown', (e)=>{
    if(e.key === 'Enter'){
      e.preventDefault();
      handleSend();
    }
  });
})();
</script>

</body>

</html>
