<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempProcesoDisciplinario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_proceso_disciplinario', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->string("radicado_original")->nullable();
            $table->string("vigencia_original")->nullable();
            $table->string("radicado")->nullable();
            $table->integer("vigencia")->nullable();
            $table->integer("estado")->nullable();
            $table->integer("id_tipo_proceso")->constrained('mas_tipo_proceso')->nullable();;
            $table->integer("id_dependencia_origen")->constrained('mas_dependencia_origen')->nullable();
            $table->integer("id_dependencia_duena")->constrained('mas_dependencia_origen')->nullable();
            $table->integer("id_etapa")->constrained('mas_etapa')->nullable();
            $table->integer("id_tipo_expediente");
            $table->integer("id_sub_tipo_expediente");
            $table->integer("id_tipo_evaluacion");
            $table->integer("id_tipo_conducta");
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
        Schema::dropIfExists('temp_proceso_disciplinario');
    }
}
