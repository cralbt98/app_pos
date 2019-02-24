<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Items extends Model
{
    //
    protected $fillable = [
        'nombre',
        'id_category',
        'is_recipe',
        'tragos_por',
        'precio_compra',
        'precio_venta',
        'stock',
        'stock_alert',
    ];
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $atributes = [
        //set default values here

    ];
    public function actualizar_stock($newstock){
        $this->stock = $newstock;
        $this->save();
    }
}
