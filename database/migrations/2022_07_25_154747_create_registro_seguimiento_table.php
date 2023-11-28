<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRegistroSeguimientoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('registro_seguimiento', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid("id_proceso_disciplinario");
            $table->date('fecha_registro')->nullable();
            $table->string('descripcion', 4000);
            $table->uuid('id_documento_sirius')->nullable();
            $table->boolean('finalizado');
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
            $table->boolean('eliminado')->nullable();
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
        Schema::dropIfExists('registro_seguimiento');
    }
}
