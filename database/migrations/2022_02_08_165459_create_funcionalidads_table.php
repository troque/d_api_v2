<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuncionalidadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_funcionalidad', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('nombre_mostrar', 256);
            $table->integer("id_modulo");
            $table->timestamps();

            $table->foreign('id_modulo')->references('id')->on('mas_modulo')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mas_funcionalidad');
    }
}
