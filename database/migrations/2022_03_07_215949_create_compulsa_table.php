<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompulsaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compulsa', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->uuid('id_proceso_disciplinario');
            $table->string("radicado", 50);
            $table->string("vigencia", 4);
            $table->uuid('id_proceso_disciplinario_compulsa');
            $table->string("radicado_compulsa", 50);
            $table->string("vigencia_compulsa", 4);
            $table->uuid("id_documento_sirius");
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
        Schema::dropIfExists('compulsa');
    }
}
