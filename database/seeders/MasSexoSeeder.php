<?php

namespace Database\Seeders;

use App\Models\SexoModel;
use Illuminate\Database\Seeder;

class MasSexoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->sexo() as $proceso) {
            SexoModel::create($proceso) ;
        }
    }

    public function Sexo()
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
                "nombre" => "Intersexual",
                "estado" => true
            ],
        ];
    }
}
