<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $guarded = [];

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    protected static function booted()
    {
        static::creating(function ($attendance) {
        $siswa = \App\Models\User::find($attendance->student_id); 
        
        if ($siswa && $siswa->class_id) {
            $attendance->class_id = $siswa->class_id;
        }
    });
    }

}
