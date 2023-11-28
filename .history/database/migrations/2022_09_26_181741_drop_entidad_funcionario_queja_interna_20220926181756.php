<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEntidadFuncionarioQuejaInterna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entidad_funcionario_queja_interna', function (Blueprint $table) {
            Schema::dropIfExists('entidad_funcionario_queja_interna');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entidad_funcionario_queja_interna', function (Blueprint $table) {
            //
        });
    }
}
