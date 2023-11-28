<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNombreInMasTipoSujetoProcesal extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_tipo_sujeto_procesal', function (Blueprint $table) {
            $table->string('nombre')->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_tipo_sujeto_procesal', function (Blueprint $table) {
            //
        });
    }
}
