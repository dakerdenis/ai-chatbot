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

  <!-- Верхний бренд как в макете -->
  <header class="brand-top container-xxl">
    <div class="brand-mark">Daker<span class="dot">.</span></div>
    <div class="brand-sub">Software Digital Studio</div>
  </header>

  <!-- Центральный «лист» -->
  <main class="sheet-wrap container-xxl">
    <section class="sheet">
      <div class="sheet-head">Version 1.0</div>

      <div class="hero row gx-5 gy-4 align-items-center">
        <!-- Круг-логотип -->
        <div class="col-auto">
          <div class="logo-circle">
            <div class="logo-fallback">LOGO</div>
          </div>
        </div>

        <!-- Заголовки -->
        <div class="col">
          <h1 class="h1 fw-bold mb-1">Hey!</h1>
          <div class="display-5 fw-bold lh-1 mb-1">
            You are welcome to the <span class="grad">D.A.I.</span> Assistant.
          </div>
          <div class="h3 text-muted">How can I help you today?</div>
        </div>
      </div>

      <!-- Три карточки -->
      <div class="row g-4 mt-4 mb-5">
        @foreach([1,2,3] as $i)
        <div class="col-12 col-md-6 col-xl-4">
          <article class="teaser">
            <div class="teaser-badge"><i class="bi bi-people"></i></div>
            <div class="teaser-title">Stock market updates</div>
            <p class="teaser-text">
              Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus pulvinar arcu lacus,
              a maximus purus imperdiet auctor. Integer nisi neque, placerat id suscipit non, convallis eget ipsum.
            </p>
          </article>
        </div>
        @endforeach
      </div>

      <!-- Большая строка ввода -->
      <form class="askbar" onsubmit="return false;">
        <input class="ask-input" placeholder="Type and press ENTER..." />
        <button class="ask-send" type="button" title="Send">
          <i class="bi bi-arrow-right"></i>
        </button>
      </form>

      <div class="sheet-foot text-muted small text-center mt-2">
        D.A.I. — Developed by Daker, powered by AI
      </div>
    </section>
  </main>

  <script>
    // лёгкая подсветка кнопки
    const btn = document.querySelector('.ask-send');
    btn?.addEventListener('pointermove', (e)=>{
      const r = btn.getBoundingClientRect();
      btn.style.setProperty('--x', `${e.clientX - r.left}px`);
      btn.style.setProperty('--y', `${e.clientY - r.top}px`);
    });
  </script>
</body>
</html>
