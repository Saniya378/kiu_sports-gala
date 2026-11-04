<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Player;
use App\Models\Team;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PlayerController extends Controller
{
    /**
     * Store a new player for the given team with optional photo.
     */
    public function store(Request $request, Team $team): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'reg_no' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('players', 'public');
        }

        $player = $team->players()->create($validated);

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', "Player '{$player->name}' added successfully.");
    }

    /**
     * Bulk import players from a CSV file.
     */
    public function importCsv(Request $request, Team $team): RedirectResponse
    {
        $request->validate([
            'csv_file' => ['required', 'file', 'mimes:csv,txt', 'max:2048'],
        ]);

        $file = $request->file('csv_file');
        $content = file_get_contents($file->getRealPath());
        $lines = preg_split('/\r\n|\r|\n/', trim($content));

        if (empty($lines)) {
            return redirect()
                ->route('admin.teams.edit', $team)
                ->withErrors(['csv_file' => 'Uploaded CSV file is empty.']);
        }

        $imported = 0;
        $skipped = 0;

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if ($line === '') {
                continue;
            }

            $row = str_getcsv($line);
            $name = trim($row[0] ?? '');
            $regNo = trim($row[1] ?? '');
            $contact = trim($row[2] ?? '');

            // Skip header line if present
            if ($index === 0 && (strtolower($name) === 'name' || strtolower($name) === 'player name')) {
                continue;
            }

            if (empty($name)) {
                continue;
            }

            // Avoid duplicate in same team
            $exists = $team->players()
                ->where(function ($q) use ($name, $regNo) {
                    $q->where('name', $name);
                    if (!empty($regNo)) {
                        $q->orWhere('reg_no', $regNo);
                    }
                })
                ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $team->players()->create([
                'name' => mb_substr($name, 0, 255),
                'reg_no' => !empty($regNo) ? mb_substr($regNo, 0, 100) : null,
                'contact' => !empty($contact) ? mb_substr($contact, 0, 100) : null,
            ]);

            $imported++;
        }

        $msg = "Roster Import Complete: {$imported} athletes imported successfully.";
        if ($skipped > 0) {
            $msg .= " ({$skipped} duplicate players skipped).";
        }

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', $msg);
    }

    /**
     * Download sample CSV template for team roster imports.
     */
    public function sampleCsv(): Response
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="kiu_team_players_template.csv"',
        ];

        $sample = "Player Name,Registration Number,Contact\n"
                . "Ali Ahmed,2023-KIU-CS-042,0300-1234567\n"
                . "Kamran Khan,2022-KIU-CS-015,0312-9876543\n"
                . "Bilal Tariq,2024-KIU-CS-088,0345-5551234\n";

        return response($sample, 200, $headers);
    }

    /**
     * Remove the specified player from the given team.
     */
    public function destroy(Team $team, Player $player): RedirectResponse
    {
        abort_unless($player->team_id === $team->id, 404);

        $name = $player->name;

        if ($player->photo && Storage::disk('public')->exists($player->photo)) {
            Storage::disk('public')->delete($player->photo);
        }

        $player->delete();

        return redirect()
            ->route('admin.teams.edit', $team)
            ->with('success', "Player '{$name}' removed from team.");
    }
}
