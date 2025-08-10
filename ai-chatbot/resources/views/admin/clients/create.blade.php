@extends('layouts.admin')
@section('title','Создать клиента')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h3 class="mb-0">Создать клиента</h3>
  <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-light">
    <i class="bi bi-arrow-left"></i> Назад к списку
  </a>
</div>

<div class="card glass">
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger mb-4"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
    @endif

    <form method="post" action="{{ route('admin.clients.store') }}" class="row g-4">
      @csrf

      <div class="col-md-6">
        <label class="form-label">Имя</label>
        <input name="name" class="form-control" value="{{ old('name') }}" required>
        @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" value="{{ old('email') }}" required>
        @error('email') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Пароль</label>
        <input name="password" type="password" class="form-control" placeholder="Минимум 8 символов" required>
        @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
        <div class="form-text">Пароль можно сменить позже на странице редактирования.</div>
      </div>

      <div class="col-md-6">
        <label class="form-label">План</label>
        <select name="plan" class="form-select">
          @foreach(['trial','basic','standard','premium'] as $p)
            <option value="{{ $p }}" @selected(old('plan')===$p)>{{ strtoupper($p) }}</option>
          @endforeach
        </select>
        @error('plan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">Диалоги всего</label>
        <input name="dialog_limit" type="number" class="form-control" value="{{ old('dialog_limit',200) }}" min="0">
        @error('dialog_limit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">Промтов максимум</label>
        <input name="prompts_limit" type="number" class="form-control" value="{{ old('prompts_limit',1) }}" min="0">
        @error('prompts_limit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">Длина промта</label>
        <div class="input-group">
          <input name="prompt_max_length" type="number" class="form-control" value="{{ old('prompt_max_length',300) }}" min="1">
          <span class="input-group-text">симв.</span>
        </div>
        @error('prompt_max_length') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3">
        <label class="form-label">Rate limit</label>
        <div class="input-group">
          <input name="rate_limit" type="number" class="form-control" value="{{ old('rate_limit',30) }}" min="1">
          <span class="input-group-text">/мин</span>
        </div>
        @error('rate_limit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-6">
        <label class="form-label">Домены (через запятую)</label>
        <input name="domains" class="form-control" value="{{ old('domains') }}" placeholder="example.com, foo.bar">
        <div class="form-text">Будут записаны в таблицу доменов клиента.</div>
        @error('domains') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
      </div>

      <div class="col-md-3 d-flex align-items-end">
        <div class="form-check form-switch">
          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" {{ old('is_active',true) ? 'checked' : '' }}>
          <label class="form-check-label" for="is_active">Активен</label>
        </div>
      </div>

      <div class="col-12">
        <button class="btn btn-primary">
          <i class="bi bi-check2-circle me-1"></i> Создать
        </button>
        <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-light ms-2">Отмена</a>
      </div>
    </form>
  </div>
</div>
@endsection
