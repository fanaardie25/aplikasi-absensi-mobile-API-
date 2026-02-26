<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}