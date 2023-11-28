<?php

namespace Database\Seeders;

use App\Models\VigenciaModel;
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
        foreach ($this->vigencia() as $proceso) {
            VigenciaModel::create($proceso) ;
        }
    }

    public function MasTipoEstadoEtapa()
    {
        return [
            ["id" => "1","nombre" => "contestado"],
            ["id" => "1","nombre" => "finalizado"],
            ["id" => "1","nombre" => "remitido"],

        ];
    }
}
