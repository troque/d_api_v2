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
            $table->integer("id_tipo_sujeto_procesal");
            $table->string('id_proceso_disciplinario');
            $table->integer("tipo_documento");
            $table->string("numero_documento", 20);
            $table->string("primer_nombre", 256);
            $table->string("segundo_nombre", 256);
            $table->string("primer_apellido", 256);
            $table->string("segundo_apellido", 256);
            $table->integer("id_departamento");
            $table->integer("id_ciudad");
            $table->string("direccion", 1000);
            $table->integer("id_localidad");
            $table->string("email", 512);
            $table->string("telefono_celular", 40);
            $table->string("telefono_fijo",40);
            $table->integer("id_sexo");
            $table->integer("id_genero");
            $table->integer("id_orientacion_sexual");
            $table->string("entidad",100);
            $table->string("cargo",100);
            $table->string("tarjeta_profesional",100);
            $table->integer("id_dependencia");
            $table->integer("id_tipo_entidad");
            $table->string("nombre_entidad",256);
            $table->integer("id_entidad");
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
