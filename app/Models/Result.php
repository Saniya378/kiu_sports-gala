<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fixture_id',
        'team1_score',
        'team2_score',
        'winner_team_id',
        'player_of_the_match_id',
        'summary',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'team1_score' => 'integer',
            'team2_score' => 'integer',
        ];
    }

    /**
     * Get the fixture associated with this result.
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * Get the winning team (if not a draw).
     */
    public function winnerTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'winner_team_id');
    }

    /**
     * Get the player of the match / MVP award.
     */
    public function playerOfTheMatch(): BelongsTo
    {
        return $this->belongsTo(Player::class, 'player_of_the_match_id');
    }
}

