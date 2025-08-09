@extends('layouts.client')
@section('title','Промты')

@section('content')
<div class="row g-4">
  <div class="col-lg-5">
    <div class="card glass shadow-strong">
      <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-plus-circle me-1"></i>Добавить промт</h5>
        <form method="post" action="{{ route('client.prompts.store') }}" class="needs-validation" novalidate>
          @csrf
          <div class="mb-3">
            <label class="form-label">Заголовок</label>
            <input name="title" class="form-control" required placeholder="Например: Контакты / Услуги">
            <div class="invalid-feedback">Заполните заголовок</div>
          </div>
          <div class="mb-3">
            <label class="form-label">Текст (до {{ $client->prompt_max_length }})</label>
            <textarea name="content" rows="6" class="form-control" required></textarea>
            <div class="form-text text-secondary">Кратко и по делу. Из этого формируется база знаний чата.</div>
          </div>
          <button class="btn btn-primary"><i class="bi bi-save2 me-1"></i>Сохранить</button>
        </form>
      </div>
    </div>

    <div class="card glass shadow-strong mt-4">
      <div class="card-body">
        <h5 class="mb-3"><i class="bi bi-arrows-collapse me-1"></i>Сжать промт</h5>
        <div class="mb-3">
          <textarea id="src" rows="5" class="form-control" placeholder="Вставьте длинный текст для сжатия"></textarea>
        </div>
        <div class="d-flex gap-2">
          <button id="compress" class="btn btn-outline-light">
            <span class="spinner-border spinner-border-sm me-2 d-none" id="compressSpinner"></span>
            Сжать
          </button>
          <button id="copyCompressed" class="btn btn-outline-secondary" disabled><i class="bi bi-clipboard me-1"></i>Скопировать</button>
        </div>
        <div class="mt-3">
          <label class="form-label">Результат</label>
          <textarea id="out" rows="5" class="form-control" readonly></textarea>
        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-7">
    <div class="card glass shadow-strong">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <h5 class="mb-0"><i class="bi bi-list-ul me-1"></i>Ваши промты</h5>
          <span class="text-secondary small">Лимит: {{ $client->prompts()->count() }} / {{ $client->prompts_limit }}</span>
        </div>

        @forelse($prompts as $p)
          <div class="border rounded p-3 mb-3">
            <div class="d-flex align-items-center justify-content-between">
              <strong class="me-2">{{ $p->title }}</strong>
              <form method="post" action="{{ route('client.prompts.destroy',$p) }}" onsubmit="return confirm('Удалить промт?')" class="ms-auto">
                @csrf @method('DELETE')
                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
              </form>
            </div>
            <form method="post" action="{{ route('client.prompts.update',$p) }}" class="mt-2">
              @csrf @method('PUT')
              <textarea name="content" rows="4" class="form-control mb-2">{{ $p->content }}</textarea>
              <div class="d-flex gap-2">
                <button class="btn btn-outline-light"><i class="bi bi-save me-1"></i>Обновить</button>
                <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(this.closest('form').querySelector('textarea').value)">
                  <i class="bi bi-clipboard me-1"></i>Копировать
                </button>
              </div>
            </form>
          </div>
        @empty
          <div class="text-secondary">Пока нет промтов — добавьте первый слева.</div>
        @endforelse
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  // валидация форм Bootstrap
  (() => {
    const forms = document.querySelectorAll('.needs-validation');
    Array.from(forms).forEach(f => {
      f.addEventListener('submit', e => {
        if (!f.checkValidity()) { e.preventDefault(); e.stopPropagation(); }
        f.classList.add('was-validated');
      }, false);
    });
  })();

  // Сжатие промта
  const btn = document.getElementById('compress');
  const spin = document.getElementById('compressSpinner');
  const src = document.getElementById('src');
  const out = document.getElementById('out');
  const copyBtn = document.getElementById('copyCompressed');

  btn?.addEventListener('click', async () => {
    const text = src.value.trim(); if(!text) return;
    spin.classList.remove('d-none'); btn.disabled = true; copyBtn.disabled = true; out.value = '';
    try{
      const res = await fetch('{{ route('client.prompts.compress') }}', {
        method:'POST',
        headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
        body: JSON.stringify({ text })
      });
      const data = await res.json();
      out.value = data.success ? (data.result || '') : ('Ошибка: ' + (data.error || 'unknown'));
      copyBtn.disabled = !data.success || !out.value.trim();
    }catch(err){
      out.value = 'Ошибка сети';
    }finally{
      spin.classList.add('d-none'); btn.disabled = false;
    }
  });

  copyBtn?.addEventListener('click', async () => {
    if(!out.value.trim()) return;
    await navigator.clipboard.writeText(out.value);
    copyBtn.classList.add('btn-success'); setTimeout(()=>copyBtn.classList.remove('btn-success'), 700);
  });
</script>
@endpush
