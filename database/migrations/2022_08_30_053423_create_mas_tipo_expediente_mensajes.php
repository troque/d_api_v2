<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasTipoExpedienteMensajes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_tipo_expediente_mensajes', function (Blueprint $table) {
            $table->id()->primary();
            $table->string("mensaje")->nullable();
            $table->id("id_tipo_expediente")->constrained('mas_tipo_expediente');
            $table->id("id_sub_tipo_expediente")->nullable();
            $table->boolean("estado");
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
        Schema::dropIfExists('mas_tipo_expediente_mensajes');
    }
}