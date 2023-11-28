<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionFaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacion_fase', function (Blueprint $table) {
            $table->bigIncrements('id')->primary();
            $table->foreignId('fase_id')->constrained('mas_fase');
            $table->foreignId('resultado_evaluacion')->constrained('mas_resultado_evaluacion');
            $table->boolean('rojo');
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
        Schema::dropIfExists('evaluacion_fase');
    }
}
