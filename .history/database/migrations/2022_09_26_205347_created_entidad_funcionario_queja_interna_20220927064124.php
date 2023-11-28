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
            $table->uuid('uuid')->primary()->unique();
            $table->boolean('se_identifica_investigado');
            $table->string('id_proceso_disciplinario')->constrained('proceso_disciplinario');
            $table->string('id_entidad_investigado')->constrained('entidad_investigado');
            $table->integer('id_tipo_funcionario')->constrained('mas_tipo_funcionario')->nullable();;
            $table->integer('id_tipo_documento')->constrained('mas_tipo_documento')->nullable();;
            $table->string('numero_documento')->nullable();
            $table->string('primer_nombre')->nullable();
            $table->string('segundo_nombre')->nullable();
            $table->string('primer_apellido')->nullable();
            $table->string('segundo_apellido')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('numero_contrato')->nullable();
            $table->integer('dependencia')->constrained('mas_dependencia_origen')->nullable();;
            $table->boolean("estado")->nullable();
            $table->string("observaciones")->nullable();
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
