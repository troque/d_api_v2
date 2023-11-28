<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasParametroCamposModel;

class MasParametroCamposSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->ParametroCampos() as $proceso) {
            MasParametroCamposModel::create($proceso);
        }
    }

    public function ParametroCampos()
    {
        return [
            [
                "nombre_campo" => "Antecedentes",
                "type" => "Antecedentes",
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
                "nombre_campo" => "Investigado",
                "type" => "Investigado",
                "value" => "",
                "estado" => true
            ],
            [
                "nombre_campo" => "Cargo",
                "type" => "Cargo",
                "value" => "",
                "estado" => true
            ],
            [
                "nombre_campo" => "Entidad",
                "type" => "Entidad",
                "value" => "",
                "estado" => true
            ],
            [
                "nombre_campo" => "Interesados",
                "type" => "Interesados",
                "value" => "",
                "estado" => true
            ],
            [
                "nombre_campo" => "Fecha de Ingreso",
                "type" => "fecha de Ingreso",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Fecha de Registro",
                "type" => "Fecha de Registro",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Número de auto (generado despues de aprobación)",
                "type" => "Número de auto (generado despues de aprobación)",
                "value" => '${numero_de_auto}',
                "estado" => true
            ],
            [
                "nombre_campo" => "Radicación",
                "type" => "Sinproc",
                "value" => "",
                "estado" => true
            ],
            [
                "nombre_campo" => "Hechos",
                "type" => "Interesados",
                "value" => "",
                "estado" => true
            ],
            [
                "nombre_campo" => "Número de radicado",
                "type" => "Sinproc",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Dependencia Origen",
                "type" => "Dependencia",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Delegada",
                "type" => "Dependencia",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Fecha Hechos",
                "type" => "Fecha Hechos",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Quejoso",
                "type" => "Interesados",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Radicado",
                "type" => "Número de radicado",
                "value" => null,
                "estado" => true
            ],
            [
                "nombre_campo" => "Auto",
                "type" => "Número de auto (generado despues de aprobación)",
                "value" => null,
                "estado" => true
            ],
        ];
    }
}