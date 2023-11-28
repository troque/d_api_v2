<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionExpedientePermitidoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacion_expediente_permitido', function (Blueprint $table) {
  
             //FOREIGN KEY CONSTRAINTS
             $table->foreignId('tipo_expediente_id')->constrained('mas_tipo_expediente');
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
        Schema::dropIfExists('evaluacion_expediente_permitido');
    }
}
