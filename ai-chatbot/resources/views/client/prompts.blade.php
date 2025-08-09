<!doctype html><meta charset="utf-8"><title>Промты</title>
<h1>Промты клиента</h1>
@if(session('success'))<div style="color:green">{{ session('success') }}</div>@endif
@if($errors->any())<div style="color:red">{{ $errors->first() }}</div>@endif

<h2>Добавить промт</h2>
<form method="post" action="{{ route('client.prompts.store') }}">
  @csrf
  <input name="title" placeholder="Заголовок" required>
  <br>
  <textarea name="content" rows="6" cols="60" placeholder="Текст (до {{ $client->prompt_max_length }})" required></textarea>
  <br><button>Сохранить</button>
</form>

<h2>Сжать промт</h2>
<textarea id="src" rows="4" cols="60" placeholder="Вставьте длинный текст"></textarea><br>
<button id="compress">Сжать</button>
<pre id="out"></pre>
<script>
document.getElementById('compress').onclick = async () => {
  const text = document.getElementById('src').value.trim();
  if(!text) return;
  const res = await fetch('{{ route('client.prompts.compress') }}', {
    method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'{{ csrf_token() }}'},
    body: JSON.stringify({text})
  });
  const data = await res.json();
  document.getElementById('out').textContent = data.success ? data.result : ('Ошибка: '+data.error);
};
</script>

<h2>Список</h2>
@foreach($prompts as $p)
  <form method="post" action="{{ route('client.prompts.update',$p) }}">
    @csrf @method('PUT')
    <strong>{{ $p->title }}</strong><br>
    <textarea name="content" rows="4" cols="60">{{ $p->content }}</textarea><br>
    <button>Обновить</button>
  </form>
  <form method="post" action="{{ route('client.prompts.destroy',$p) }}" onsubmit="return confirm('Удалить?')">
    @csrf @method('DELETE')
    <button>Удалить</button>
  </form>
  <hr>
@endforeach
