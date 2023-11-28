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
                "nombre" => "Apartamento",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "Avenida",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "Autopista",
                "estado" => true
            ],
            [
                "id" => 4,
                "nombre" => "Barrio",
                "estado" => true
            ],
            [
                "id" => 5,
                "nombre" => "Calle",
                "estado" => true
            ],
            [
                "id" => 6,
                "nombre" => "Carrera",
                "estado" => true
            ],
            [
                "id" => 7,
                "nombre" => "Diagonal",
                "estado" => true
            ],
            [
                "id" => 8,
                "nombre" => "Edificio",
                "estado" => true
            ],
            [
                "id" => 9,
                "nombre" => "Norte",
                "estado" => true
            ],
            [
                "id" => 10,
                "nombre" => "Sur",
                "estado" => true
            ],
            [
                "id" => 11,
                "nombre" => "Transversal",
                "estado" => true
            ],
        ];
    }
}
