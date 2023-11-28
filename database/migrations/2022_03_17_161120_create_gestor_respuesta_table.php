<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGestorRespuestaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gestor_respuesta', function (Blueprint $table) {
            $table->uuid('uuid')->unique()->primary();
            $table->uuid('id_proceso_disciplinario');
            $table->boolean('aprobado');
            $table->integer('version');
            $table->boolean('nuevo_documento');
            $table->string('descripcion', 256);
            $table->boolean('proceso_finalizado');
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gestor_respuesta');
    }
}
