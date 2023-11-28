<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeAntecedenteDependenciaTable extends Migration
{
   
    public function up()
    {
        Schema::table('antecedente', function (Blueprint $table) {
            $table->integer("id_dependencia")->nullable()->change();
        });
    }

    public function down()
    {
        Schema::dropIfExists('antecedente');
    }
}
