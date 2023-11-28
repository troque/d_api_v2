<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFuncionarioAsignadoInProcesoDisciplinario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proceso_disciplinario', function (Blueprint $table) {
            $table->integer("id_funcionario_asignado", false)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proceso_disciplinario', function (Blueprint $table) {
            //
        });
    }
}
