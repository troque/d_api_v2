<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCierreEtapaConfiguracionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cierre_etapa_configuracion', function (Blueprint $table) {
            $table->increments('id')->primary();
            $table->foreignId('id_tipo_proceso_disciplinario')->constrained('mas_fase');
            $table->integer('id_tipo_expediente')->constrained('mas_fase');
            $table->integer('id_tipo_expediente')->constrained('mas_tipo_expediente');
            $table->integer('id_tipo_cierre_etapa')->constrained('mas_tipo_cierre_etapa');
            $table->integer('id_subtipo_expediente');
            $table->integer('orden');
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
        Schema::dropIfExists('cierre_etapa_configuracion');
    }
}
