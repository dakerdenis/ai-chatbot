@extends('layouts.client')
@section('title','Кабинет клиента')

@section('content')
<div class="row g-4">
  {{-- Левая колонка --}}
  <div class="col-12 col-lg-8">
    {{-- Общая информация --}}
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-0 fw-semibold">Общая информация</h5>
          <span class="badge bg-primary fs-6 px-3 py-2">{{ strtoupper($client->plan) }}</span>
        </div>
        <div class="text-muted mb-3">Здравствуйте, <span class="fw-semibold">{{ $client->name }}</span>!</div>

        {{-- Прогресс использования --}}
        <div class="mb-2 d-flex justify-content-between small text-muted">
          <span>Использование диалогов</span>
          <span>{{ $client->dialog_used }} / {{ $client->dialog_limit }}</span>
        </div>
        @php
          $pct = $client->dialog_limit ? min(100, round($client->dialog_used / $client->dialog_limit * 100)) : 0;
        @endphp
        <div class="progress mb-3" style="height:16px; border-radius:8px;">
          <div class="progress-bar bg-info fw-semibold" style="width: {{ $pct }}%;">{{ $pct }}%</div>
        </div>

        {{-- Метрики --}}
        <div class="row g-3">
          <div class="col-md-4">
            <div class="metric-box">
              <div class="text-muted small mb-1">Макс. промтов</div>
              <div class="metric-value">{{ $client->prompts_limit }}</div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="metric-box">
              <div class="text-muted small mb-1">Длина промта</div>
              <div class="metric-value">{{ $client->prompt_max_length }} <span class="metric-unit">симв.</span></div>
            </div>
          </div>
          <div class="col-md-4">
            <div class="metric-box">
              <div class="text-muted small mb-1">Rate limit</div>
              <div class="metric-value">{{ $client->rate_limit }}<span class="metric-unit">/мин</span></div>
            </div>
          </div>
        </div>

        {{-- Кнопки --}}
        <div class="mt-4">
          <a href="{{ route('client.prompts.index') }}" class="btn btn-primary">
            <i class="bi bi-sliders me-1"></i> Управлять промтами
          </a>
        </div>
      </div>
    </div>

    {{-- Домены --}}
    <div class="card shadow-sm border-0 mt-4">
      <div class="card-body">
        <h5 class="mb-3 fw-semibold">Домены</h5>
        @if($client->domains->isEmpty())
          <div class="text-muted">Домены не добавлены. Обратитесь к администратору.</div>
        @else
          <div class="d-flex flex-wrap gap-2">
            @foreach($client->domains as $d)
              <span class="badge bg-light text-dark border">{{ $d->domain }}</span>
            @endforeach
          </div>
        @endif
      </div>
    </div>
  </div>

  {{-- Правая колонка --}}
  <div class="col-12 col-lg-4">
    {{-- API токен --}}
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <h6 class="mb-2 fw-semibold">API токен</h6>
        <div class="small text-muted mb-2">Наведите или коснитесь, чтобы увидеть. Клик — скопировать.</div>
    
        <div id="tokenBox" class="token-box copyable" role="button" tabindex="0" aria-label="Показать и скопировать API токен">
          <code class="token-value" id="tokenValue">{{ $client->api_token }}</code>
          <i class="bi bi-clipboard ms-2 text-secondary"></i>
        </div>
      </div>
    </div>
    

    {{-- Интеграция --}}
    <div class="card shadow-sm border-0 mt-4">
      <div class="card-body">
        <h6 class="mb-2 fw-semibold">Интеграция виджета</h6>
        <div class="small text-muted mb-2">Вставьте на ваш сайт:</div>
        <pre class="small bg-light p-2 rounded border mb-2"><code>&lt;script src="{{ url('/widget.js') }}" data-api-token="{{ $client->api_token }}"&gt;&lt;/script&gt;</code></pre>
        <div class="text-muted small">Убедитесь, что ваш домен добавлен в список разрешённых.</div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  const box = document.getElementById('tokenBox');
  const val = document.getElementById('tokenValue');

  // Копирование по клику
  box?.addEventListener('click', () => {
    const v = val?.innerText || '';
    navigator.clipboard.writeText(v).then(() => {
      box.classList.add('border-success');
      setTimeout(()=> box.classList.remove('border-success'), 800);
    });
  });

  // Для тач-устройств: показывать при нажатии, скрывать при отпускании
  let touched = false;
  box?.addEventListener('pointerdown', () => { touched = true; box.classList.add('reveal'); });
  window.addEventListener('pointerup', () => { if (touched) { box.classList.remove('reveal'); touched = false; }});
</script>
@endpush

