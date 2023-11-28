<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropCierreEtapaConfiguracionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cierre_etapa_configuracion', function (Blueprint $table) {
            Schema::dropIfExists('cierre_etapa_configuracion');
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
            Schema::dropIfExists('cierre_etapa_configuracion');
        });
    }
}
