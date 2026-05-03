<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GrupoPrecioRegla extends Model
{
    protected $connection = 'mysql';
    protected $table = 'grupo_precio_reglas';
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'lineas'           => 'array',
        'sublineas_inc'    => 'array',
        'sublineas_exc'    => 'array',
        'skus_inc'         => 'array',
        'skus_exc'         => 'array',
        'sublineas_rates'  => 'array',
        'lineas_rates'     => 'array',
    ];
}
