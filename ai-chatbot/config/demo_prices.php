<?php

return [

    // КЛЮЧЕВЫЕ СЛОВА (substring‑матч)
    'patterns' => [
        'ru' => ['тариф', 'цена', 'цен', 'стоим', 'сколько стоит', 'прайс'],
        'en' => ['price', 'pricing', 'plan', 'plans', 'cost', 'how much', 'fee'],
        'az' => ['tarif', 'qiym', 'neçə', 'ödəniş', 'neçəyə'],
    ],

    // ГОТОВЫЕ ОТВЕТЫ
    'answers' => [
        'ru' => "Тарифы D.A.I.:\n\n• Бесплатный — 0 AZN/мес: 20 диалогов/мес, 1 промпт (до 1000 симв.), 1 запрос/мин.\n• Starter — 29 AZN/мес: 300 диалогов, 5 промптов (по 1500 симв.), 5 запросов/мин.\n• Growth — 69 AZN/мес: 1000 диалогов, 15 промптов (по 2500 симв.), 15 запросов/мин.\n• Pro — 149 AZN/мес: 3000 диалогов, 50 промптов (по 3000 симв.), 30 запросов/мин.\n\nВо всех платных — приоритетная поддержка и автоопределение языка.",
        'en' => "D.A.I. plans:\n\n• Free — 0 AZN/mo: 20 dialogs/mo, 1 prompt (up to 1k chars), 1 req/min.\n• Starter — 29 AZN/mo: 300 dialogs, 5 prompts (1.5k chars each), 5 req/min.\n• Growth — 69 AZN/mo: 1,000 dialogs, 15 prompts (2.5k chars each), 15 req/min.\n• Pro — 149 AZN/mo: 3,000 dialogs, 50 prompts (3k chars each), 30 req/min.\n\nAll paid plans include priority support and auto language detection.",
        'az' => "D.A.I. tarifləri:\n\n• Pulsuz — 0 AZN/ay: ayda 20 dialoq, 1 prompt (1k simvoladək), 1 sorğu/dəq.\n• Starter — 29 AZN/ay: 300 dialoq, 5 prompt (hər biri 1.5k), 5 sorğu/dəq.\n• Growth — 69 AZN/ay: 1000 dialoq, 15 prompt (hər biri 2.5k), 15 sorğu/dəq.\n• Pro — 149 AZN/ay: 3000 dialoq, 50 prompt (hər biri 3k), 30 sorğu/dəq.\n\nBütün ödənişli paketlərdə prioritet dəstək və auto‑dil aşkarlanması var.",
    ],
];
