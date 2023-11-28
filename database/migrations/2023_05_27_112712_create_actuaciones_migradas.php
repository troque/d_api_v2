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
        Schema::create('actuaciones_migradas', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->string("radicado");
            $table->integer("vigencia");
            $table->integer("item")->nullable();
            $table->string("nombre")->nullable();
            $table->integer("id_tipo_actuacion")->nullable();
            $table->integer("id_etapa")->nullable();
            $table->string("autoNumero")->nullable();
            $table->date("fecha")->nullable();
            $table->string("path")->nullable();
            $table->string("dependencia")->nullable();
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
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
        Schema::dropIfExists('actuaciones_migradas');
    }
};
