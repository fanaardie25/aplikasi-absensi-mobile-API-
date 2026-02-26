<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduleClass extends Model
{
    protected $table = 'schedule_classes';
    protected $guarded = [];

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'schedule_class_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
  
    public function rollingSchedule(): BelongsTo
    {
        return $this->belongsTo(ScheduleClass::class, 'schedule_class_id');
    }
}
