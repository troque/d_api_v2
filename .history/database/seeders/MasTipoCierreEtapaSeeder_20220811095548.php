<?php

namespace Database\Seeders;

use App\Models\VigenciaModel;
use Illuminate\Database\Seeder;

class MasTipoCierreEtapaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->vigencia() as $proceso) {
            VigenciaModel::create($proceso) ;
        }
    }

    public function Vigencia()
    {
        return [
            ["vigencia" => "2022","estado" => true],
            ["vigencia" => "2021","estado" => true],
            ["vigencia" => "2020","estado" => true],
            ["vigencia" => "2019","estado" => false],
        ];
    }
}
