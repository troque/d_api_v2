<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMasOrdenFuncionarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_orden_funcionario', function (Blueprint $table) {
            $table->integer("id_expediente")->nullable();
            $table->integer("id_sub_expediente")->nullable();
            $table->integer("id_tercer_expediente")->nullable();
            $table->boolean("unico_rol")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
