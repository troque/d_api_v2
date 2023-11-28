<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdFuncionarioAsignadoInLogProcesoDisciplinario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_proceso_disciplinario', function (Blueprint $table) {
            $table->string('id_funcionario_asignado', 255)->nullable();
            $table->dropColumn('id_tipo_expediente');
            $table->dropColumn('id_tipo_sub_expediente');
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
