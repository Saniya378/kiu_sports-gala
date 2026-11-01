<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Venue extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get the fixtures scheduled at this venue.
     */
    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class);
    }
}

