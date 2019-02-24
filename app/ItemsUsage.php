<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ItemsUsage extends Model
{
    //
    protected $fillable = [
        'id_item',
        'tragos_restantes',
    ];
    protected $table = 'items_usage';
}
