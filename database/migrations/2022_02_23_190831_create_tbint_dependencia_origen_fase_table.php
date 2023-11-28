<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTbintDependenciaOrigenFaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tbint_dependencia_origen_fase', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('id_dependencia_origen');
            $table->integer('id_fase');
            $table->boolean('estado');
            $table->dateTime("fecha_ingreso");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tbint_dependencia_origen_fase');
    }
}
