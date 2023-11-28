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
            $table->foreignId('id_fase')->constrained('mas_fase');
            $table->foreignId('id_resultado_evaluacion')->constrained('mas_resultado_evaluacion');
            $table->integer('orden')->nullable();
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
