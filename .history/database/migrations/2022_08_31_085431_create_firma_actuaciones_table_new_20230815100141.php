<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFirmaActuacionesTableNew extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firma_actuaciones', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid("id_actuacion")->constrained('actuaciones');
            $table->foreignId("id_user")->constrained('users');
            $table->integer('tipo_firma');
            $table->integer('estado');
            $table->boolean('eliminado')->nullable();
            $table->uuid("uuid_proceso_disciplinario")->constrained('proceso_disciplinario');
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
        Schema::dropIfExists('firma_actuaciones');
    }
}
