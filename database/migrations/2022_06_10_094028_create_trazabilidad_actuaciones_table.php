<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTrazabilidadActuacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trazabilidad_actuaciones', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('uuid_actuacion');
            $table->foreignId('id_estado_actuacion')->constrained('MAS_ESTADO_ACTUACIONES');
            $table->string('observacion');
            $table->boolean('estado');
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
        Schema::dropIfExists('trazabilidad_actuaciones');
    }
}
