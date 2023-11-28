<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ParametroCamposCaratulasModel;
use Illuminate\Support\Facades\DB;

class ParametroCamposCaratulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->ParametroCamposCaratula() as $proceso) {
            ParametroCamposCaratulasModel::create($proceso);
        }
    }

    public function ParametroCamposCaratula()
    {
        return [
            [
                "nombre_campo" => "Sinproc",
                "type" => "Sinproc",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Antecedentes",
                "type" => "Antecedentes",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Usuario",
                "type" => "Usuario",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Dependencia",
                "type" => "Dependencia",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Vigencia",
                "type" => "Vigencia",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Fecha Registro",
                "type" => "Fecha Registro",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Fecha Ingreso",
                "type" => "Fecha Ingreso",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Generado",
                "type" => "Generado",
                "value" => null,
                "estado" => true
            ],
        ];
    }
}