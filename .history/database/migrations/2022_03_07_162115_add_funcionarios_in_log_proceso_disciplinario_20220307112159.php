<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFuncionariosInLogProcesoDisciplinario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('log_proceso_disciplinario', function (Blueprint $table) {
            $table->string('id_funcionario_actual', 255)->nullable();
            $table->string('id_funcionario_registra', 255)->nullable();
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
