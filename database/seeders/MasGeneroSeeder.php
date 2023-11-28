<?php

namespace Database\Seeders;

use App\Models\GeneroModel;
use Illuminate\Database\Seeder;

class MasGeneroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->genero() as $proceso) {
            GeneroModel::create($proceso) ;
        }
    }

    public function Genero()
    {
        return [
            [
                "nombre" => "Femenino",
                "estado" => true
            ],
            [
                "nombre" => "Masculino",
                "estado" => true
            ],
            [
                "nombre" => "Transgenero",
                "estado" => true
            ],
            [
                "nombre" => "No deseo informar",
                "estado" => true
            ],
        ];
    }
}
