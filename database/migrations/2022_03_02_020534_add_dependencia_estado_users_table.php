<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDependenciaEstadoUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('nombre', 255)->nullable();
            $table->string('apellido', 255)->nullable();
            $table->integer("id_dependencia")->nullable();
            $table->boolean("estado")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function ($table) {
            $table->dropColumn('nombre');
            $table->dropColumn('apellido');
            $table->dropColumn('id_dependencia');
            $table->dropColumn('estado');
        });
    }
}
