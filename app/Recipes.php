<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Recipes extends Model
{
    //
    protected $fillable = [
        'id_item',
        'id_ingredient',
        'tragos'
    ];
    protected $table = 'recipes';
}
