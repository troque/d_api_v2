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
        Schema::create('tbint_roles_mas_actuaciones', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignId('id_mas_actuacion')->constrained('mas_actuaciones');
            $table->foreignId('id_rol')->constrained('roles');
            $table->string("created_user", 256)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles_mas_atbint_roles_mas_actuacionesctuaciones');
    }
};
