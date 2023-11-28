<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIdUsuarioDependenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_dependencia_origen', function (Blueprint $table) {
            $table->integer("id_usuario_jefe")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mas_dependencia_origen', function ($table) {
            $table->dropColumn('id_usuario_jefe');
        });
    }
}
