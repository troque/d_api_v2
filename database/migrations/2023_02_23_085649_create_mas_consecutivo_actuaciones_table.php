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
        Schema::create('mas_consecutivo_actuaciones', function (Blueprint $table) {
            $table->id();
            $table->integer('id_vigencia');
            $table->integer('consecutivo');
            $table->integer('id_actuacion')->nullable();
            $table->boolean('estado')->default(true);
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
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
        Schema::dropIfExists('mas_consecutivo_actuaciones');
    }
};
