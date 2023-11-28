<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdTipoExpedienteInMasTipoDerechoPeticion extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_tipo_derecho_peticion', function (Blueprint $table) {
            $table->unique('id_tipo_expediente', 'id_etapa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_tipo_derecho_peticion', function (Blueprint $table) {
            //
        });
    }
}
