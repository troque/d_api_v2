<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdClasificacionRadicadoInLogProcesoDisciplinario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_proceso_disciplinario', function (Blueprint $table) {
            $table->string("id_clasificacion_radicado", false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('log_proceso_disciplinario', function (Blueprint $table) {
            //
        });
    }
}
