<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionResultadoPermitidoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacion_resultado_permitido', function (Blueprint $table) {
            $table->foreignId('resultado_evaluacion_id')->constrained('mas_resultado_evaluacion');
            $table->foreignId('fase_id')->constrained('mas_fase');
            $table->string("created_user")->nullable();
            $table->string("updated_user")->nullable();
            $table->string("deleted_user")->nullable();
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
        Schema::dropIfExists('evaluacion_resultado_permitido');
    }
}
