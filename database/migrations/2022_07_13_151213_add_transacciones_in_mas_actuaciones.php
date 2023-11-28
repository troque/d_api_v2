<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransaccionesInMasActuaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_actuaciones', function (Blueprint $table) {
            $table->string('texto_dejar_en_mis_pendientes')->nullable()->after('nombre_plantilla_manual');
            $table->string('texto_enviar_a_alguien_de_mi_dependencia')->nullable()->after('nombre_plantilla_manual');
            $table->string('texto_enviar_a_jefe_de_la_dependencia')->nullable()->after('nombre_plantilla_manual');
            $table->string('texto_enviar_a_otra_dependencia')->nullable()->after('nombre_plantilla_manual');
            $table->string('texto_regresar_proceso_al_ultimo_usuario')->nullable()->after('nombre_plantilla_manual');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_actuaciones', function (Blueprint $table) {
            //
        });
    }
}
