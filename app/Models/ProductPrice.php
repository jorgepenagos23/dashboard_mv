<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductPrice extends Model
{
    protected $connection = 'mysql';
    protected $table = 'product_prices';
    public $timestamps = false;

    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'product_id', 'id');
    }
}
