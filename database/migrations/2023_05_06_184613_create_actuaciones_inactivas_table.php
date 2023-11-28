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
        Schema::create('actuaciones_inactivas', function (Blueprint $table) {
            $table->uuid()->primary();
            $table->foreignUuid('id_actuacion')->constrained('actuaciones','uuid');
            $table->foreignUuid('id_actuacion_principal')->constrained('actuaciones','uuid');
            $table->foreignUuid('id_proceso_disciplinario')->constrained('proceso_disciplinario','uuid');
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
        Schema::dropIfExists('actuaciones_inactivas');
    }
};
