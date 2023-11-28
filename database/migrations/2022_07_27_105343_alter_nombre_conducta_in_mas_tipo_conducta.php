<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterNombreConductaInMasTipoConducta extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_tipo_conducta', function (Blueprint $table) {
           $table->renameColumn('conducta_nombre', 'nombre');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_tipo_conducta', function (Blueprint $table) {
            //
        });
    }
}
