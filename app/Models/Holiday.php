<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Holiday extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'date',
        'description',
    ];
    
    // Pastikan date otomatis menjadi instance Carbon agar formatnya konsisten
    protected $casts = [
        'date' => 'date',
    ];
}
