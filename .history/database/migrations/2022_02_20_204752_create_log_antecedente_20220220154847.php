<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogAntecedente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_antecedente', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->string('id_proceso_disciplinario');
            $table->string('id_antecedente');
            $table->string('descripcion', 2000);
            $table->dateTime('fecha_registro');
            $table->integer('id_dependencia');
            $table->boolean('estado');

            $table->integer('id_etapa');
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
        Schema::dropIfExists('log_antecedente');
    }
}
