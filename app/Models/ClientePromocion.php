<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClientePromocion extends Model
{
    protected $connection = 'mysql';
    protected $table = 'clientes_promociones';
    public $timestamps = false;

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'id_cliente', 'id');
    }

    public function promocion()
    {
        return $this->belongsTo(Promocion::class, 'id_promocion', 'id');
    }
}
