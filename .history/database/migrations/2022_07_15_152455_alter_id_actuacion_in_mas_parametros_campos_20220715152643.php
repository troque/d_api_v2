<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterIdActuacionInMasParametrosCampos extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_parametros_campos', function (Blueprint $table) {
            $table->foreignId('id_actuacion')->constrained('mas_actuaciones')->after('estado')->chage();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_parametros_campos', function (Blueprint $table) {
            //
        });
    }
}
