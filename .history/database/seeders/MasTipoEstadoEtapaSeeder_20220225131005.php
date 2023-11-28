<?php

namespace Database\Seeders;

use App\Models\TipoEstadoEtapaModel;
use Illuminate\Database\Seeder;

class MasTipoEstadoEtapaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoEstadoEtapa() as $estado) {
            TipoEstadoEtapaModel::create($estado) ;
        }
    }

    public function tipoEstadoEtapa()
    {
        return [
            ["id" => "1","nombre" => "contestado"],
            ["id" => "2","nombre" => "finalizado"],
            ["id" => "3","nombre" => "remitido"],

        ];
    }
}
