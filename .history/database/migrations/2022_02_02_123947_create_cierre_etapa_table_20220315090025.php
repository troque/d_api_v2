<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCierreEtapaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cierre_etapa', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->string('id_proceso_disciplinario');
            $table->integer('id_etapa');
            $table->integer('id_funcionario_asignado');
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
        Schema::dropIfExists('cierre_etapa');
    }
}
