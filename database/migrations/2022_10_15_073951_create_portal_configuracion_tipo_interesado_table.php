<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortalConfiguracionTipoInteresadoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portal_configuracion_tipo_interesado', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->integer("id_tipo_sujeto_procesal")->constrained('mas_tipo_sujeto_procesal')->nullable();
            $table->boolean("permiso_consulta")->nullable();
            $table->boolean("estado")->default(true);
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
        Schema::dropIfExists('portal_configuracion_tipo_interesado');
    }
}