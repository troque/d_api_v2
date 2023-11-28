<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesoDisciplinarioPorSemaforoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::create('proceso_disciplinario_por_semaforo', function (Blueprint $table) {
            $table->id();
            $table->foreignId("id_semaforo")->constrained('semaforo');
            $table->foreignUuid('id_proceso_disciplinario')->constrained('proceso_disciplinario','uuid');
            $table->foreignUuid('id_actuacion')->nullable()->constrained('actuaciones','uuid');
            $table->date("fecha_inicio");
            $table->boolean('estado');
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
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
        Schema::dropIfExists('proceso_disciplinario_por_semaforo');
    }
}
