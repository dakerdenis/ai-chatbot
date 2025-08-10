@extends('layouts.admin')
@section('title','Клиенты')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-3">
  <h3 class="mb-0">Клиенты</h3>
  <div class="d-flex gap-2">
    <a href="{{ route('admin.clients.create') }}" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Создать клиента
    </a>
    <form method="post" action="{{ route('admin.logout') }}">@csrf
      <button class="btn btn-outline-danger"><i class="bi bi-box-arrow-right me-1"></i> Выйти</button>
    </form>
  </div>
</div>

<div class="card glass mb-3">
  <div class="card-body">
    <form class="row g-2">
      <div class="col-sm-6 col-lg-4">
        <input type="text" name="q" class="form-control" placeholder="Поиск по имени/email...">
      </div>
      <div class="col-auto">
        <button class="btn btn-outline-light"><i class="bi bi-search"></i></button>
      </div>
    </form>
  </div>
</div>

<div class="card glass">
  <div class="card-body p-0">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th style="width:70px">ID</th>
            <th>Имя</th>
            <th>Email</th>
            <th>План</th>
            <th>Диалоги</th>
            <th>API токен</th>
            <th style="width:90px"></th>
          </tr>
        </thead>
        <tbody>
        @forelse($clients as $c)
          <tr>
            <td class="text-secondary">#{{ $c->id }}</td>
            <td class="fw-semibold">{{ $c->name }}</td>
            <td class="text-secondary">{{ $c->email }}</td>
            <td><span class="badge bg-info-subtle text-info-emphasis">{{ strtoupper($c->plan) }}</span></td>
            <td><span class="badge bg-secondary-subtle text-secondary-emphasis">{{ $c->dialog_used }}/{{ $c->dialog_limit }}</span></td>
            <td>
              <div class="token-box" tabindex="0" role="button" aria-label="Показать токен">
                <code class="token-value">{{ $c->api_token }}</code>
                <i class="bi bi-clipboard ms-2 text-secondary"></i>
              </div>
            </td>
            <td class="text-end">
              <a href="{{ route('admin.clients.edit',$c) }}" class="btn btn-sm btn-outline-light">
                <i class="bi bi-pencil-square"></i>
              </a>
            </td>
          </tr>
        @empty
          <tr><td colspan="7" class="text-center text-secondary py-4">Нет клиентов</td></tr>
        @endforelse
        </tbody>
      </table>
    </div>
  </div>
  @if(method_exists($clients,'links'))
    <div class="card-footer bg-transparent">
      {{ $clients->links() }}
    </div>
  @endif
</div>
@endsection

@push('scripts')
<script>
  // клик по токену — копировать; наведение — показать
  document.querySelectorAll('.token-box').forEach(box=>{
    const val = box.querySelector('.token-value');
    box.addEventListener('click', ()=>{
      navigator.clipboard.writeText(val?.innerText || '');
      box.classList.add('border','border-success');
      setTimeout(()=> box.classList.remove('border','border-success'), 800);
    });
    let touch=false;
    box.addEventListener('pointerdown', ()=>{ touch=true; box.classList.add('reveal'); });
    window.addEventListener('pointerup', ()=>{ if(touch){ box.classList.remove('reveal'); touch=false; }});
  });
</script>
@endpush
