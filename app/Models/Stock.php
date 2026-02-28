<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $connection = 'mysql';
    protected $table = 'stock';
    public $timestamps = false;

    protected $guarded = [];

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'product_id', 'id');
    }
}
