<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEliminadoPorReclasificacionInValidarClasificado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('validar_clasificado', function (Blueprint $table) {
            $table->integer('eliminado_por_reclacisificaicon')->nullable()->after('EXCLUYENTE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('validar_clasificado', function (Blueprint $table) {
            //
        });
    }
}
