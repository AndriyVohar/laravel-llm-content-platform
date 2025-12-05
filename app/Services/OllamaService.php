<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OllamaService
{
    protected string $baseUrl;
    protected string $model;
    protected int $timeout;

    public function __construct()
    {
        $this->baseUrl = config('ollama.url', 'http://localhost:11434');
        $this->model = config('ollama.model', 'gemma3:4b');
        $this->timeout = config('ollama.timeout', 120);
    }

    /**
     * Generate a response from Ollama
     */
    public function generate(string $prompt, ?string $model = null): array
    {
        $model = $model ?? $this->model;

        try {
            $response = Http::timeout($this->timeout)
                ->post($this->baseUrl . '/api/generate', [
                    'model' => $model,
                    'prompt' => $prompt,
                    'stream' => false,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                return [
                    'success' => true,
                    'response' => $data['response'] ?? '',
                    'model' => $data['model'] ?? $model,
                    'created_at' => $data['created_at'] ?? now()->toISOString(),
                    'total_duration' => $data['total_duration'] ?? 0,
                ];
            }

            return [
                'success' => false,
                'error' => 'Failed to get response from Ollama. Status: ' . $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Ollama API Error', [
                'message' => $e->getMessage(),
                'model' => $model,
                'prompt_length' => strlen($prompt),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check if Ollama is available
     */
    public function checkHealth(): bool
    {
        try {
            $response = Http::timeout(5)->get($this->baseUrl);
            return $response->successful();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * List available models
     */
    public function listModels(): array
    {
        try {
            $response = Http::timeout(10)->get($this->baseUrl . '/api/tags');

            if ($response->successful()) {
                return $response->json()['models'] ?? [];
            }

            return [];
        } catch (\Exception $e) {
            Log::error('Failed to list Ollama models: ' . $e->getMessage());
            return [];
        }
    }
}

