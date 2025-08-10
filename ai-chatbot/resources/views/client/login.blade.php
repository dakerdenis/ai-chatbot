@php($bodyClass = 'page-auth')
@php($suppressAlerts = true)
@extends('layouts.client')
@section('title','Вход клиента')

@section('content')
  {{-- animated background helpers --}}
  <div class="auth-orb auth-orb--blue"></div>
  <div class="auth-orb auth-orb--violet"></div>
  <div class="auth-orb auth-orb--amber"></div>

  <div class="auth-wrap">
    <div class="auth-card">
      <div class="card shadow-strong">
        <div class="card-body p-4 p-md-5">
          <div class="text-center mb-4">
            <div class="auth-brand">
              <div class="auth-logo"></div>
              <div>
                <div class="display-6 fw-bold auth-title">AI&nbsp;Widget</div>
                <div class="text-muted">Вход в кабинет клиента</div>
              </div>
            </div>
          </div>

          <form method="post" action="{{ route('client.login') }}" novalidate>
            @csrf
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input name="email" type="email" class="form-control form-control-lg" placeholder="you@company.com" required autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label">Пароль</label>
              <input name="password" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
            </div>
            <button class="btn btn-primary btn-lg w-100 btn-submit">
              <i class="bi bi-box-arrow-in-right me-1"></i> Войти
            </button>

            <div class="auth-error">
              @if($errors->any())
                <div class="text-danger">
                  <i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}
                </div>
              @endif
            </div>
          </form>

          <div class="text-center auth-foot mt-1" style="font-size:.92rem">
            Нужна помощь? Свяжитесь с поддержкой.
          </div>
        </div>
      </div>
      <div class="text-center mt-3 auth-foot">© {{ date('Y') }} AI Widget</div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // небольшой “light-follow” на кнопке (без сторонних либ)
  const btn = document.querySelector('.btn-submit');
  if (btn) {
    btn.addEventListener('pointermove', (e) => {
      const r = btn.getBoundingClientRect();
      btn.style.setProperty('--x', `${e.clientX - r.left}px`);
      btn.style.setProperty('--y', `${e.clientY - r.top}px`);
    });
  }
</script>
@endpush
