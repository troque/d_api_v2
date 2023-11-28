<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNombreInMasLocalidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
            {
                Schema::table('mas_localidad', function (Blueprint $table) {
                    $table->string('nombre')->unique()->change();
                });
            }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_localidad', function (Blueprint $table) {
            //
        });
    }
}
