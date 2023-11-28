<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeObservacionMesaTrabajoInProcesoDesgloseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proceso_desglose', function (Blueprint $table) {
            $table->string("observacion_mesa_trabajo")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proceso_desglose', function (Blueprint $table) {
            //
        });
    }
}
