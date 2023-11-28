<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdProcesoDisciplinarioInValidarClasificacion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('validar_clasificacion', function (Blueprint $table) {
            $table->string('id_proceso_disciplinario', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('validar_clasificacion', function (Blueprint $table) {
            //
        });
    }
}
