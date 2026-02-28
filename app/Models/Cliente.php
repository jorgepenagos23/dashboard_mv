<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $connection = 'mysql';
    protected $table = 'cliente';
    public $timestamps = false;

    protected $guarded = [];

    public function direcciones()
    {
        return $this->hasMany(Direccion::class, 'CTECLI_CODIGO_K', 'cliente');
    }

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'cliente_id', 'cliente');
    }
}
