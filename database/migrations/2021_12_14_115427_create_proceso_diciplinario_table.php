<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProcesoDiciplinarioTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proceso_disciplinario', function (Blueprint $table) {
            //$table->id();
            $table->uuid('uuid')->unique();
            $table->string("radicado", 50);
            $table->string("vigencia", 4)->nullable();
            //$table->string("antecedente", 2000);
            $table->boolean("estado")->default(0);
            $table->integer("id_tipo_proceso");
            $table->integer("id_origen_radicado");
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
        Schema::dropIfExists('proceso_disciplinario');
    }
}
