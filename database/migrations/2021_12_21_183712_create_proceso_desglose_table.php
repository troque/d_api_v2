<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesoDesgloseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceso_desglose', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->string('id_tramite_usuario');
            $table->dateTime('fecha_ingreso');
            $table->string('numero_auto', 50);
            $table->string('auto_asociado', 50);
            $table->dateTime('fecha_auto_desglose');
            $table->integer('id_dependencia_origen');
            $table->string('observacion_mesa_trabajo');
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
        Schema::dropIfExists('proceso_desglose');
    }
}
