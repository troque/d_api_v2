<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedEntidadFuncionarioQuejaInterna extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('entidad_funcionario_queja_interna', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('id_entidad_investigado')->constrained('entidad_investigado');
            $table->integer('id_tipo_funcionario')->constrained('mas_tipo_funcionario');
            $table->integer('id_tipo_documento')->constrained('mas_tipo_documento');
            $table->string('numero_documento')
            $table->integer('primer_nombre');
            $table->integer('segundo_nombre');
            $table->integer('primer_apellido');
            $table->integer('segundo_apellido');
            $table->integer('razon_social');
            $table->integer('numero_contrato');
            $table->integer('dependencia')->constrained('mas_dependencia_origen');
            $table->boolean("estado")->nullable();
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
            $table->timestamps();
            $table->softDeletes();


            "data.attributes.id_entidad_investigado" => ["required"],
            "data.attributes.id_tipo_funcionario" => ["required"],
            "data.attributes.id_tipo_documento" => ["required"],
            "data.attributes.numero_documento" => ["required"],
            "data.attributes.primer_nombre" => ["required"],
            "data.attributes.segundo_nombre" => [""],
            "data.attributes.primer_apellido" => ["required"],
            "data.attributes.segundo_apellido" => [""],
            "data.attributes.razon_social" => [""],
            "data.attributes.numero_contrato" => [""],
            "data.attributes.dependencia" => [""],

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
}
