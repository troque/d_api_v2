<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempInteresados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_interesados', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->integer("tipo_interesado")->nullable();
            $table->integer("tipo_sujeto_procesal")->nullable();
            $table->string("primer_nombre")->nullable();
            $table->string("segundo_nombre")->nullable();
            $table->string("primer_apellido")->nullable();
            $table->string("segundo_apellido")->nullable();
            $table->integer("tipo_documento")->nullable();
            $table->string("numero_documento")->nullable();
            $table->string("email")->nullable();
            $table->string("telefono")->nullable();
            $table->string("telefono2")->nullable();
            $table->string("cargo")->nullable();
            $table->integer("orientacion_sexual")->nullable();
            $table->integer("sexo")->nullable();
            $table->string("direccion")->nullable();
            $table->integer("departamento")->nullable();
            $table->integer("ciudad")->nullable();
            $table->integer("localidad")->nullable();
            $table->integer("entidad")->nullable();
            $table->integer("sector")->nullable();
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
        Schema::dropIfExists('temp_interesados');
    }
}
