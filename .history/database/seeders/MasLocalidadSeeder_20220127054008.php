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
                "nombre" => "Localidad 1",
                "estado" => true
            ],
            [
                "nombre" => "Localidad 2",
                "estado" => true
            ],
            [
                "nombre" => "Localidad 3",
                "estado" => true
            ],
        ];
    }
}
