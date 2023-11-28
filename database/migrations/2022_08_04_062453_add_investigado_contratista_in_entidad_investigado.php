<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvestigadoContratistaInEntidadInvestigado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entidad_investigado', function (Blueprint $table) {
            $table->boolean('investigado')->nullable();
            $table->boolean('contratista')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entidad_investigado', function (Blueprint $table) {
            //
        });
    }
}