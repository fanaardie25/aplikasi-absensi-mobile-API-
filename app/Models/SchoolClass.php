<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class SchoolClass extends Model
{
    protected $fillable = [
        'name',
        'grade',
        'major',
        'sequence',
        'class_teacher',
        'academic_year',
        'is_active',
    ];

    public function schedules(): BelongsToMany
{
    return $this->belongsToMany(
        FridaySchedule::class, 
        'schedule_classes',
        'class_id', 
        'schedule_id',
    )->withTimestamps();
}
}