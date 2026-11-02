<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Player extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'team_id',
        'name',
        'reg_no',
        'contact',
    ];

    /**
     * Get the team that the player belongs to.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Get the player of the match awards won by this player.
     */
    public function playerOfTheMatchAwards(): HasMany
    {
        return $this->hasMany(Result::class, 'player_of_the_match_id');
    }
}
