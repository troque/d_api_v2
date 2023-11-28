<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Modulo;
use App\Models\ModuloGrupoModel;

class MasModuloGrupoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        foreach ($this->modulos() as $modulo) {
            ModuloGrupoModel::create($modulo);
        }
    }

    public function modulos()
    {
        return [
            [
                "id" => 1,
                "nombre" => "MIS PENDIENTES",
                "orden" => 1,
                "estado" => true,
            ],
            [
                "id" => 2,
                "nombre" => "CAPTURA Y REPARTO",
                "orden" => 2,
                "estado" => true,
            ],
            [
                "id" => 3,
                "nombre" => "EVALUACIÓN QUEJA PQR",
                "orden" => 3,
                "estado" => true,
            ],
            [
                "id" => 4,
                "nombre" => "ACTUACIONES",
                "orden" => 4,
                "estado" => true,
            ],
            [
                "id" => 5,
                "nombre" => "TRANSACCIONES",
                "orden" => 5,
                "estado" => true,
            ],
            [
                "id" => 6,
                "nombre" => "BUSCADOR",
                "orden" => 7,
                "estado" => true,
            ],
            [
                "id" => 7,
                "nombre" => "MIGRACIÓN",
                "orden" => 8,
                "estado" => true,
            ],
            [
                "id" => 8,
                "nombre" => "CAJA DE HERRAMIENTAS",
                "orden" => 9,
                "estado" => true,
            ],

            [
                "id" => 9,
                "nombre" => "GENERALIDADES",
                "orden" => 0,
                "estado" => true,
            ],

            [
                "id" => 10,
                "nombre" => "ADMINISTRADOR",
                "orden" => 10,
                "estado" => true,
            ],

            [
                "id" => 11,
                "nombre" => "TRANSACCIONES EXCLUSIVAS PARA SECRETARIA COMÚN",
                "orden" => 6,
                "estado" => true,
            ],

        ];
    }
}
