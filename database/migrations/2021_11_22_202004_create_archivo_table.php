<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_subcategoria');
            $table->string('nombre');
            $table->string('descripcion');
            $table->string('resumen');
            $table->boolean('activo');
            $table->dateTime('fecha_creacion')->nullable();
            $table->dateTime('fecha_modificacion')->nullable();
            $table->integer('usuario_creacion')->nullable();
            $table->integer('usuario_modificacion')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('archivo');
    }
}
