<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdMasDependenciaOrigenInSemaforo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('semaforo', function (Blueprint $table) {
            $table->foreignId("id_mas_dependencia_inicia")->nullable()->constrained('mas_dependencia_origen')->after('id_mas_actuacion_inicia');
            $table->foreignId("id_mas_grupo_trabajo_inicia")->nullable()->constrained('mas_grupo_trabajo_secretaria_comun')->after('id_mas_actuacion_inicia');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
