@extends('layouts.client')
@section('title','Кабинет клиента')

@section('content')
<div class="row g-4">
  <div class="col-12 col-lg-8">
    <div class="card glass shadow-strong">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-0">Общая информация</h5>
          <span class="badge text-bg-primary">{{ strtoupper($client->plan) }}</span>
        </div>
        <div class="text-secondary mb-3">Здравствуйте, <span class="fw-semibold">{{ $client->name }}</span>!</div>

        <div class="mb-2 d-flex justify-content-between small text-secondary">
          <span>Использование диалогов</span>
          <span>{{ $client->dialog_used }} / {{ $client->dialog_limit }}</span>
        </div>
        @php
          $pct = $client->dialog_limit ? min(100, round($client->dialog_used/$client->dialog_limit*100)) : 0;
        @endphp
        <div class="progress mb-3" role="progressbar" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100" style="height:14px;border-radius:10px;">
          <div class="progress-bar bg-info" style="width: {{ $pct }}%">{{ $pct }}%</div>
        </div>

        <div class="row g-3">
          <div class="col-md-4">
            <div class="p-3 glass rounded h-100">
              <div class="text-secondary small mb-1">Макс. промтов</div>
              <div class="h5 mb-0">{{ $client->prompts_limit }}</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 glass rounded h-100">
              <div class="text-secondary small mb-1">Длина промта</div>
              <div class="h5 mb-0">{{ $client->prompt_max_length }} символов</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="p-3 glass rounded h-100">
              <div class="text-secondary small mb-1">Rate limit</div>
              <div class="h5 mb-0">{{ $client->rate_limit }}/мин</div>
            </div>
          </div>
        </div>

        <div class="mt-4">
          <a href="{{ route('client.prompts.index') }}" class="btn btn-primary">
            <i class="bi bi-sliders me-1"></i> Управлять промтами
          </a>
          <a href="{{ url('/widget.js') }}" class="btn btn-outline-light ms-2" target="_blank">
            <i class="bi bi-braces me-1"></i> widget.js
          </a>
        </div>
      </div>
    </div>

    <div class="card glass shadow-strong mt-4">
      <div class="card-body">
        <h5 class="mb-3">Домены</h5>
        @if($client->domains->isEmpty())
          <div class="text-secondary">Домены не добавлены. Обратитесь к администратору.</div>
        @else
          <div class="d-flex flex-wrap gap-2">
            @foreach($client->domains as $d)
              <span class="badge text-bg-secondary">{{ $d->domain }}</span>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

  <div class="col-12 col-lg-4">
    <div class="card glass shadow-strong">
      <div class="card-body">
        <h6 class="mb-2">API токен</h6>
        <div class="small text-secondary mb-2">Наведитесь, чтобы увидеть. Клик — копировать.</div>
        <div id="tokenBox" class="p-3 border rounded d-flex align-items-center justify-content-between copyable">
          <code class="token-blur" id="tokenValue">{{ $client->api_token }}</code>
          <i class="bi bi-clipboard ms-2"></i>
        </div>
      </div>
    </div>

    <div class="card glass shadow-strong mt-4">
      <div class="card-body">
        <h6 class="mb-2">Интеграция виджета</h6>
        <div class="small text-secondary mb-2">Вставьте на ваш сайт:</div>
        <pre class="small mb-2"><code>&lt;script src="{{ url('/widget.js') }}" data-api-token="{{ $client->api_token }}"&gt;&lt;/script&gt;</code></pre>
        <div class="text-secondary small">Убедитесь, что ваш домен добавлен в список разрешённых.</div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  document.getElementById('tokenBox')?.addEventListener('click', () => {
    const v = document.getElementById('tokenValue')?.innerText || '';
    navigator.clipboard.writeText(v).then(() => {
      const t = document.getElementById('tokenBox');
      t.classList.add('border-success');
      setTimeout(()=> t.classList.remove('border-success'), 800);
    });
  });
</script>
@endpush
