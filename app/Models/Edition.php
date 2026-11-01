<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edition extends Model
{
    protected $fillable = [
        'title',
        'year',
        'start_date',
        'end_date',
        'status',
        'banner',
        'description',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'year' => 'integer',
        ];
    }

    /**
     * Get the public URL for the banner.
     */
    public function getBannerUrlAttribute(): ?string
    {
        return $this->banner ? asset('storage/' . $this->banner) : null;
    }

    /**
     * Get the teams participating in this edition.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
