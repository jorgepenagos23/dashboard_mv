<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobVacancy extends Model
{
    protected $connection = 'sqlite_vacancies';

    protected $fillable = [
        'title',
        'description',
        'requirements',
        'location',
        'is_active',
        'image_path',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
