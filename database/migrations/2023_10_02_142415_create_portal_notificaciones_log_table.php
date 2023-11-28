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
        Schema::create('portal_notificaciones_log', function (Blueprint $table) {
            $table->uuid()->primary()->comment('Identificador único de la tabla');
            $table->foreignUuid('id_notificacion')->constrained('portal_notificaciones','uuid')->comment('Identificador foreano de la tabla: PORTAL_NOTIFICACIONES');
            $table->foreignId('id_dependencia')->constrained('mas_dependencia_origen')->comment('Identificador foreano de la tabla: MAS_DEPENDENCIA_ORIGEN');
            $table->string("descripcion", 4000)->nullable()->comment('Descripción del log');
            $table->string("created_user", 256)->nullable()->comment('Usuario que realizo el registro');
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
        Schema::dropIfExists('portal_notificaciones_log');
    }
};
