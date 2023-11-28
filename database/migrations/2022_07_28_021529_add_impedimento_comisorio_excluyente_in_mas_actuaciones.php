<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImpedimentoComisorioExcluyenteInMasActuaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_actuaciones', function (Blueprint $table) {
            $table->integer('impedimento')->nullable()->after('TEXTO_REGRESAR_PROCESO_AL_ULTIMO_USUARIO');
            $table->integer('comisorio')->nullable()->after('IMPEDIMENTO');
            $table->integer('excluyente')->nullable()->after('COMISORIO');
            $table->string('DESPUES_APROBACION_LISTAR_ACTUACION')->nullable()->change();
            $table->foreignId('ID_ETAPA')->nullable()->change();
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