<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PedidoProducto extends Model
{
    protected $connection = 'mysql';
    protected $table = 'pedido_producto';
    public $timestamps = false;

    // This table has a composite primary key 'pedido_id', 'producto_id'
    protected $primaryKey = null;
    public $incrementing = false;

    protected $guarded = [];

    public function pedido()
    {
        return $this->belongsTo(Pedido::class, 'pedido_id', 'id');
    }

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }
}
