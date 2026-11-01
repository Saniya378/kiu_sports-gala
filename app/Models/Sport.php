<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sport extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'category',
        'rules',
    ];

    /**
     * Get a formatted display name including category.
     */
    public function getFullNameAttribute(): string
    {
        return "{$this->name} (" . ucfirst($this->category) . ")";
    }

    /**
     * Get the teams registered for this sport.
     */
    public function teams(): HasMany
    {
        return $this->hasMany(Team::class);
    }
}
