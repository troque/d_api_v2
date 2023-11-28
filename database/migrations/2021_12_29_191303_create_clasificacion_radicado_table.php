<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClasificacionRadicadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clasificacion_radicado', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->string('id_proceso_disciplinario');
            $table->integer('id_etapa');
            $table->integer('id_tipo_expediente');
            $table->string('observaciones')->nullable();
            $table->integer('id_tipo_queja')->nullable();
            $table->integer('id_termino_respuesta')->nullable();
            $table->date('fecha_termino')->nullable();
            $table->integer('hora_termino')->nullable();
            $table->boolean('gestion_juridica')->nullable();
            $table->boolean('estado')->nullable();
            $table->integer('id_estado_reparto')->nullable();
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
        Schema::dropIfExists('clasificacion_radicado');
    }


}
