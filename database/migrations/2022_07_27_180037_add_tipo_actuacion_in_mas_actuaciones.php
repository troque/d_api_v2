<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoActuacionInMasActuaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_actuaciones', function (Blueprint $table) {
            $table->integer('tipo_actuacion')->after('texto_regresar_proceso_al_ultimo_usuario');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_actuaciones', function (Blueprint $table) {
            //
        });
    }
}
