<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('proceso_poder_preferente', function (Blueprint $table) {
            $table->integer('id_etapa_asignada')->nullable()->constrained('mas_etapa')->after('dependencia_cargo');
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
};
