<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeleteUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('interesado', function (Blueprint $table) {
            $table->string('created_user')->nullable();;
            $table->string('updated_user')->nullable();;
            $table->string('deleted_user')->nullable();;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('interesado', function (Blueprint $table) {
            //
        });
    }
}
