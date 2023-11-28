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
        Schema::create('tbint_dependencia_actuacion', function (Blueprint $table) {
            $table->uuid();
            $table->foreignId('id_dependencia')->constrained('mas_dependencia_origen');
            $table->foreignId('id_dependencia_destino')->constrained('mas_dependencia_origen');
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
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
        Schema::dropIfExists('tbint_dependencia_actuacion');
    }
};
