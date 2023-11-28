<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCamposActuacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('actuaciones', function (Blueprint $table) {
            $table->string('uuid_proceso_disciplinario')->after('documento_ruta');
            $table->foreignId('id_etapa')->constrained('mas_etapa')->after('id_estado_actuacion');
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