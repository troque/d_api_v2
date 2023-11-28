<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropUniqueIdProcesoDisciplinarioInCierreEtapa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cierre_etapa', function (Blueprint $table) {
            $table->string('id_proceso_disciplinario')->unique(false)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cierre_etapa', function (Blueprint $table) {
            //
        });
    }
}
