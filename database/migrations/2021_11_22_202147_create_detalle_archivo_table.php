<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetalleArchivoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalle_archivo', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_archivo');
            $table->integer('id_tipo');
            $table->string('url');
            $table->string('observaciones');
            $table->integer('consecutivo');
            $table->boolean('actual');
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
        Schema::dropIfExists('detalle_archivo');
    }
}
