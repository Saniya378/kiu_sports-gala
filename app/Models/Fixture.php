<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Fixture extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'edition_id',
        'sport_id',
        'team1_id',
        'team2_id',
        'venue_id',
        'match_date',
        'match_time',
        'round',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'match_date' => 'date:Y-m-d',
        ];
    }

    /**
     * Get the edition that the fixture belongs to.
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    /**
     * Get the sport that the fixture is for.
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the first team in the fixture.
     */
    public function team1(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team1_id');
    }

    /**
     * Get the second team in the fixture.
     */
    public function team2(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'team2_id');
    }

    /**
     * Get the venue where the fixture takes place.
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Venue::class);
    }

    /**
     * Get the result associated with the fixture.
     */
    public function result(): HasOne
    {
        return $this->hasOne(Result::class);
    }

    /**
     * Get the match photos uploaded for this fixture.
     */
    public function photos(): HasMany
    {
        return $this->hasMany(MatchPhoto::class);
    }

    /**
     * Get the timeline events for this fixture.
     */
    public function events(): HasMany
    {
        return $this->hasMany(MatchEvent::class)->orderBy('id', 'asc');
    }
}

