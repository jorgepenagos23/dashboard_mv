<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoPromocion extends Model
{
    protected $connection = 'mysql';
    protected $table = 'productos_promociones';
    public $timestamps = false; // No timestamps in schema

    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'id_producto', 'id');
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'id_promocion', 'id');
    }
}
