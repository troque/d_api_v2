<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DocumentoSiriusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documento_sirius', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->string('id_proceso_disciplinario');
            $table->integer('id_etapa');
            $table->integer('id_fase');
            $table->string('url_archivo');
            $table->string('nombre_archivo')->unique();
            $table->boolean('estado');
            $table->integer('num_folios');
            $table->string('num_radicado', 20);
            $table->string("extension")->nullable();
            $table->double("peso")->nullable();
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
        Schema::dropIfExists('documento_sirius');
    }
}
