@php($bodyClass = 'page-auth')
@php($suppressAlerts = true)
@extends('layouts.admin')
@section('title','Вход администратора')

@section('content')
<div class="auth-card">
  <div class="card glass">
    <div class="card-body p-4 p-md-5">
      <div class="text-center mb-4">
        <div class="display-6 brand-gradient fw-bold">Admin</div>
        <div class="text-secondary">Вход в панель управления</div>
      </div>

      <form method="post" action="{{ route('admin.login') }}" novalidate>
        @csrf
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input name="email" type="email" class="form-control form-control-lg" placeholder="admin@company.com" required autofocus>
        </div>
        <div class="mb-3">
          <label class="form-label">Пароль</label>
          <input name="password" type="password" class="form-control form-control-lg" placeholder="••••••••" required>
        </div>
        <button class="btn btn-primary btn-lg w-100"><i class="bi bi-box-arrow-in-right me-1"></i> Войти</button>

        <div class="mt-2" style="min-height:24px">
          @if($errors->any())
            <div class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>{{ $errors->first() }}</div>
          @endif
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
