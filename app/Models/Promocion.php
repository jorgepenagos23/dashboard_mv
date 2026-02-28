<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promocion extends Model
{
    protected $connection = 'mysql';
    protected $table = 'promociones';
    public $timestamps = false; // No created_at, updated_at in schema

    protected $guarded = [];
}
