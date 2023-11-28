<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddValidacionJefeInClasificacionRadicado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clasificacion_radicado', function (Blueprint $table) {
            $table->boolean('validacion_jefe')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('clasificacion_radicado', function (Blueprint $table) {
            //
        });
    }
}
