<!doctype html><meta charset="utf-8"><title>Создать клиента</title>
<h1>Создать клиента</h1>
@if($errors->any())<div style="color:red">{{ $errors->first() }}</div>@endif
<form method="post" action="{{ route('admin.clients.store') }}">
@csrf
Имя: <input name="name" required><br>
Email: <input name="email" required><br>
Пароль: <input name="password" type="password" required><br>
План: <select name="plan">
  <option>trial</option><option>basic</option><option>standard</option><option>premium</option>
</select><br>
Диалоги всего: <input name="dialog_limit" type="number" value="200"><br>
Промтов максимум: <input name="prompts_limit" type="number" value="1"><br>
Длина промта: <input name="prompt_max_length" type="number" value="300"><br>
Rate limit: <input name="rate_limit" type="number" value="30"><br>
Активен: <input type="checkbox" name="is_active" checked><br>
Домены (через запятую): <input name="domains" placeholder="example.com,foo.bar"><br>
<button>Создать</button>
</form>
