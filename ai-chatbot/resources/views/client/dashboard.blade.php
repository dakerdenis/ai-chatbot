<!doctype html><meta charset="utf-8"><title>Кабинет клиента</title>
<h1>Клиент: {{ $client->name }}</h1>
<p>Тариф: {{ $client->plan }} | Диалоги: {{ $client->dialog_used }}/{{ $client->dialog_limit }}</p>
<form method="post" action="{{ route('client.logout') }}">@csrf<button>Выйти</button></form>
<p><a href="{{ route('client.prompts.index') }}">Промты</a></p>
