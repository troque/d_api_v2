<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MasTipoExpedienteMensajesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('MAS_TIPO_EXPEDIENTE_MENSAJES')->delete();

        foreach ($this->mas_tipo_expediente_mensajes() as $tipo) {
            DB::table('MAS_TIPO_EXPEDIENTE_MENSAJES')->insert(
                array(
                    'mensaje' => $tipo['mensaje'],
                    'id_tipo_expediente' => $tipo['id_tipo_expediente'],
                    'id_sub_tipo_expediente' => $tipo['id_sub_tipo_expediente'],
                    'estado' => $tipo['estado'],
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                )
            );
        }
    }

    /**
     *
     */
    public function mas_tipo_expediente_mensajes()
    {
        return [
            [
                "mensaje" => "Un Derecho de petición considerado como Copias cuenta con 15 días para ser resuelto.",
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 1,
                "estado" => 1
            ],
            [
                "mensaje" => "Un Derecho de petición considerado como General cuenta con 15 días para ser resuelto.",
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 2,
                "estado" => 1
            ],
            [
                "mensaje" => "Un Derecho de petición considerado como Alerta de control político cuenta con 3 días para ser resuelto.",
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 3,
                "estado" => 1
            ],
        ];
    }
}
