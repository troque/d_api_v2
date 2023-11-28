<?php

namespace Database\Seeders;

use App\Models\DependenciaConfiguracionModel;
use App\Models\DependenciaOrigenModel;
use Illuminate\Database\Seeder;

class MasDependenciaConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        foreach ($this->dependenciaConfiguracion() as $dependencia) {
            DependenciaConfiguracionModel::create($dependencia);
        }
    }

    public function dependenciaConfiguracion()
    {
        return [

            ["id_dependencia_origen" => "0", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "0", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "0", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "0", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "0", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "0", "id_dependencia_acceso" => "6"],

            ["id_dependencia_origen" => "5", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "5", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "5", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "5", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "5", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "5", "id_dependencia_acceso" => "6"],
            ["id_dependencia_origen" => "5", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "31", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "31", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "31", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "31", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "31", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "31", "id_dependencia_acceso" => "6"],
            ["id_dependencia_origen" => "31", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "50", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "50", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "50", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "50", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "50", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "50", "id_dependencia_acceso" => "6"],
            ["id_dependencia_origen" => "50", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "50"],

            ["id_dependencia_origen" => "61", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "61", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "61", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "61", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "61", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "61", "id_dependencia_acceso" => "6"],
            ["id_dependencia_origen" => "61", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "64", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "64", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "64", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "64", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "64", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "64", "id_dependencia_acceso" => "6"],
            ["id_dependencia_origen" => "64", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "310", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "310", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "310", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "310", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "310", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "310", "id_dependencia_acceso" => "6"],

            ["id_dependencia_origen" => "314", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "314", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "314", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "314", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "314", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "314", "id_dependencia_acceso" => "6"],

            ["id_dependencia_origen" => "318", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "318", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "318", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "318", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "318", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "318", "id_dependencia_acceso" => "6"],

            ["id_dependencia_origen" => "397", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "397", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "397", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "397", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "397", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "397", "id_dependencia_acceso" => "6"],

            ["id_dependencia_origen" => "410", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "410", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "410", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "410", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "410", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "410", "id_dependencia_acceso" => "6"],

            ["id_dependencia_origen" => "411", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "411", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "411", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "411", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "411", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "411", "id_dependencia_acceso" => "6"],

            ["id_dependencia_origen" => "412", "id_dependencia_acceso" => "1", "porcentaje_asignacion" => "100"],
            ["id_dependencia_origen" => "412", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "412", "id_dependencia_acceso" => "5"],

            ["id_dependencia_origen" => "413", "id_dependencia_acceso" => "1"],
            ["id_dependencia_origen" => "413", "id_dependencia_acceso" => "2"],
            ["id_dependencia_origen" => "413", "id_dependencia_acceso" => "3"],
            ["id_dependencia_origen" => "413", "id_dependencia_acceso" => "4"],
            ["id_dependencia_origen" => "413", "id_dependencia_acceso" => "5"],
            ["id_dependencia_origen" => "413", "id_dependencia_acceso" => "6"],
            ["id_dependencia_origen" => "413", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "414", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],
            ["id_dependencia_origen" => "414", "id_dependencia_acceso" => "12", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "415", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "416", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

            ["id_dependencia_origen" => "417", "id_dependencia_acceso" => "7", "porcentaje_asignacion" => "100"],

        ];
    }
}
