<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DeleteEstadoObservacionTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('antecedente', function (Blueprint $table) {
            $table->dropColumn('observacion_estado');
        });

        Schema::table('interesado', function (Blueprint $table) {
            $table->dropColumn('observacion_estado');
        });

        Schema::table('entidad_investigado', function (Blueprint $table) {
            $table->dropColumn('observaciones_estado');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
