<!doctype html><meta charset="utf-8"><title>Клиенты</title>
<h1>Клиенты</h1>
@if(session('success'))<div style="color:green">{{ session('success') }}</div>@endif
<p><a href="{{ route('admin.clients.create') }}">+ Создать клиента</a></p>
<form method="post" action="{{ route('admin.logout') }}">@csrf<button>Выйти</button></form>
<table border="1" cellpadding="6">
  <tr><th>ID</th><th>Имя</th><th>Email</th><th>План</th><th>Лимит</th><th>API токен</th><th></th></tr>
  @foreach($clients as $c)
  <tr>
    <td>{{ $c->id }}</td>
    <td>{{ $c->name }}</td>
    <td>{{ $c->email }}</td>
    <td>{{ $c->plan }}</td>
    <td>{{ $c->dialog_used }}/{{ $c->dialog_limit }}</td>
    <td><code>{{ $c->api_token }}</code></td>
    <td><a href="{{ route('admin.clients.edit',$c) }}">Ред.</a></td>
  </tr>
  @endforeach
</table>
{{ $clients->links() }}
