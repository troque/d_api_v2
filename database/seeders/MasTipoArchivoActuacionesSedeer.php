<?php

namespace Database\Seeders;

use App\Models\TipoArchivoActuacionesModel;
use Illuminate\Database\Seeder;

class MasTipoArchivoActuacionesSedeer extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->MasTipoArchivoActuaciones() as $proceso) {
            TipoArchivoActuacionesModel::create($proceso);
        }
    }

    public function MasTipoArchivoActuaciones()
    {
        return [
            [
                "nombre" => "Documento inicial",
                "codigo" => "DOCINI",
                "descripcion" => "Documento inicial de la actuación"
            ],
            [
                "nombre" => "Documento definitivo",
                "codigo" => "DOCFIN",
                "descripcion" => "Documento definitivo o final de la actuación"
            ]
        ];
    }
}