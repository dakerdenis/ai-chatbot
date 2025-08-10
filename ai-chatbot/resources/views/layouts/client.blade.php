<!doctype html>
<html lang="ru" data-bs-theme="light">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Кабинет клиента')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="{{ asset('assets/client.css') }}" rel="stylesheet">

  @stack('head')
</head>
<body class="{{ $bodyClass ?? '' }}">

  @if(session('client_id'))
  <nav class="navbar navbar-expand-lg bg-white border-bottom fixed-top shadow-sm">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="{{ route('client.dashboard') }}">
        <i class="bi bi-robot text-primary"></i> AI Widget
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="topnav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('client.dashboard')?'active':'' }}" href="{{ route('client.dashboard') }}">
              <i class="bi bi-speedometer2 me-1"></i> Дашборд
            </a>
          </li>
          <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('client.prompts.*')?'active':'' }}" href="{{ route('client.prompts.index') }}">
              <i class="bi bi-sliders me-1"></i> Промты
            </a>
          </li>
        </ul>
        <form method="post" action="{{ route('client.logout') }}" class="d-flex">@csrf
          <button class="btn btn-outline-danger">
            <i class="bi bi-box-arrow-right me-1"></i> Выйти
          </button>
        </form>
      </div>
    </div>
  </nav>
  @endif

  <div class="content-wrap">
    <div class="container">
      @unless(!empty($suppressAlerts))
        @if(session('success'))
          <div class="alert alert-success mt-3"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
        @endif
        @if($errors->any() && empty($suppressAlerts))
          <div class="alert alert-danger mt-3"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
        @endif
      @endunless

      @yield('content')
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
