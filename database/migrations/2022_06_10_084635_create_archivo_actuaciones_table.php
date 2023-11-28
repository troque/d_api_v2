<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArchivoActuacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('archivo_actuaciones', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->string('uuid_actuacion');
            $table->foreignId('id_tipo_archivo')->constrained('MAS_TIPO_ARCHIVO_ACTUACIONES');
            $table->string("documento_ruta");
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
        Schema::dropIfExists('archivo_actuaciones');
    }
}