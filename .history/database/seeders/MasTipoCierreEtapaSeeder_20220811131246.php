<?php

namespace Database\Seeders;

use App\Models\TipoCierreEtapaModel;
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
        foreach ($this->tipo_cierre_etapa() as $tipo) {
            TipoCierreEtapaModel::create($tipo) ;
        }
    }

    /**
     *
     */
    public function tipo_cierre_etapa()
    {
        return [
            ["nombre" => "Reparto aleatorio","estado" => true],
            ["nombre" => "Asignado asi mismo","estado" => true],
            ["nombre" => "Asignacion dirigida","estado" => true],
        ];
    }
}
