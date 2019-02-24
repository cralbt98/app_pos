<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    //
    protected $fillable = [
        'cliente',
        'id_mesa',
        'total',
        'estado',
        'saldo',
    ];
    protected $table = 'orders';
}
