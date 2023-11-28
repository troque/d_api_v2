<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncorporacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incorporacion', function (Blueprint $table) {
            $table->uuid('uuid')->unique();
            $table->uuid('id_proceso_disciplinario_expediente');
            $table->uuid('id_proceso_disciplinario_incorporado');
            $table->integer("id_dependencia_origen");
            $table->string("expediente", 50);
            $table->string("vigencia_expediente", 4)->nullable();
            $table->integer("version");
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
        Schema::dropIfExists('incorporacion');
    }
}
