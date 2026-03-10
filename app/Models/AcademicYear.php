<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicYear extends Model
{
    protected $guarded = [];


    public function schoolClasses(): HasMany
    {
        return $this->hasMany(SchoolClass::class, 'academic_year_id');
    }


    protected static function booted()
    {
        static::saving(function ($model) {
            if ($model->is_active) {
                static::where('id', '!=', $model->id)->update(['is_active' => false]);
            }
        });
    }
}
