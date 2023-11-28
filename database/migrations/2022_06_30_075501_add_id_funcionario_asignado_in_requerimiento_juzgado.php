<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdFuncionarioAsignadoInRequerimientoJuzgado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('requerimiento_juzgado', function (Blueprint $table) {
            $table->string('id_funcionario_asignado')->nullable()->after('descripcion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('requerimiento_juzgado', function (Blueprint $table) {
            //
        });
    }
}
