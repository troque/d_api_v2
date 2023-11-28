<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMasDependenciaConfiguracionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mas_dependencia_configuracion', function (Blueprint $table) {
            //FOREIGN KEY CONSTRAINTS
            $table->foreignId('id_dependencia_origen')->constrained('mas_dependencia_origen');
            $table->foreignId('id_dependencia_acceso')->constrained('mas_dependencia_acceso');
            $table->string("created_user")->nullable();
            $table->string("updated_user")->nullable();
            $table->string("deleted_user")->nullable();
            
            //SETTING THE PRIMARY KEYS
            $table->primary(['id_dependencia_origen', 'id_dependencia_acceso']);
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
        Schema::dropIfExists('mas_dependencia_configuracion');
    }
}
