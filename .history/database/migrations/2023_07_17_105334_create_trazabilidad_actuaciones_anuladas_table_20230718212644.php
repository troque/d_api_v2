<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trazabilidad_actuaciones_anuladas', function (Blueprint $table) {
            $table->uuid()->primary()->comment('Identificador único de la tabla');
            $table->string('uuid_trazabilidad_actuaciones', 2);
            $table->foreignUuid('uuid_actuacion')->constrained('actuaciones', 'uuid');
            $table->uuid('id_dependencia')->constrained('mas_dependencia_origen', 'id');
            $table->boolean('estado_anulacion_registro')->comment('Estado de la actuación anulada');
            $table->string("created_user", 256)->nullable()->comment('Usuario que creó el registro');
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
        Schema::dropIfExists('trazabilidad_actuaciones_anuladas');
    }
};
