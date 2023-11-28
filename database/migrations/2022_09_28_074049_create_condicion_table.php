<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCondicionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condicion', function (Blueprint $table) {
            $table->id();
            $table->integer("inicial");
            $table->integer("final");
            $table->string("color");
            $table->foreignId("id_semaforo")->constrained('semaforo');
            $table->boolean('estado');
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('condicion');
    }
}
