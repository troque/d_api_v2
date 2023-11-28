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
            $table->string('numero_documento')->nullable();
            $table->string('primer_nombre')->nullable();
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('numero_contrato')->nullable();
            $table->integer('dependencia')->constrained('mas_dependencia_origen');
            $table->boolean("estado")->nullable();
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
        //
    }
}
