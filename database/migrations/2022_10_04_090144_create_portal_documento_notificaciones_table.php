<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePortalDocumentoNotificacionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('portal_documento_notificaciones', function (Blueprint $table) {
            $table->uuid('uuid')->primary()->unique();
            $table->integer("uuid_notificaciones")->constrained('portal_notificaciones')->nullable();
            $table->string("documento");
            $table->string("extension");
            $table->string("tamano");
            $table->string("ruta");
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
        Schema::dropIfExists('portal_documento_notificaciones');
    }
}