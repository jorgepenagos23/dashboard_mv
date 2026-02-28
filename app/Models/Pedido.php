<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $connection = 'mysql';
    protected $table = 'pedidos';
    public $timestamps = false;

    protected $guarded = [];

    public function cliente()
    {
        // Assuming cliente_id maps to cliente.cliente as per the schema varchar(100) vs varchar(50) unique
        return $this->belongsTo(Cliente::class, 'cliente_id', 'cliente');
    }

    public function detalles()
    {
        return $this->hasMany(PedidoProducto::class, 'pedido_id', 'id');
    }
}
