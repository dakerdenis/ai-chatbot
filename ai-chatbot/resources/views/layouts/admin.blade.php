<!doctype html>
<html lang="ru" data-bs-theme="dark">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title','Admin')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="{{ asset('assets/admin.css') }}" rel="stylesheet">
  @stack('head')
</head>
<body class="{{ $bodyClass ?? '' }}">

  @if(session('admin_id'))
  <nav class="navbar navbar-expand-lg glass fixed-top">
    <div class="container">
      <a class="navbar-brand fw-semibold" href="{{ url('/admin/dashboard') }}">
        <i class="bi bi-shield-lock"></i> <span class="brand-gradient">Admin</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topnav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="topnav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item"><a class="nav-link {{ request()->is('admin/dashboard')?'active':'' }}" href="{{ url('/admin/dashboard') }}"><i class="bi bi-speedometer me-1"></i>Дашборд</a></li>
          <li class="nav-item"><a class="nav-link {{ request()->is('admin/clients*')?'active':'' }}" href="{{ route('admin.clients.index') }}"><i class="bi bi-people me-1"></i>Клиенты</a></li>
        </ul>
        <form method="post" action="{{ route('admin.logout') }}" class="d-flex">@csrf
          <button class="btn btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i>Выйти</button>
        </form>
      </div>
    </div>
  </nav>
  @endif

  <div class="content-wrap">
    <div class="container">
      @if(session('success'))
        <div class="alert alert-success glass"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
      @endif
      @if($errors->any() && empty($suppressAlerts))
        <div class="alert alert-danger glass"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
      @endif

      @yield('content')
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
