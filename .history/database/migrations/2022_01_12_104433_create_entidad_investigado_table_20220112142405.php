<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntidadInvestigadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entidad_investigado', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->string('id_proceso_disciplinario');
            $table->integer('id_etapa');
            $table->string('nombre_entidad')->nullable();
            $table->string('nombre_investigado')->nullable();
            $table->string('cargo')->nullable();
            $table->string('codigo')->nullable();
            $table->string('observaciones')->nullable();
            $table->boolean('estado')->nullable();
            $table->boolean('requiere_registro')->nullable();
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
        Schema::dropIfExists('entidad_investigado');
    }
}
