<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Agenda extends Model
{
    protected $fillable = [
        'name',
        'start_absensi',
        'end_absensi',
        'teacher_id',
        'category',
        'is_active',
        'target_gender',
        'target_religion',
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }
}
