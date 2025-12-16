<?php

namespace App\Http\Controllers;

use App\Models\Biography;
use Illuminate\Http\Request;

class BiographyController extends Controller
{
    public function index()
    {
        $biographies = Biography::orderBy('created_at', 'desc')->get();

        return inertia('Biographies/Index', [
            'biographies' => $biographies
        ]);
    }

    public function show(Biography $biography)
    {
        return inertia('Biographies/Show', [
            'biography' => $biography
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'birth_year' => 'nullable|integer|min:1|max:' . date('Y'),
            'death_year' => 'nullable|integer|min:1|max:' . date('Y'),
            'description' => 'required|string',
        ]);

        $biography = Biography::create($validated);

        return response()->json([
            'success' => true,
            'biography' => $biography
        ]);
    }

    public function destroy(Biography $biography)
    {
        $biography->delete();

        return redirect()->route('biographies.index');
    }
}
