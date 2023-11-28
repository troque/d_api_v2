<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogEtapaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_etapa', function (Blueprint $table) {
            $table->uuid('uuid')->primary();;
            $table->string('id_proceso_disciplinario');
            $table->string('id_etapa');
            $table->string('id_fase');
            $table->string('id_tipo_cambio');
            $table->string('id_estado');
            $table->string('descripcion');
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
        Schema::dropIfExists('log_etapa');
    }
}
