<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableInInteresadoTable extends Migration
{
   
    public function up()
    {
        Schema::table('interesado', function (Blueprint $table) {
            $table->integer("id_tipo_sujeto_procesal")->nullable()->change();
            $table->integer("tipo_documento")->nullable()->change();
            $table->string("numero_documento")->nullable()->change();
            $table->string("primer_nombre")->nullable()->change();
            $table->string("segundo_nombre")->nullable()->change();
            $table->string("primer_apellido")->nullable()->change();
            $table->string("segundo_apellido")->nullable()->change();
            $table->integer("id_departamento")->nullable()->change();
            $table->integer("id_ciudad")->nullable()->change();
            $table->string("direccion")->nullable()->change();
            $table->integer("id_localidad")->nullable()->change();
            $table->string("telefono_celular")->nullable()->change();
            $table->string("telefono_fijo")->nullable()->change();
            $table->integer("id_sexo")->nullable()->change();
            $table->integer("id_genero")->nullable()->change();
            $table->integer("id_orientacion_sexual")->nullable()->change();
            $table->string("entidad")->nullable()->change();
            $table->string("cargo")->nullable()->change();
            $table->string("tarjeta_profesional")->nullable()->change();
            $table->string("nombre_entidad")->nullable()->change();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interesado');
    }
}
