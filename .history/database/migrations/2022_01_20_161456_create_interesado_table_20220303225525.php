<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInteresadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interesado', function (Blueprint $table) {
            //$table->id();
            $table->uuid('uuid')->primary();
            $table->integer("id_etapa");
            $table->integer("id_tipo_interesao");
            $table->integer("id_tipo_sujeto_procesal")->nullable();
            $table->string('id_proceso_disciplinario');
            $table->integer("tipo_documento")->nullable();
            $table->string("numero_documento", 20)->nullable();
            $table->string("primer_nombre", 256)->nullable();
            $table->string("segundo_nombre", 256)->nullable();
            $table->string("primer_apellido", 256)->nullable();
            $table->string("segundo_apellido", 256)->nullable();
            $table->integer("id_departamento")->nullable();
            $table->integer("id_ciudad")->nullable();
            $table->string("direccion", 1000)->nullable();
            $table->integer("id_localidad")->nullable();
            $table->string("email", 512);
            $table->string("telefono_celular", 40)->nullable();
            $table->string("telefono_fijo",40)->nullable();
            $table->integer("id_sexo")->nullable();
            $table->integer("id_genero")->nullable();
            $table->integer("id_orientacion_sexual")->nullable();
            $table->string("entidad",100)->nullable();
            $table->string("cargo",100)->nullable();
            $table->string("tarjeta_profesional",100)->nullable();
            $table->integer("id_dependencia")->nullable();
            $table->integer("id_tipo_entidad")->nullable();
            $table->string("nombre_entidad",256)->nullable();
            $table->integer("id_entidad")->nullable();
            $table->integer("id_funcionario");
            $table->boolean("estado")->default(0);
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
        Schema::dropIfExists('interesado');
    }
}
