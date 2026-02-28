<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Direccion extends Model
{
    protected $connection = 'mysql';
    protected $table = 'direcciones';
    public $timestamps = false;

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'CTECLI_CODIGO_K', 'cliente');
    }
}
