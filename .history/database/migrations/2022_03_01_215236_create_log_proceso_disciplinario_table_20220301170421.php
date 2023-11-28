<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogProcesoDisciplinarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_proceso_disciplinario', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('id_proceso_disciplinario');
            $table->integer('id_etapa');
            $table->integer('id_fase')->nullable();
            $table->integer('id_tipo_log');
            $table->integer('id_estado');
            $table->string('descripcion');
            $table->integer('id_dependencia_origen');
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
        Schema::dropIfExists('log_proceso_disciplinario');
    }
}
