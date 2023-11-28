<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeObservacionesClasificacionRadicadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clasificacion_radicado', function (Blueprint $table) {
            $table->string('observaciones', 4000)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        /*Schema::table('clasificacion_radicado', function (Blueprint $table) {
            $table->string('observaciones')->change();
        });*/
    }
}
