<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MasTipoCierreEtapaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_tipo_cierre_etapa')->delete();

        foreach ($this->mas_tipo_cierre_etapa() as $tipo) {
            DB::table('mas_tipo_cierre_etapa')->insert(
                array(
                    'nombre' => $tipo['nombre'],
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
    public function mas_tipo_cierre_etapa()
    {
        return [
            [
                "nombre" => "REPARTO ALEATORIO",
                "estado" => "1"
            ],
            [
                "nombre" => "ASIGNADO A SI MISMO",
                "estado" => "1"
            ],
            [
                "nombre" => "ASIGNACION DIRIGIDA",
                "estado" => "1"
            ],
            [
                "nombre" => "CIERRE DEFINITVO",
                "estado" => "1"
            ],
        ];
    }
}
