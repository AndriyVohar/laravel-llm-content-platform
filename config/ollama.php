<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Ollama API Configuration
    |--------------------------------------------------------------------------
    |
    | Configure your Ollama instance settings here.
    |
    */

    'url' => env('OLLAMA_URL', 'http://localhost:11434'),

    'model' => env('OLLAMA_MODEL', 'gemma3:4b'),

    'timeout' => env('OLLAMA_TIMEOUT', 120),

    'max_tokens' => env('OLLAMA_MAX_TOKENS', 2048),
];

