<?php

namespace Database\Seeders;

use App\Models\MasEstadoVisibilidadModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasEstadoVisibilidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->Estados() as $proceso) {
            MasEstadoVisibilidadModel::create($proceso);
        }
    }

    public function Estados()
    {
        return [
            [
                "id" => 1,
                "nombre" => "VISIBLE PARA TODOS",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "VISIBLE PARA LA DEPENDENCIA",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "VISIBLE SOLO PARA MI Y EL JEFE",
                "estado" => true
            ],
            [
                "id" => 4,
                "nombre" => "OCULTO A TODOS",
                "estado" => true
            ]
        ];
    }
}
