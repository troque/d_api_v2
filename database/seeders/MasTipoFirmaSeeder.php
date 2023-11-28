<?php

namespace Database\Seeders;

use App\Models\TipoFirmaModel;
use Illuminate\Database\Seeder;

class MasTipoFirmaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoFirma() as $tipo_expediente) {
            TipoFirmaModel::create($tipo_expediente);
        }
    }

    public function tipoFirma()
    {
        return [
            [
                "nombre" => "Principal",
                "estado" => true,
                "tamano" => 2,
            ],
            [
                "nombre" => "Firmó",
                "estado" => true,
                "tamano" => 1,
            ],
            [
                "nombre" => "Elaboró",
                "estado" => true,
                "tamano" => 0,
            ],
        ];
    }
}