<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::orderBy('created_at', 'desc')->get();

        return inertia('News/Index', [
            'news' => $news
        ]);
    }

    public function show(News $news)
    {
        return inertia('News/Show', [
            'news' => $news
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $news = News::create($validated);

        return response()->json([
            'success' => true,
            'news' => $news
        ]);
    }

    public function destroy(News $news)
    {
        $news->delete();

        return redirect()->route('news.index');
    }
}
