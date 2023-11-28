<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateValidarClasificacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('validar_clasificacion', function (Blueprint $table) {

            $table->uuid('uuid')->primary();
            $table->foreignId('id_clasificacion_radicado')->constrained('clasificacion_radicado');
            $table->foreignId('id_etapa')->constrained('mas_etapa');
            $table->boolean("estado");
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
        Schema::dropIfExists('validar_clasificacion');
    }
}
