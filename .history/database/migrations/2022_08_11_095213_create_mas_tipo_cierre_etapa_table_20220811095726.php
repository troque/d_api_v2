<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasTipoCierreEtapaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_tipo_cierre_etapa', function (Blueprint $table) {
            $table->increments('id')->primary();
            $table->string('nombre');
            $table->boolean('estado');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mas_tipo_cierre_etapa');
    }
}
