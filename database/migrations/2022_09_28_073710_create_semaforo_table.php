<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemaforoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semaforo', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->foreignId("id_mas_evento_inicio")->constrained('mas_evento_inicio');
            $table->foreignId("id_mas_actuacion_inicia")->nullable()->constrained('mas_actuaciones');
            $table->string("nombre_campo_fecha");
            $table->boolean('estado');
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
        Schema::dropIfExists('semaforo');
    }
}
