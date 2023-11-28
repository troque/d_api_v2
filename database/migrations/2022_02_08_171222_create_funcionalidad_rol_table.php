<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFuncionalidadRolTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funcionalidad_rol', function (Blueprint $table) {
            //FOREIGN KEY CONSTRAINTS
            $table->foreignId('funcionalidad_id')->constrained('mas_funcionalidad');
            $table->foreignId('role_id')->constrained('roles');

            //SETTING THE PRIMARY KEYS
            $table->primary(['funcionalidad_id', 'role_id']);
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funcionalidad_rol');
    }
}
