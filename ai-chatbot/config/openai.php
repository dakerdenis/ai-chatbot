<?php

return [
    // читаем из .env один раз на этапе кеша конфигурации
    'api_key' => env('OPENAI_API_KEY', ''),
    'model'   => env('OPENAI_MODEL', 'gpt-4o-mini'),
];
