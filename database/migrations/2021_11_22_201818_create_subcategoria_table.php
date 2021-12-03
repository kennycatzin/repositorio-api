<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubcategoriaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subcategoria', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_categoria');
            $table->string('descripcion');
            $table->integer('orden');
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
        Schema::dropIfExists('subcategoria');
    }
}
