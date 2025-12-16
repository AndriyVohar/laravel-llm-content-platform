<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class AIServiceClient
{
    private string $baseUrl;
    private int $timeout;
    private string $model;

    public function __construct()
    {
        $this->baseUrl = config('services.ai_service.url', 'http://localhost:8000');
        $this->timeout = config('services.ai_service.timeout', 30);
        $this->model = config('services.ai_service.model', 'gemma3:4b');
    }

    /**
     * Простий чат без інструментів
     */
    public function chat(string $message, ?string $provider = null, ?string $model = null): array
    {
        return $this->chatWithMessages(
            [['role' => 'user', 'content' => $message]],
            $provider,
            [],
            null,
            0.7,
            $model
        );
    }

    /**
     * Чат з повною контролем над повідомленнями
     */
    public function chatWithMessages(
        array $messages,
        ?string $provider = null,
        array $tools = [],
        ?int $maxTokens = null,
        float $temperature = 0.7,
        ?string $model = null
    ): array {
        $payload = [
            'messages' => $messages,
            'provider' => $provider ?? config('services.ai_service.provider', 'deepinfra'),
            'model' => $model ?? $this->model,
            'temperature' => $temperature,
        ];

        if (!empty($tools)) {
            $payload['tools'] = $tools;
        }

        if ($maxTokens !== null) {
            $payload['max_tokens'] = $maxTokens;
        }

        try {
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/api/chat", $payload);

            return $response->json();
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Отримати список доступних інструментів
     */
    public function getAvailableTools(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/tools");

            return $response->json();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Отримати список доступних провайдерів
     */
    public function getAvailableProviders(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/providers");

            return $response->json();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Перевірити здоров'я сервісу
     */
    public function healthCheck(): array
    {
        try {
            $response = Http::timeout($this->timeout)
                ->get("{$this->baseUrl}/api/health");

            return $response->json();
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}

