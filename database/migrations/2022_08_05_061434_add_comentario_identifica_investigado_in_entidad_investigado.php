<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComentarioIdentificaInvestigadoInEntidadInvestigado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('entidad_investigado', function (Blueprint $table) {
            $table->boolean('planta')->nullable();
            $table->string('comentario_identifica_investigado', 4000)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('entidad_investigado', function (Blueprint $table) {
            //
        });
    }
}