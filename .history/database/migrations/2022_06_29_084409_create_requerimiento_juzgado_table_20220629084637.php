<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequerimientoJuzgadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requerimiento_juzgado', function (Blueprint $table) {

            $table->uuid('uuid')->primary();
            $table->string('descripcion');
            $table->string('id_proceso_disciplinario');
            $table->integer('id_etapa');
            $table->integer('id_dependencia_origen');
            $table->string('observacion_estado')->nullable();
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
        Schema::dropIfExists('requerimiento_juzgado');
    }
}
