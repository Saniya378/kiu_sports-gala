<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\MatchVideo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class MatchVideoController extends Controller
{
    /**
     * Store a new match video highlight or short reel for a fixture.
     */
    public function store(Request $request, Fixture $fixture): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'video_url' => ['required', 'string', 'max:500'],
            'thumbnail_url' => ['nullable', 'url', 'max:500'],
            'duration' => ['nullable', 'string', 'max:30'],
            'type' => ['nullable', 'string', 'in:reel,highlight,ceremony'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]);

        MatchVideo::create([
            'fixture_id' => $fixture->id,
            'title' => $validated['title'],
            'video_url' => $validated['video_url'],
            'thumbnail_url' => $validated['thumbnail_url'] ?? null,
            'duration' => $validated['duration'] ?? '0:45',
            'type' => $validated['type'] ?? 'reel',
            'caption' => $validated['caption'] ?? null,
            'uploaded_by' => $request->user()?->id,
        ]);

        return back()->with('success', 'Match video highlight / reel added successfully.');
    }

    /**
     * Delete the specified match video clip.
     */
    public function destroy(MatchVideo $video): RedirectResponse
    {
        $video->delete();

        return back()->with('success', 'Match video clip deleted successfully.');
    }
}

