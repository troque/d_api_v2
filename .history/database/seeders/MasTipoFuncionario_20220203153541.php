<?php

namespace Database\Seeders;

use App\Models\TipoRespuestaModel;
use Illuminate\Database\Seeder;

class MasTipoRespuestaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->vigencia() as $proceso) {
            TipoRespuestaModel::create($proceso) ;
        }
    }

    public function Vigencia()
    {
        return [
            ["id" => 1,"nombre" => "si"],
            ["id" => 2, "nombre" => "no"]
        ];
    }
}
