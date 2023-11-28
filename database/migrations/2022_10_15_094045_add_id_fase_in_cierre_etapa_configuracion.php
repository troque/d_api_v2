<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdFaseInCierreEtapaConfiguracion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cierre_etapa_configuracion', function (Blueprint $table) {
            $table->integer('id_fase')->nullable()->after('id_etapa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cierre_etapa_configuracion', function (Blueprint $table) {
            //
        });
    }
}
