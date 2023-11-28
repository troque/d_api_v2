<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAntecedenteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('antecedente', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->string('descripcion', 2000);
            $table->dateTime('fecha_registro');
            $table->integer('id_dependencia');
            $table->boolean('estado');
            $table->uuid('id_proceso_disciplinario');
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
        Schema::dropIfExists('antecedente');
    }
}
