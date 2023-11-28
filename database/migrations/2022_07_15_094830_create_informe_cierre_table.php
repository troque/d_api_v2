<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInformeCierreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('informe_cierre', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid("id_proceso_disciplinario");
            $table->string('radicado_sirius')->nullable();
            $table->string('documento_sirius')->nullable();
            $table->integer('id_etapa')->nullable();
            $table->integer('id_fase')->nullable();
            $table->string('descripcion', 4000);
            $table->uuid('id_documento_sirius')->nullable();
            $table->boolean('finalizado');
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
        Schema::dropIfExists('informe_cierre');
    }
}
