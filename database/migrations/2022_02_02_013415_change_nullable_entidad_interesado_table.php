<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeNullableEntidadInteresadoTable extends Migration
{
   
    public function up()
    {
        Schema::table('interesado', function (Blueprint $table) {

            $table->integer("ID_TIPO_ENTIDAD")->nullable()->change();
            
        });
    }

    public function down()
    {
        Schema::dropIfExists('interesado');
    }
}
