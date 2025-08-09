<!doctype html><meta charset="utf-8"><title>Редактировать клиента</title>
<h1>Редактировать клиента #{{ $client->id }}</h1>
@if(session('success'))<div style="color:green">{{ session('success') }}</div>@endif
@if($errors->any())<div style="color:red">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ route('admin.clients.update',$client) }}">
@csrf @method('PUT')
Имя: <input name="name" value="{{ $client->name }}"><br>
Пароль (если сменить): <input name="password" type="password"><br>
План: <select name="plan">
  @foreach(['trial','basic','standard','premium'] as $p)
    <option value="{{ $p }}" @selected($client->plan===$p)>{{ $p }}</option>
  @endforeach
</select><br>
Диалоги всего: <input name="dialog_limit" type="number" value="{{ $client->dialog_limit }}"><br>
Промтов максимум: <input name="prompts_limit" type="number" value="{{ $client->prompts_limit }}"><br>
Длина промта: <input name="prompt_max_length" type="number" value="{{ $client->prompt_max_length }}"><br>
Rate limit: <input name="rate_limit" type="number" value="{{ $client->rate_limit }}"><br>
Активен: <input type="checkbox" name="is_active" @checked($client->is_active)><br>
Домены: <input name="domains" value="{{ $domains }}"><br>
<button>Сохранить</button>
</form>

<form method="post" action="{{ route('admin.clients.destroy',$client) }}" onsubmit="return confirm('Удалить клиента?')">
@csrf @method('DELETE')
<button>Удалить клиента</button>
</form>
