<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fixture;
use App\Models\MatchEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class MatchEventController extends Controller
{
    /**
     * Store a new match timeline event (Goal, Wicket, Card, Commentary Note).
     */
    public function store(Request $request, Fixture $fixture): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'team_id' => ['nullable', 'exists:teams,id'],
            'player_id' => ['nullable', 'exists:players,id'],
            'minute' => ['nullable', 'string', 'max:50'],
            'event_type' => ['required', 'string', 'in:goal,yellow_card,red_card,wicket,six,four,point,foul,substitute,note'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $event = $fixture->events()->create($validated);
        $event->load(['team', 'player']);

        // Invalidate live match caches immediately
        Cache::forget("fixture_live_{$fixture->id}");
        Cache::forget('live_scores_today_all');
        if ($fixture->sport_id) {
            Cache::forget("live_scores_today_{$fixture->sport_id}");
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Timeline event recorded successfully.',
                'event' => [
                    'id' => $event->id,
                    'minute' => $event->minute,
                    'icon' => $event->icon,
                    'event_type' => $event->event_type,
                    'title' => $event->title,
                    'description' => $event->description,
                    'team' => $event->team?->name,
                    'player' => $event->player?->name,
                ],
            ]);
        }

        return back()->with('success', "Event '{$event->title}' added to match timeline.");
    }

    /**
     * Delete a match timeline event.
     */
    public function destroy(Fixture $fixture, MatchEvent $event): RedirectResponse|JsonResponse
    {
        abort_unless($event->fixture_id === $fixture->id, 404);

        $event->delete();

        // Invalidate live match caches
        Cache::forget("fixture_live_{$fixture->id}");
        Cache::forget('live_scores_today_all');
        if ($fixture->sport_id) {
            Cache::forget("live_scores_today_{$fixture->sport_id}");
        }

        if (request()->expectsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Event removed from timeline.',
            ]);
        }

        return back()->with('success', 'Timeline event removed.');
    }
}

