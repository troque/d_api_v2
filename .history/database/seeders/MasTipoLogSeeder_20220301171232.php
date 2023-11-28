<?php

namespace Database\Seeders;


use App\Models\TipoLogModel;
use Illuminate\Database\Seeder;

class MasEtapaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->masTipoLog() as $tipoLog) {
         TipoLogModel::create($tipoLog) ;
        }
    }

    public function masTipoLog()
    {
        return [

            [
                "nombre" => "Etapa",
                "id_tipo_proceso" => 1
            ],

            [
                "nombre" => "Fase",
                "id_tipo_proceso" => 1
            ],
        ];
    }
}
