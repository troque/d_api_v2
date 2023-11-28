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
        Schema::table('actuaciones', function (Blueprint $table) {
            $table->foreignId('id_estado_visibilidad')->default(1)->nullable()->constrained('mas_estado_visibilidad');
            $table->foreignId('id_dependencia_origen')->nullable()->constrained('mas_dependencia_origen');
            $table->foreignId('id_usuario')->nullable()->constrained('users');
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
