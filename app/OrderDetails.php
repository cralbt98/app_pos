<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetails extends Model
{
    //
    protected $fillable = [
        'id_order',
        'id_item',
        'qty',
        'subtotal',
        'precio',
    ];
    protected $table = 'order_details';
}
