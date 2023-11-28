<?php

namespace Database\Seeders;

use App\Models\TipoEntidadModel;
use Illuminate\Database\Seeder;

class MasTipoEntidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoEntidad() as $proceso) {
            TipoEntidadModel::create($proceso);
        }
    }

    public function TipoEntidad()
    {
        return [
            [
                "nombre" => "Pública",
                "estado" => true
            ],
            [
                "nombre" => "Privada",
                "estado" => true
            ]
        ];
    }
}
