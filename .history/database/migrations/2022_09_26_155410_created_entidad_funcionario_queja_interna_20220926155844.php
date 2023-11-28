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
            $table->string('id_tipo_funcionario')->constrained('mas_tipo_funcionario');
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
