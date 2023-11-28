<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempEntidades extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_entidades', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->integer("id_entidad")->nullable();
            $table->string("sector")->nullable();
            $table->string("direccion")->nullable();
            $table->string("nombre_investigado")->nullable();
            $table->string("cargo_investigado")->nullable();
            $table->string("observaciones")->nullable();
            $table->string("radicado")->nullable();
            $table->integer("vigencia")->nullable();
            $table->integer("item")->nullable();
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
        Schema::dropIfExists('temp_entidades');
    }
}
