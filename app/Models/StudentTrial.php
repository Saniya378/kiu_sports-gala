<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentTrial extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'edition_id',
        'department_id',
        'sport_id',
        'student_name',
        'reg_no',
        'gender',
        'contact',
        'experience_level',
        'playing_position',
        'additional_notes',
        'status',
        'coordinator_notes',
    ];

    /**
     * Get the edition this trial application belongs to.
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    /**
     * Get the department the student represents.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the sport discipline the student applied for.
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }
}

