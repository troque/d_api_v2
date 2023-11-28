<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipoInteresadosInPortalConfiguracionTipoInteresado extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('portal_configuracion_tipo_interesado', function (Blueprint $table) {
            $table->integer('id_tipo_interesado')->nullable()->after('permiso_consulta');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('portal_configuracion_tipo_interesado', function (Blueprint $table) {
            $table->integer('id_tipo_interesado')->nullable()->after('permiso_consulta');
        });
    }
}
