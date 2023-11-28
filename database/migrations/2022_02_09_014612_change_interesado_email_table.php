<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeInteresadoEmailTable extends Migration
{
   
    public function up()
    {
        Schema::table('interesado', function (Blueprint $table) {
            $table->string("email")->nullable()->change();
        });
    }

    public function down()
    {
        Schema::dropIfExists('interesado');
    }
}
