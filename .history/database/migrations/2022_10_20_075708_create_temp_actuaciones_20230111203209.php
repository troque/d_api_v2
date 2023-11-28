<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempActuaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_actuaciones', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->string("radicado");
            $table->integer("vigencia");
            $table->integer("item");
            $table->integer("nombre")->nullable();
            $table->integer("tipo")->nullable();
            $table->string("autoNumero");
            $table->date("fecha");
            $table->string("path");
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
        Schema::dropIfExists('temp_actuaciones');
    }
}
