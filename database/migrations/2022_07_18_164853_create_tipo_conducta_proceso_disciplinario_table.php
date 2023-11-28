<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTipoConductaProcesoDisciplinarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tipo_conducta_proceso_disciplinario', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('id_proceso_disciplinario')->constrained('proceso_disciplinario');
            $table->integer('id_tipo_conducta')->constrained('mas_tipo_conducta');
            $table->boolean('estado');
            $table->integer('id_etapa')->constrained('mas_etapa');
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
        Schema::dropIfExists('tipo_conducta_proceso_disciplinario');
    }
}
