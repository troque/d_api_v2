<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatedMasTipoFuncionario extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $table->id()->primary();
        $table->string("nombre");
        $table->boolean("estado");
        $table->string("created_user", 256)->nullable();
        $table->string("updated_user", 256)->nullable();
        $table->string("deleted_user", 256)->nullable();
        $table->timestamps();
        $table->softDeletes();
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
}
