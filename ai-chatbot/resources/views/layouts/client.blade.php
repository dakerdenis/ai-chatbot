<!doctype html>
<html lang="ru" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Кабинет клиента')</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Иконки -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <style>
    body{
      min-height:100vh;
      background:
        radial-gradient(1200px 600px at 100% -20%, rgba(17,24,39,.35), transparent 60%),
        radial-gradient(1000px 500px at -10% 120%, rgba(99,102,241,.25), transparent 60%),
        linear-gradient(#0b0c10, #0f1117);
    }
    .glass {
      background: rgba(255,255,255,.04);
      border: 1px solid rgba(255,255,255,.08);
      backdrop-filter: blur(10px);
    }
    .brand-gradient {
      background: linear-gradient(90deg, #60a5fa, #a78bfa, #f472b6);
      -webkit-background-clip: text; background-clip: text; color: transparent;
    }
    .content-wrap { padding-top: 84px; padding-bottom: 40px; }
    .form-control, .form-select, .btn { border-radius: 12px; }
    .card { border-radius: 14px; }
    .copyable { cursor: pointer; }
    .token-blur{ filter: blur(5px); transition: filter .15s; }
    .token-blur:hover{ filter:none; }
    .shadow-strong{ box-shadow: 0 20px 50px rgba(0,0,0,.4) }
  </style>
  @stack('head')
</head>
<body>

<nav class="navbar navbar-expand-lg glass fixed-top">
  <div class="container">
    <a class="navbar-brand fw-semibold" href="{{ route('client.dashboard') }}">
      <i class="bi bi-robot"></i> <span class="brand-gradient">AI Widget</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="topnav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('client.dashboard')?'active':'' }}" href="{{ route('client.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Дашборд</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('client.prompts.*')?'active':'' }}" href="{{ route('client.prompts.index') }}"><i class="bi bi-sliders me-1"></i>Промты</a></li>
      </ul>
      <form method="post" action="{{ route('client.logout') }}" class="d-flex">@csrf
        <button class="btn btn-outline-light"><i class="bi bi-box-arrow-right me-1"></i>Выйти</button>
      </form>
    </div>
  </div>
</nav>

<div class="content-wrap">
  <div class="container">
    @if(session('success'))
      <div class="alert alert-success glass shadow-strong"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-danger glass shadow-strong"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
    @endif

    @yield('content')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
