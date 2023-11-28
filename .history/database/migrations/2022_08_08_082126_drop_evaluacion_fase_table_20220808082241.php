<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class DropEvaluacionFaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('evaluacion_fase', function (Blueprint $table) {
            Schema::dropIfExists('evaluacion_fase');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('evaluacion_fase', function (Blueprint $table) {
            Schema::dropIfExists('evaluacion_fase');
        });
    }
}
