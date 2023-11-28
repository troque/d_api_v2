<?php

namespace Database\Seeders;


use App\Models\TipoLogModel;
use Illuminate\Database\Seeder;

class MasTipoLogSeeder extends Seeder
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
                "nombre" => "etapa",
            ],

            [
                "nombre" => "fase",
            ],
        ];
    }
}
