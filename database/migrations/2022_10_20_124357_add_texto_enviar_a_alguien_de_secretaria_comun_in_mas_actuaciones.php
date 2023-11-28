<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTextoEnviarAAlguienDeSecretariaComunInMasActuaciones extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mas_actuaciones', function (Blueprint $table) {
            $table->string('texto_enviar_a_alguien_de_secretaria_comun_dirigido', 4000)->nullable()->after('TEXTO_REGRESAR_PROCESO_AL_ULTIMO_USUARIO');
            $table->string('texto_enviar_a_alguien_de_secretaria_comun_aleatorio', 4000)->nullable()->after('TEXTO_REGRESAR_PROCESO_AL_ULTIMO_USUARIO');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
