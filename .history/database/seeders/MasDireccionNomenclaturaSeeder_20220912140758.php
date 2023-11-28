<?php

namespace Database\Seeders;

use App\Models\DireccionNomenclaturaModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasDireccionNomenclaturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_direccion_nomenclatura')->delete();

        foreach ($this->tipoDireccionNomenclatura() as $item) {
            DireccionNomenclaturaModel::create($item);
        }
    }

    public function tipoDireccionNomenclatura()
    {
        return [
            [
                "id" => 1,
                "nombre" => "apartamento",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "avenida",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "autopista",
                "estado" => true
            ],
            [
                "id" => 4,
                "nombre" => "barrio",
                "estado" => true
            ],
            [
                "id" => 5,
                "nombre" => "calle",
                "estado" => true
            ],
            [
                "id" => 6,
                "nombre" => "carrera",
                "estado" => true
            ],
            [
                "id" => 7,
                "nombre" => "diagonal",
                "estado" => true
            ],
            [
                "id" => 8,
                "nombre" => "edificio",
                "estado" => true
            ],
            [
                "id" => 9,
                "nombre" => "norte",
                "estado" => true
            ],
            [
                "id" => 10,
                "nombre" => "sur",
                "estado" => true
            ],
            [
                "id" => 11,
                "nombre" => "transversal",
                "estado" => true
            ],
        ];
    }
}
