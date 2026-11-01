<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Winner extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'edition_id',
        'sport_id',
        'winner_department_id',
        'runner_up_department_id',
        'final_score',
    ];

    /**
     * Get the edition associated with this winner record.
     */
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    /**
     * Get the sport discipline.
     */
    public function sport(): BelongsTo
    {
        return $this->belongsTo(Sport::class);
    }

    /**
     * Get the winning department.
     */
    public function winnerDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'winner_department_id');
    }

    /**
     * Get the runner-up department.
     */
    public function runnerUpDepartment(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'runner_up_department_id');
    }
}

