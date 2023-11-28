<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogConsultasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_consultas', function (Blueprint $table) {
            $table->id()->primary();
            $table->integer('id_usuario');
            $table->string('id_proceso_disciplinario');
            $table->json('filtros');
            $table->integer('resultados_busqueda');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('log_consultas');
    }
}
