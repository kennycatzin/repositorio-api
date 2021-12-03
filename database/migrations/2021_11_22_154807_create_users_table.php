<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_rol');
            $table->string('name');
            $table->string('email')->unique()->notNullable();
            $table->string('password');
            $table->string('tipo');
            $table->boolean('activo');
            $table->dateTime('fecha_creacion');
            $table->dateTime('fecha_modificacion');
            $table->integer('usuario_creacion');
            $table->integer('usuario_modificacion');
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
        Schema::dropIfExists('users');
    }
}
