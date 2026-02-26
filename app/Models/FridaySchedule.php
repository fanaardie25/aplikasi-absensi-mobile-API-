<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FridaySchedule extends Model
{
    protected $guarded = [];

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(
            SchoolClass::class, 
            'schedule_classes', 
            'schedule_id',     
            'class_id' 
        )->withTimestamps();
    }
}
