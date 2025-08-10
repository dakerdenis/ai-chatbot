@extends('layouts.admin')
@section('title','Редактировать клиента #'.$client->id)

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h3 class="mb-0">Редактировать клиента #{{ $client->id }}</h3>
  <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-light">
    <i class="bi bi-arrow-left"></i> Назад к списку
  </a>
</div>

@if(session('success'))
  <div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>{{ session('success') }}</div>
@endif
@if($errors->any())
  <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>{{ $errors->first() }}</div>
@endif

<div class="row g-4">
  <div class="col-12 col-xl-8">
    <div class="card glass">
      <div class="card-body">
        <form method="post" action="{{ route('admin.clients.update',$client) }}" class="row g-4">
          @csrf @method('PUT')

          <div class="col-md-6">
            <label class="form-label">Имя</label>
            <input name="name" class="form-control" value="{{ old('name',$client->name) }}" required>
            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input class="form-control" value="{{ $client->email }}" disabled>
            <div class="form-text">Email изменить нельзя.</div>
          </div>

          <div class="col-md-6">
            <label class="form-label">Новый пароль (опционально)</label>
            <input name="password" type="password" class="form-control" placeholder="Оставьте пустым, чтобы не менять">
            @error('password') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">План</label>
            <select name="plan" class="form-select">
              @foreach(['trial','basic','standard','premium'] as $p)
                <option value="{{ $p }}" @selected(old('plan',$client->plan)===$p)>{{ strtoupper($p) }}</option>
              @endforeach
            </select>
            @error('plan') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label">Диалоги всего</label>
            <input name="dialog_limit" type="number" class="form-control" value="{{ old('dialog_limit',$client->dialog_limit) }}" min="0">
            @error('dialog_limit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label">Промтов максимум</label>
            <input name="prompts_limit" type="number" class="form-control" value="{{ old('prompts_limit',$client->prompts_limit) }}" min="0">
            @error('prompts_limit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label">Длина промта</label>
            <div class="input-group">
              <input name="prompt_max_length" type="number" class="form-control" value="{{ old('prompt_max_length',$client->prompt_max_length) }}" min="1">
              <span class="input-group-text">симв.</span>
            </div>
            @error('prompt_max_length') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-3">
            <label class="form-label">Rate limit</label>
            <div class="input-group">
              <input name="rate_limit" type="number" class="form-control" value="{{ old('rate_limit',$client->rate_limit) }}" min="1">
              <span class="input-group-text">/мин</span>
            </div>
            @error('rate_limit') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-6">
            <label class="form-label">Домены (через запятую)</label>
            <input name="domains" class="form-control" value="{{ old('domains',$domains) }}" placeholder="example.com, foo.bar">
            <div class="form-text">Заменит все домены клиента указанным списком.</div>
            @error('domains') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
          </div>

          <div class="col-md-3 d-flex align-items-end">
            <div class="form-check form-switch">
              <input class="form-check-input" type="checkbox" id="is_active" name="is_active" @checked(old('is_active',$client->is_active))>
              <label class="form-check-label" for="is_active">Активен</label>
            </div>
          </div>

          <div class="col-12">
            <button class="btn btn-primary">
              <i class="bi bi-check2-circle me-1"></i> Сохранить
            </button>
            <a href="{{ route('admin.clients.index') }}" class="btn btn-outline-light ms-2">Отмена</a>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- Правая колонка: API токен + удаление --}}
  <div class="col-12 col-xl-4">
    <div class="card glass mb-4">
      <div class="card-body">
        <h6 class="fw-semibold mb-2">API токен</h6>
        <div class="small text-secondary mb-2">Наведите, чтобы увидеть. Клик — скопировать.</div>
        <div id="tokenBox" class="token-box" role="button" tabindex="0">
          <code class="token-value" id="tokenValue">{{ $client->api_token }}</code>
          <i class="bi bi-clipboard ms-2 text-secondary"></i>
        </div>
      </div>
    </div>

    <div class="card glass">
      <div class="card-body">
        <h6 class="fw-semibold mb-3 text-danger"><i class="bi bi-trash3 me-1"></i> Опасная зона</h6>
        <form method="post" action="{{ route('admin.clients.destroy',$client) }}" onsubmit="return confirm('Удалить клиента? Это действие необратимо.')">
          @csrf @method('DELETE')
          <button class="btn btn-outline-danger w-100">Удалить клиента</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const box = document.getElementById('tokenBox');
  const val = document.getElementById('tokenValue');
  box?.addEventListener('click', () => {
    const v = val?.innerText || '';
    navigator.clipboard.writeText(v).then(() => {
      box.classList.add('border','border-success');
      setTimeout(()=> box.classList.remove('border','border-success'), 800);
    });
  });
  let t=false;
  box?.addEventListener('pointerdown', ()=>{ t=true; box.classList.add('reveal'); });
  window.addEventListener('pointerup', ()=>{ if(t){ box.classList.remove('reveal'); t=false; }});
</script>
@endpush
