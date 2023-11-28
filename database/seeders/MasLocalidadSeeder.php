<?php

namespace Database\Seeders;

use App\Models\LocalidadModel;
use Illuminate\Database\Seeder;

class MasLocalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->localidad() as $proceso) {
            LocalidadModel::create($proceso) ;
        }
    }

    public function Localidad()
    {
        return [
            [
                "nombre" => "Antonio Nariño",
                "estado" => true
            ],
            [
                "nombre" => "Barrios Unidos",
                "estado" => true
            ],
            [
                "nombre" => "Bosa",
                "estado" => true
            ],
            [
                "nombre" => "Chapinero",
                "estado" => true
            ],
            [
                "nombre" => "Ciudad Bolívar",
                "estado" => true
            ],
            [
                "nombre" => "Engativá",
                "estado" => true
            ],
            [
                "nombre" => "Fontibón",
                "estado" => true
            ],
            [
                "nombre" => "Kennedy",
                "estado" => true
            ],
            [
                "nombre" => "La Candelaria",
                "estado" => true
            ],
            [
                "nombre" => "Los Mártires",
                "estado" => true
            ],
            [
                "nombre" => "Puente Aranda",
                "estado" => true
            ],
            [
                "nombre" => "Rafael Uribe Uribe",
                "estado" => true
            ],
            [
                "nombre" => "San Cristóbal",
                "estado" => true
            ],
            [
                "nombre" => "Santa Fe",
                "estado" => true
            ],
            [
                "nombre" => "Suba",
                "estado" => true
            ],
            [
                "nombre" => "Sumapaz",
                "estado" => true
            ],
            [
                "nombre" => "Teusaquillo",
                "estado" => true
            ],
            [
                "nombre" => "Tunjuelito",
                "estado" => true
            ],
            [
                "nombre" => "Usaquén",
                "estado" => true
            ],
            [
                "nombre" => "Usme",
                "estado" => true
            ],
        ];
    }
}
