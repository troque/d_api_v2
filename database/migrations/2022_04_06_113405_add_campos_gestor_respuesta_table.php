<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposGestorRespuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gestor_respuesta', function (Blueprint $table) {
            $table->integer("orden_funcionario");
            $table->integer('id_mas_orden_funcionario');
            $table->uuid('id_documento_sirius');
            $table->string("descripcion", 4000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
