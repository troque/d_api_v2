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
            ["nombre" => "reparto_aleatorio","estado" => true],
            ["nombre" => "asignado_asi_mismo","estado" => true],
            ["nombre" => "asignacion_dirigida","estado" => true],
        ];
    }
}
