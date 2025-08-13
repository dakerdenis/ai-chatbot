<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>D.A.I. — Chat</title>

    {{-- Стили старого виджета (скопируй файл в public/assets/style/chat-bot.css) --}}
    <link rel="stylesheet" href="{{ asset('assets/chat-bot.css') }}">

    {{-- (необязательно) чуть разгружаем шрифты/рендер --}}
    <meta name="color-scheme" content="light dark">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <div class="widget-container">
        <div class="widget__header__container">
            <div class="widget__header__container-content">
                <div class="widget-header">
                    <div class="header-info">
                        <img src="{{ asset('assets/images/dai-logo.png') }}" alt="Logo" class="logo" />
                        <div>
                            <div class="title">D.A.I. Chat bot</div>
                            <div class="subtitle">Daker Artificial Intelligence</div>
                        </div>
                    </div>

                    <div class="widget-actions">
                        <button class="widget-menu" id="menuBtn" aria-label="Menu">⋯</button>
                        <div class="menu-panel" id="menuPanel" hidden>
                            <a class="menu-item" href="https://dai.daker.az" target="_blank" rel="noopener">Visit
                                website</a>

                            <!-- NEW: fancy switch -->
                            <button type="button" class="menu-item theme-switch" id="themeSwitch" aria-pressed="false">
                                <span class="ts-icon" aria-hidden="true"></span>
                                <span class="ts-label">Dark theme</span>
                            </button>
                        </div>

                        <button class="widget-close" onclick="window.parent.postMessage('close-chat', '*')">✕</button>
                    </div>

                </div>


                <div class="intro">
                    <p class="welcome">Salam və xoş gəlmisiniz!</p>
                    <p class="instruction">İstədiyiniz sualları verə bilərsiniz.</p>
                </div>
            </div>

            <div class="widget__header__container-image">
                <img src="{{ asset('assets/images/back.png') }}" alt="">
            </div>
        </div>

        <div class="widget-chat-area">
            <div id="chat" class="widget-body"></div>
        </div>

        <div class="widget-footer">
            <form id="form" autocomplete="off">
                <input type="text" maxlength="200" id="message" placeholder="Yazın və ENTER düyməsini basın..." />
                <button class="send-message-btn" type="submit">
                    <img src="{{ asset('assets/images/arrow.svg') }}" alt="">
                </button>
            </form>
        </div>
    </div>

    <audio id="chat-sound" src="{{ asset('assets/sounds/button-3.mp3') }}" preload="auto"></audio>

    <script>

        const SITE    = @json($site); 
        // данные клиента
        const token = @json($client->api_token);
        const chat = document.getElementById('chat');
        const form = document.getElementById('form');
        const input = document.getElementById('message');

        // локальная история в DOM (как в старом коде)
        const STORAGE_KEY = 'chat_history_{{ $client->id }}';
        const TIMESTAMP_KEY = 'chat_timestamp_{{ $client->id }}';
        const MAX_AGE_MS = 2 * 24 * 60 * 60 * 1000; // 2 дня
        const history = []; // для передачи последних 3 Q/A в API

        function saveChat() {
            localStorage.setItem(STORAGE_KEY, chat.innerHTML);
            localStorage.setItem(TIMESTAMP_KEY, Date.now().toString());
        }

        function loadChat() {
            const saved = localStorage.getItem(STORAGE_KEY);
            const ts = localStorage.getItem(TIMESTAMP_KEY);
            if (saved && ts && Date.now() - parseInt(ts, 10) <= MAX_AGE_MS) {
                chat.innerHTML = saved;
                chat.scrollTop = chat.scrollHeight;
                return true;
            }
            localStorage.removeItem(STORAGE_KEY);
            localStorage.removeItem(TIMESTAMP_KEY);
            return false;
        }

        window.addEventListener('DOMContentLoaded', () => {
            const had = loadChat();
            if (!had) {
                chat.innerHTML += `
          <div class="msg-row bot">
            <div class="msg bot">Salam! Mən D.A.I. köməkçisiyəm. Sizə necə kömək edə bilərəm?</div>
          </div>`;
                saveChat();
            }
            chat.scrollTop = chat.scrollHeight;
        });

        let isSending = false;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            if (isSending) return;
            const text = (input.value || '').trim();
            if (!text) return;
            if (text.length > 200) {
                alert('Mesaj 200 simvoldan çox olmamalıdır.');
                return;
            }

            isSending = true;
            input.value = '';
            document.getElementById('chat-sound')?.play();

            chat.innerHTML += `
        <div class="msg-row me">
          <div class="msg me message-me"></div>
        </div>`;
            chat.querySelector('.msg-row.me:last-child .msg').textContent = text;

            // индикатор
            const typing = document.createElement('div');
            typing.className = 'msg-row bot typing';
            typing.innerHTML = `<div class="msg">Bot yazır...</div>`;
            chat.appendChild(typing);
            chat.scrollTop = chat.scrollHeight;
            saveChat();

            try {
                const payload = {
                    message: text,
                    history: history.slice(-3) // {q,a}
                };
const res = await fetch('/api/public-chat', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-API-TOKEN': token,
    'X-CLIENT-SITE': SITE || location.hostname   // <-- ВАЖНО
  },
  body: JSON.stringify(payload),
  credentials: 'omit'
});
                const data = await res.json().catch(() => ({}));
                typing.remove();

                const answer = res.ok ? (data.answer || '…') :
                    (data.error || 'Xəta baş verdi');

                chat.innerHTML += `
          <div class="msg-row bot">
            <div class="msg bot message-bot"></div>
          </div>`;
                chat.querySelector('.msg-row.bot:last-child .msg').textContent = answer;

                chat.scrollTop = chat.scrollHeight;
                saveChat();

                history.push({
                    q: text,
                    a: answer
                });
                if (history.length > 3) history.splice(0, history.length - 3);
            } catch (err) {
                typing.remove();
                chat.innerHTML += `<div class="msg bot">❌ Şəbəkə xətası</div>`;
            } finally {
                isSending = false;
            }
        });

        // закрытие из родителя
        window.addEventListener('message', (e) => {
            if (e.data === 'close-chat') {
                window.parent.postMessage('close-chat', '*');
            }
        });
    </script>


    <script>
        (function() {
            const root = document.documentElement;
            const menuBtn = document.getElementById('menuBtn');
            const menuPanel = document.getElementById('menuPanel');
            const switchBtn = document.getElementById('themeSwitch');

            const KEY = 'aiw_theme';
            const saved = (localStorage.getItem(KEY) || 'light');
            applyTheme(saved);

            function applyTheme(mode) {
                const dark = (mode === 'dark');
                root.setAttribute('data-theme', dark ? 'dark' : 'light');
                localStorage.setItem(KEY, dark ? 'dark' : 'light');
                if (switchBtn) {
                    switchBtn.setAttribute('aria-pressed', String(dark));
                    switchBtn.classList.toggle('is-dark', dark);
                    switchBtn.querySelector('.ts-label').textContent = dark ? 'Light theme' : 'Dark theme';
                }
            }

            // toggle theme
            switchBtn?.addEventListener('click', () => {
                const nowDark = root.getAttribute('data-theme') !== 'dark' ? 'dark' : 'light';
                applyTheme(nowDark);
            });

            // menu open/close
            function openMenu() {
                menuPanel.hidden = false;
            }

            function closeMenu() {
                menuPanel.hidden = true;
            }

            menuBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                menuPanel.hidden ? openMenu() : closeMenu();
            });
            document.addEventListener('click', (e) => {
                if (!menuPanel.hidden && !menuPanel.contains(e.target) && e.target !== menuBtn) closeMenu();
            });
        })();
    </script>


</body>

</html>
