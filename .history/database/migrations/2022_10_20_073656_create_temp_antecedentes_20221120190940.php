<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTempAntecedentes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('temp_antecedentes', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->string("id_temp_proceso_disciplinario")->constrained('temp_proceso_disciplinario')->nullable();
            $table->string("descripcion")->nullable();
            $table->date("fecha_registro")->nullable();
            $table->boolean("estado")->nullable();
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
        Schema::dropIfExists('temp_antecedentes');
    }
}
