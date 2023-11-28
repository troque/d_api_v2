<?php

namespace Database\Seeders;

use App\Models\TipoProcesoModel;
use Illuminate\Database\Seeder;

class MasTipoProcesoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoProceso() as $proceso) {
            TipoProcesoModel::create($proceso);
        }
    }

    public function tipoProceso()
    {
        return [
            [
                "nombre" => "CORRESPONDENCIA - SIRIUS",
                "estado" => true
            ],
            [
                "nombre" => "DESGLOSE",
                "estado" => true
            ],
            [
                "nombre" => "SINPROC",
                "estado" => true
            ],
            [
                "nombre" => "PROCESO DISCIPLINARIO",
                "estado" => true
            ],
        ];
    }
}
