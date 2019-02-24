<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 64);
            $table->unsignedInteger('id_category');
            $table->foreign('id_category')->references('id')->on('categories');
            $table->boolean('is_recipe')->default(false);
            $table->integer('tragos_por')->unsigned()->nullable(true)->default(0);
            $table->float('precio_compra')->unsigned()->nullable(true);
            $table->float('precio_venta')->unsigned();
            $table->integer('stock')->unsigned()->nullable(true)->default(0);
            $table->integer('stock_alert')->unsigned()->nullable(true)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
