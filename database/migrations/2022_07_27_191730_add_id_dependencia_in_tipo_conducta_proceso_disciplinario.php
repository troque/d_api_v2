<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdDependenciaInTipoConductaProcesoDisciplinario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tipo_conducta_proceso_disciplinario', function (Blueprint $table) {
            $table->integer('id_dependencia')->nullable()->after('id_etapa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tipo_conducta_proceso_disciplinario', function (Blueprint $table) {
            //
        });
    }
}
