<?php

namespace Database\Seeders;

use App\Models\TipoInteresadoModel;
use Illuminate\Database\Seeder;

class MasTipoInteresadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoInteresado() as $proceso) {
            TipoInteresadoModel::create($proceso) ;
        }
    }

    public function TipoInteresado()
    {
        return [
            [
                "nombre" => "Persona Natural",
                "estado" => true
            ],
            [
                "nombre" => "Entidad",
                "estado" => true
            ]
        ];
    }
}
