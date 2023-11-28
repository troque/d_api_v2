<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempClasificacionRadicado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_clasificacion_radicado', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->integer("id_tipo_expediente");
            $table->integer("id_sub_tipo_expediente");
            $table->string("radicado")->nullable();
            $table->integer("vigencia")->nullable();
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
        Schema::dropIfExists('temp_clasificacion_radicado');
    }
}
