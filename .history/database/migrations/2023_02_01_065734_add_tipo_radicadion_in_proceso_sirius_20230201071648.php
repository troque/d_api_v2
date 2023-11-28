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
        Schema::table('proceso_sirius', function (Blueprint $table) {
            $table->string("tipo_radicadion", 2)->nullable();
            $table->string("vigencia_origen", 4)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('proceso_sirius', function (Blueprint $table) {
            //
        });
    }
};
