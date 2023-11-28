<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortalNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portal_notificaciones', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->string('uuid_proceso_disciplinario');
            $table->integer("numero_documento");
            $table->integer("tipo_documento");
            $table->string("detalle");
            $table->integer("radicado");
            $table->boolean("estado")->default(true);
            $table->string("created_user", 256)->nullable();
            $table->string("updated_user", 256)->nullable();
            $table->string("deleted_user", 256)->nullable();
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
        Schema::dropIfExists('portal_notificaciones');
    }
}