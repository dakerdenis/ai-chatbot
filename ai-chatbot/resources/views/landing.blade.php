<!doctype html>
<html lang="ru" data-bs-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>D.A.I. — AI Widget</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/landing.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
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
                    <div class="chat__cards-wrapper"> <!-- Card 1 -->
                        <div class="chat__card-element">
                            <div class="chat__card-container">
                                <div class="chat__card-logo"> <img src="{{ asset('/assets/images/icon-ai.svg') }}"
                                        alt="AI Icon"> </div>
                                <div class="chat__card-name"> Real-time AI Assistance </div>
                                <div class="chat__card-text"> Get instant, accurate answers powered by advanced AI
                                    algorithms to help you work smarter and faster. </div>
                            </div>
                        </div> <!-- Card 2 -->
                        <div class="chat__card-element">
                            <div class="chat__card-container">
                                <div class="chat__card-logo"> <img
                                        src="{{ asset('/assets/images/icon-integration.svg') }}" alt="Integration Icon">
                                </div>
                                <div class="chat__card-name"> Customizable Integrations </div>
                                <div class="chat__card-text"> Easily integrate the chatbot into your apps, websites, or
                                    platforms with minimal setup and full control. </div>
                            </div>
                        </div> <!-- Card 3 -->
                        <div class="chat__card-element">
                            <div class="chat__card-container">
                                <div class="chat__card-logo"> <i class="fa fa-clock-o" style="font-size:24px"></i>
                                </div>
                                <div class="chat__card-name"> 24/7 Availability </div>
                                <div class="chat__card-text"> Our chatbot is always online, ready to assist you at any
                                    time of the day, every day of the year. </div>
                            </div>
                        </div>
                    </div>
                    <!----Here will be messages of chatbot---->
                    <!-- ----Here will be messages of chatbot---->
                    <div class="chat__form-answers" aria-live="polite">
                        <div class="chat__thread" id="chatThread">
                            <!-- пример приветствия бота -->
                            <div class="msg bot">
                                <div class="avatar">AI</div>
                                <div class="bubble">
                                    Hi! Ask me anything about our services. Type your question and press Enter.
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
        (function() {
            const form = document.querySelector('.chat__form-form');
            const input = form?.querySelector('input[type="text"]');
            const thread = document.getElementById('chatThread');
            const typing = document.getElementById('typing');
            const sendBtn = form?.querySelector('button');
            const container = document.querySelector('.main__block-wrapper'); // сюда повесим класс is-chatting

            if (!form || !input || !thread) return;

            function scrollToBottom() {
                thread.scrollTop = thread.scrollHeight;
            }

            function addMsg(role, text) {
                const wrap = document.createElement('div');
                wrap.className = 'msg ' + (role === 'user' ? 'user' : 'bot');

                if (role !== 'user') {
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

            function toggleTyping(show) {
                typing.hidden = !show;
                if (show) scrollToBottom();
            }

            async function handleSend() {
                const q = (input.value || '').trim();
                if (!q) return;

                // включаем режим чата (карточки скроются по CSS, см. п.2)
                container?.classList.add('is-chatting');

                // добавляем сообщение пользователя
                addMsg('user', q);
                input.value = '';

                // инициализируем историю
                window.chatHistory = window.chatHistory || [];

                // показываем индикатор
                toggleTyping(true);

                try {
                    const body = {
                        message: q,
                        history: window.chatHistory.slice(-3)
                    };
                    const res = await fetch('/api/demo-chat', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(body)
                    });
                    const data = await res.json();

                    const answer = res.ok ? (data.answer || '...') : (data.error || 'Error');
                    addMsg('bot', answer);

                    // сохраняем последние 3 Q/A
                    window.chatHistory.push({
                        q,
                        a: answer
                    });
                    if (window.chatHistory.length > 3) {
                        window.chatHistory = window.chatHistory.slice(-3);
                    }
                } catch (e) {
                    addMsg('bot', 'Network error. Please try again.');
                } finally {
                    toggleTyping(false);
                }
            }


            // обработчики
            form.addEventListener('submit', (e) => {
                e.preventDefault();
                handleSend();
            });
            sendBtn?.addEventListener('click', (e) => {
                e.preventDefault();
                handleSend();
            });

            // Enter в поле
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    handleSend();
                }
            });
        })();
    </script>

</body>

</html>
