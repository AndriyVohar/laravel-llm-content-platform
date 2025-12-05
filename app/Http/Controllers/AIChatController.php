<?php

namespace App\Http\Controllers;

use App\Services\OllamaService;
use Illuminate\Http\Request;

class AIChatController extends Controller
{
    protected OllamaService $ollama;

    public function __construct(OllamaService $ollama)
    {
        $this->ollama = $ollama;
    }

    public function send(Request $request)
    {
        $request->validate([
            'prompt' => 'required|string|max:5000'
        ]);

        $prompt = $request->input('prompt');

        $result = $this->ollama->generate($prompt);

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'response' => $result['response']
            ]);
        } else {
            $errorMessage = 'Error: ' . ($result['error'] ?? 'Unknown error occurred');
            $errorMessage .= '. Please make sure Ollama is running on ' . config('ollama.url');

            return response()->json([
                'success' => false,
                'response' => $errorMessage
            ], 500);
        }
    }
}
