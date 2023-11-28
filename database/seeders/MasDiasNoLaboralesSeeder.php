<?php

namespace Database\Seeders;

use App\Models\DiasNoLaboralesModel;
use Illuminate\Database\Seeder;

class MasDiasNoLaboralesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->diasNoLaborales() as $proceso) {
            DiasNoLaboralesModel::create($proceso);
        }
    }

    public function DiasNoLaborales()
    {
        return [
            [
                "fecha" => date('Y-m-d'),
                "estado" => true
            ]
        ];
    }
}
