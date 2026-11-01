<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlayerController extends Controller
{
    /**
     * Store a new player for the given team.
     */
    public function store(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reg_no' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
        ]);

        $player = $team->players()->create($validated);

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', "Player '{$player->name}' added successfully.");
    }

    /**
     * Remove the specified player from the given team.
     */
    public function destroy(Team $team, Player $player): RedirectResponse
    {
        abort_unless($player->team_id === $team->id, 404);

        $name = $player->name;
        $player->delete();

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', "Player '{$name}' removed from team.");
    }
}
