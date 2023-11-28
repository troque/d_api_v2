<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActuaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actuaciones', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->foreignId('id_actuacion')->constrained('MAS_ACTUACIONES');
            $table->string('usuario_accion');
            $table->foreignId('id_estado_actuacion')->constrained('MAS_ESTADO_ACTUACIONES');
            $table->string('observacion');
            $table->string("documento_ruta", 256)->nullable();
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
        Schema::dropIfExists('actuaciones');
    }
}