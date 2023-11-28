<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdProcesoDisciplinarioInProcesoPoderPreferente extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proceso_poder_preferente', function (Blueprint $table) {
            $table->integer('id_proceso_disciplinario')->nullable()->after('id_etapa');
        });

        Schema::table('actuaciones', function (Blueprint $table) {
            $table->integer('id_dependencia')->nullable()->after('id_etapa');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proceso_poder_preferente', function (Blueprint $table) {
            //
        });
    }
}