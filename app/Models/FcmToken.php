<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FcmToken extends Model
{
    protected $connection = 'mysql';
    protected $table = 'fcm_tokens';
    public $timestamps = false; // "creado_en" is the only timestamp field in the table

    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'usuario_id', 'id');
    }
}
