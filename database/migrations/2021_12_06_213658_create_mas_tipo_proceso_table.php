<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasTipoProcesoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_tipo_proceso', function (Blueprint $table) {
            $table->id();
            $table->string("nombre");
            $table->boolean("estado");
            $table->string("created_user")->nullable();
            $table->string("updated_user")->nullable();
            $table->string("deleted_user")->nullable();
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
        Schema::dropIfExists('mas_tipo_proceso');
    }
}
