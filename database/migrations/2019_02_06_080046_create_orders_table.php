<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->increments('id');
            $table->string('cliente', 64)->nullable(true)->default('Invitado');
            $table->unsignedInteger('id_mesa');
            $table->foreign('id_mesa')->references('id')->on('tables');
            $table->float('total')->unsigned(true);
            $table->boolean('estado')->default(true);
            $table->float('saldo')->default(true);
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
        Schema::dropIfExists('orders');
    }
}
