<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AIChatController extends Controller
{
    public function send(Request $request)
    {
        $prompt = $request->input('prompt');

        // тут буде твоя LLM-логіка, поки що заглушка
        $response = "Тут AI згенерує відповідь на: " . $prompt;

        return back()->with(['response' => $response]);
    }
}
