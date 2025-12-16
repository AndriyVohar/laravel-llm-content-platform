<?php

namespace App\Http\Controllers;

use App\Services\AIServiceClient;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    protected AIServiceClient $aiService;

    public function __construct(AIServiceClient $aiService)
    {
        $this->aiService = $aiService;
    }

    public function send(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:5000',
            'model' => 'nullable|string',
            'provider' => 'nullable|string'
        ]);

        $prompt = $request->input('prompt');
        $model = $request->input('model');
        $provider = $request->input('provider');

        $result = $this->aiService->chat($prompt, $provider, $model);

        if ($result['success'] ?? false) {
            return response()->json([
                'success' => true,
                'response' => $result['message']['content'] ?? ''
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
}
