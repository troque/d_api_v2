<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOrdenInMasEtapa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_etapa', function (Blueprint $table) {
            $table->integer('orden')->nullable()->after('estado');
            $table->string('link')->nullable()->after('orden');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_etapa', function (Blueprint $table) {
            //
        });
    }
}
