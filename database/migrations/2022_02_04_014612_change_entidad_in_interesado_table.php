<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeEntidadInInteresadoTable extends Migration
{
   
    public function up()
    {
        Schema::table('interesado', function (Blueprint $table) {
            $table->integer("id_entidad")->nullable()->change();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interesado');
    }
}
