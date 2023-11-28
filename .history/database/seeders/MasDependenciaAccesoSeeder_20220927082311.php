<?php

namespace Database\Seeders;

use App\Models\DependenciaAccesoModel;
use App\Models\FaseModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MasDependenciaAccesoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('MAS_DEPENDENCIA_ACCESO')->delete();
        foreach ($this->DependenciaAcceso() as $dependenciaAcceso) {
            DependenciaAccesoModel::create($dependenciaAcceso);
        }
    }

    public function DependenciaAcceso()
    {
        return [
            [
                "nombre" => "Remitir proceso",
                "estado" => true
            ],
            [
                "nombre" => "Creacion Proceso",
                "estado" => true
            ],
            [
                "nombre" => "Crear remision queja - Incorporación",
                "estado" => true
            ],
            [
                "nombre" => "Crear interesado",
                "estado" => true
            ],
            [
                "nombre" => "Crear usuario",
                "estado" => true
            ],
            [
                "nombre" => "Modificar usuario",
                "estado" => true
            ],
            [
                "nombre" => "Crear remision queja - Comisorio Eje",
                "estado" => true
            ],
            [
                "nombre" => "Crear remision queja - Remisorio Interno",
                "estado" => true
            ],
            [
                "nombre" => "Queja interna",
                "estado" => true
            ],
        ];
    }
}
