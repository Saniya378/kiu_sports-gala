<?php

namespace App\Http\Controllers;

use App\Models\Fixture;
use App\Models\MatchFanVote;
use App\Models\MatchReaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FanEngagementController extends Controller
{
    /**
     * Submit a prediction vote for which department/team will win.
     */
    public function vote(Request $request, Fixture $fixture): JsonResponse
    {
        $validated = $request->validate([
            'team_id' => 'required|integer|in:' . $fixture->team1_id . ',' . $fixture->team2_id,
        ]);

        $sessionId = $request->session()->getId() ?? md5($request->ip() . $request->userAgent());

        MatchFanVote::updateOrCreate(
            [
                'fixture_id' => $fixture->id,
                'session_id' => $sessionId,
            ],
            [
                'team_id' => $validated['team_id'],
                'ip_address' => $request->ip(),
            ]
        );

        $selectedTeam = $validated['team_id'] == $fixture->team1_id ? $fixture->team1 : $fixture->team2;

        return response()->json([
            'success' => true,
            'message' => 'Vote recorded! Thank you for supporting ' . ($selectedTeam?->name ?? 'your team') . '.',
            'stats' => $fixture->fresh()->fan_vote_stats,
            'user_voted_team_id' => (int) $validated['team_id'],
        ]);
    }

    /**
     * Record a spectator live emoji reaction (fire, clap, cheer, trophy).
     */
    public function react(Request $request, Fixture $fixture): JsonResponse
    {
        $validated = $request->validate([
            'reaction' => 'required|string|in:fire,clap,cheer,trophy',
        ]);

        $reactionRecord = MatchReaction::firstOrCreate(
            [
                'fixture_id' => $fixture->id,
                'reaction' => $validated['reaction'],
            ],
            [
                'count' => 0,
            ]
        );

        $reactionRecord->increment('count');

        return response()->json([
            'success' => true,
            'reaction' => $validated['reaction'],
            'count' => (int) $reactionRecord->fresh()->count,
            'reactions' => $fixture->fresh()->reactions_stats,
        ]);
    }

    /**
     * Retrieve live fan poll stats and reactions counts for this fixture.
     */
    public function stats(Request $request, Fixture $fixture): JsonResponse
    {
        $sessionId = $request->session()->getId() ?? md5($request->ip() . $request->userAgent());
        $userVote = MatchFanVote::where('fixture_id', $fixture->id)->where('session_id', $sessionId)->first();

        return response()->json([
            'poll' => $fixture->fan_vote_stats,
            'reactions' => $fixture->reactions_stats,
            'user_voted_team_id' => $userVote ? (int) $userVote->team_id : null,
        ]);
    }
}

