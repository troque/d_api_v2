<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropTableFaseEtapa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fase_etapa', function (Blueprint $table) {
            $table->table('fase_etapa');
        });

        Schema::table('actuaciones', function (Blueprint $table) {
            $table->dropColumn('observacion');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fase_etapa', function (Blueprint $table) {
            //
        });
    }
}
