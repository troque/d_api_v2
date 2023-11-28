<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_parametro_campos', function (Blueprint $table) {
            $table->string('referencia_tabla')->nullable();
            $table->string('referencia_columna')->nullable();
            $table->boolean('referencia_ultimo_dato')->nullable();
            $table->boolean('principal')->nullable();
            $table->string('referencia_tabla_maestra')->nullable();
            $table->string('referencia_columna_maestra')->nullable();
            $table->string('referencia_columna_maestra_consulta')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
