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
            $table->foreignId('id_subtipo_expediente')->constrained('mas_resultado_evaluacion');
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
