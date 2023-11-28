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
        Schema::table('proceso_disciplinario_por_semaforo', function (Blueprint $table) {
            $table->foreignUuid('id_actuacion_finaliza')->nullable()->constrained('actuaciones','uuid');
            $table->foreignId('id_dependencia_finaliza')->nullable()->constrained('mas_dependencia_origen');
            $table->foreignId('id_usuario_finaliza')->nullable()->constrained('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
