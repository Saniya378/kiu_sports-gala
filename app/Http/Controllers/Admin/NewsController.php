<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\News;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class NewsController extends Controller
{
    /**
     * Display a listing of the news articles.
     */
    public function index(Request $request): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();

        $query = News::with('edition')->latest('published_at')->latest('id');

        if ($request->filled('edition_id')) {
            $query->where('edition_id', $request->edition_id);
        }

        $newsList = $query->paginate(15)->withQueryString();

        return view('admin.news.index', compact('newsList', 'editions'));
    }

    /**
     * Show the form for creating a new news article.
     */
    public function create(): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();

        return view('admin.news.create', compact('editions'));
    }

    /**
     * Store a newly created news article in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'], // Max 5MB
            'edition_id' => ['nullable', 'exists:editions,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        News::create($validated);

        return redirect()->route('admin.news.index')->with('success', 'News article published successfully.');
    }

    /**
     * Show the form for editing the specified news article.
     */
    public function edit(News $news): View
    {
        $editions = Edition::orderBy('year', 'desc')->get();

        return view('admin.news.edit', compact('news', 'editions'));
    }

    /**
     * Update the specified news article in storage.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,gif', 'max:5120'],
            'edition_id' => ['nullable', 'exists:editions,id'],
            'published_at' => ['nullable', 'date'],
        ]);

        if ($request->hasFile('image')) {
            if ($news->image && Storage::disk('public')->exists($news->image)) {
                Storage::disk('public')->delete($news->image);
            }
            $validated['image'] = $request->file('image')->store('news', 'public');
        }

        if (empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $news->update($validated);

        return redirect()->route('admin.news.index')->with('success', 'News article updated successfully.');
    }

    /**
     * Remove the specified news article from storage.
     */
    public function destroy(News $news): RedirectResponse
    {
        if ($news->image && Storage::disk('public')->exists($news->image)) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'News article deleted successfully.');
    }
}

