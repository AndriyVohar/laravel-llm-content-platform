<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use App\Models\ChatMessage;
use App\Models\News;
use App\Services\AIServiceClient;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    protected AIServiceClient $aiService;

    public function __construct(AIServiceClient $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index()
    {
        $messages = ChatMessage::orderBy('created_at', 'asc')->take(100)->get();

        return inertia('Chat/Index', [
            'messages' => $messages
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:5000',
            'mode' => 'required|in:general,biography,news',
            'model' => 'nullable|string',
            'provider' => 'nullable|string'
        ]);

        $prompt = $request->input('prompt');
        $mode = $request->input('mode');
        $model = $request->input('model');
        $provider = $request->input('provider');

        // Зберігаємо повідомлення користувача
        $userMessage = ChatMessage::create([
            'type' => 'user',
            'mode' => $mode,
            'content' => $prompt
        ]);

        // Обгортаємо промпт в залежності від режиму
        $wrappedPrompt = $this->wrapPrompt($prompt, $mode);

        // Відправляємо до AI
        $result = $this->aiService->chat($wrappedPrompt, $provider, $model);

        if ($result['success'] ?? false) {
            $aiResponse = $result['message']['content'] ?? '';

            // Зберігаємо відповідь AI
            $aiMessage = ChatMessage::create([
                'type' => 'ai',
                'mode' => $mode,
                'content' => $aiResponse
            ]);

            // Якщо режим створення біографії або новини, парсимо відповідь та зберігаємо
            if ($mode === 'biography') {
                $this->saveBiographyFromAI($aiResponse);
            } elseif ($mode === 'news') {
                $this->saveNewsFromAI($aiResponse);
            }

            return response()->json([
                'success' => true,
                'response' => $aiResponse,
                'message' => $aiMessage
            ]);
        } else {
            $errorMessage = 'Error: ' . ($result['error'] ?? 'Unknown error occurred');
            $errorMessage .= '. Please make sure AI Service is running on ' . config('services.ai_service.url');

            return response()->json([
                'success' => false,
                'response' => $errorMessage
            ], 500);
        }
    }

    public function clear()
    {
        ChatMessage::truncate();

        return response()->json(['success' => true]);
    }

    private function wrapPrompt(string $prompt, string $mode): string
    {
        switch ($mode) {
            case 'biography':
                return "Ти - асистент для створення біографій. Користувач попросить тебе створити біографію.
Поверни відповідь СТРОГО у наступному JSON форматі без додаткового тексту:
{
    \"full_name\": \"ПІБ особи\",
    \"birth_year\": рік народження (число або null),
    \"death_year\": рік смерті (число або null),
    \"description\": \"Детальна біографія у форматі Markdown з заголовками, списками тощо\"
}

Запит користувача: {$prompt}";

            case 'news':
                return "Ти - асистент для створення новин. Користувач попросить тебе створити новину.
Поверни відповідь СТРОГО у наступному JSON форматі без додаткового тексту:
{
    \"title\": \"Назва новини\",
    \"description\": \"Повний текст новини\"
}

Запит користувача: {$prompt}";

            case 'general':
            default:
                return $prompt;
        }
    }

    private function saveBiographyFromAI(string $aiResponse): ?Biography
    {
        try {
            // Спробуємо витягти JSON з відповіді
            $jsonStart = strpos($aiResponse, '{');
            $jsonEnd = strrpos($aiResponse, '}');

            if ($jsonStart === false || $jsonEnd === false) {
                return null;
            }

            $jsonString = substr($aiResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
            $data = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            if (isset($data['full_name']) && isset($data['description'])) {
                return Biography::create([
                    'full_name' => $data['full_name'],
                    'birth_year' => $data['birth_year'] ?? null,
                    'death_year' => $data['death_year'] ?? null,
                    'description' => $data['description'],
                ]);
            }
        } catch (\Exception $e) {
            // Логуємо помилку, але не зупиняємо виконання
            \Log::error('Failed to save biography from AI: ' . $e->getMessage());
        }

        return null;
    }

    private function saveNewsFromAI(string $aiResponse): ?News
    {
        try {
            // Спробуємо витягти JSON з відповіді
            $jsonStart = strpos($aiResponse, '{');
            $jsonEnd = strrpos($aiResponse, '}');

            if ($jsonStart === false || $jsonEnd === false) {
                return null;
            }

            $jsonString = substr($aiResponse, $jsonStart, $jsonEnd - $jsonStart + 1);
            $data = json_decode($jsonString, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                return null;
            }

            if (isset($data['title']) && isset($data['description'])) {
                return News::create([
                    'title' => $data['title'],
                    'description' => $data['description'],
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Failed to save news from AI: ' . $e->getMessage());
        }

        return null;
    }
}
