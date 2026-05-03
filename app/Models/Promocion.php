<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ClientePromocion;

class Promocion extends Model
{
    protected $connection = 'mysql';
    protected $table = 'promociones';
    public $timestamps = false; // No created_at, updated_at in schema

    protected $guarded = [];

    public function clientesAsignados()
    {
        return $this->hasMany(ClientePromocion::class, 'id_promocion', 'id');
    }
}
