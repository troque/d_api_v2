<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasTipoUnidadTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_tipo_unidad', function (Blueprint $table) {
            $table->id()->primary();
            $table->string("nombre");
            $table->integer("codigo_unidad")->nullable();;
            $table->string("descripcion_unidad")->nullable();
            $table->id("id_dependencia")->constrained('mas_dependencia_origen');
            $table->boolean("estado");
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mas_tipo_unidad');
    }
}