<!doctype html><meta charset="utf-8"><title>Вход клиента</title>
<h1>Вход клиента</h1>
@if($errors->any()) <div style="color:red">{{ $errors->first() }}</div> @endif
<form method="post" action="{{ route('client.login') }}">
  @csrf
  <input name="email" placeholder="email" required><br>
  <input name="password" type="password" placeholder="пароль" required><br>
  <button>Войти</button>
</form>
