<?php

namespace Database\Seeders;

use App\Models\OrientacionSexualModel;
use Illuminate\Database\Seeder;

class MasOrientacionSexualSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->orientacionSexual() as $proceso) {
            OrientacionSexualModel::create($proceso) ;
        }
    }

    public function OrientacionSexual()
    {
        return [
            [
                "nombre" => "Bisexual",
                "estado" => true
            ],
            [
                "nombre" => "Heterosexual",
                "estado" => true
            ],
            [
                "nombre" => "Homosexual",
                "estado" => true
            ],
            [
                "nombre" => "No deseo informar",
                "estado" => true
            ],
        ];
    }
}
