<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MatchEvent extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'fixture_id',
        'team_id',
        'player_id',
        'minute',
        'event_type',
        'title',
        'description',
    ];

    /**
     * Get the fixture for this event.
     */
    public function fixture(): BelongsTo
    {
        return $this->belongsTo(Fixture::class);
    }

    /**
     * Get the team associated with this event.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the player associated with this event.
     */
    public function player(): BelongsTo
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * Get an emoji icon for this event type.
     */
    public function getIconAttribute(): string
    {
        return match ($this->event_type) {
            'goal' => '⚽',
            'yellow_card' => '🟨',
            'red_card' => '🟥',
            'wicket' => '🏏',
            'six' => '🚀',
            'four' => '⚡',
            'point' => '🎯',
            'foul' => '⚠️',
            'substitute' => '🔄',
            default => '📢',
        };
    }
}

