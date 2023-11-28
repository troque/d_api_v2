<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPrefijoInMasDependenciaOrigen extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_dependencia_origen', function (Blueprint $table) {
            $table->string("prefijo", 256)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_dependencia_origen', function (Blueprint $table) {
            $table->dropColumn('prefijo');
        });
    }
}