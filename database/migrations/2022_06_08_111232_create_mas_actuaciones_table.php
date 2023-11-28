<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasActuacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_actuaciones', function (Blueprint $table) {
            $table->id()->primary();
            $table->string("nombre_actuacion");
            $table->string('nombre_plantilla');
            $table->string('id_etapa')->nullable();
            $table->boolean('estado');
            $table->string('id_etapa_despues_aprobacion')->nullable();
            $table->boolean('despues_aprobacion_listar_actuacion');
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
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
        Schema::dropIfExists('mas_actuaciones');
    }
}