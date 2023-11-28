<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_ciudad', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('mas_departamento', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('MAS_DEPENDENCIA_ORIGEN', function (Blueprint $table) {
            $table->boolean("estado")->index()->nullable();
        });

        Schema::table('MAS_ESTADO_REPARTO', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('MAS_FUNCIONALIDAD', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('MAS_MODULO', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('MAS_PARAMETRO', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });


        Schema::table('MAS_TERMINO_RESPUESTA', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('MAS_TIPO_DERECHO_PETICION', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('MAS_TIPO_EXPEDIENTE', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });

        Schema::table('MAS_TIPO_QUEJA', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });


        Schema::table('MAS_TIPO_RESPUESTA', function (Blueprint $table) {
            $table->boolean("estado")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //Schema::dropIfExists('entidad_investigado');
    }
}
