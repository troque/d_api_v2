<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvaluacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evaluacion', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('id_proceso_disciplinario');
            $table->boolean("noticia_priorizada");
            $table->string("justificacion", 4000);
            $table->boolean("estado");
            $table->foreignId('resultado_evaluacion')->constrained('MAS_RESULTADO_EVALUACION');
            $table->foreignId('tipo_conducta')->constrained('MAS_TIPO_CONDUCTA');
            $table->string("created_user")->nullable();
            $table->string("updated_user")->nullable();
            $table->string("deleted_user")->nullable();
            
            //SETTING THE PRIMARY KEYS
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
        Schema::dropIfExists('evaluacion');
    }
}
