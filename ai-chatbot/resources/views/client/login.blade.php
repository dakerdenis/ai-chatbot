@extends('layouts.client')
@section('title','Вход клиента')

@push('head')
<style>
  .auth-wrap{min-height:calc(100vh - 84px); display:flex; align-items:center;}
</style>
@endpush

@section('content')
<div class="auth-wrap">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-sm-10 col-md-7 col-lg-5">
        <div class="card glass shadow-strong">
          <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
              <div class="display-6 brand-gradient fw-bold">AI Widget</div>
              <div class="text-secondary">Вход в кабинет клиента</div>
            </div>

            <form method="post" action="{{ route('client.login') }}" novalidate>
              @csrf
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control form-control-lg" placeholder="you@company.com" required autofocus>
              </div>
              <div class="mb-4">
                <label class="form-label">Пароль</label>
                <input name="password" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
              </div>
              <button class="btn btn-primary btn-lg w-100">
                <i class="bi bi-box-arrow-in-right me-1"></i> Войти
              </button>
            </form>

            <div class="text-center text-secondary mt-3" style="font-size:.9rem">
              Нужна помощь? Свяжитесь с поддержкой.
            </div>
          </div>
        </div>
        <div class="text-center mt-3 text-secondary">© {{ date('Y') }} AI Widget</div>
      </div>
    </div>
  </div>
</div>
@endsection
