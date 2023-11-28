<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTipoExpedienteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users_tipo_expediente', function (Blueprint $table) {
            //FOREIGN KEY CONSTRAINTS
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('tipo_expediente_id')->constrained('mas_tipo_expediente');
            $table->string("created_user")->nullable();
            $table->string("updated_user")->nullable();
            $table->string("deleted_user")->nullable();
            $table->timestamps();
            
            //SETTING THE PRIMARY KEYS
            $table->primary(['user_id', 'tipo_expediente_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users_tipo_expediente');
    }
}
