<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
