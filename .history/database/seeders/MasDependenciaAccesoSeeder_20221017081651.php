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
                "id" => 1,
                "nombre" => "Remitir proceso",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "Creacion Proceso",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "Crear remision queja - Incorporación",
                "estado" => true
            ],
            [
                "id" => 4,
                "nombre" => "Crear interesado",
                "estado" => true
            ],
            [
                "id" => 5,
                "nombre" => "Crear usuario",
                "estado" => true
            ],
            [
                "id" => 6,
                "nombre" => "Modificar usuario",
                "estado" => true
            ],
            [
                "id" => 7,
                "nombre" => "Crear remision queja - Comisorio Eje",
                "estado" => true
            ],
            [
                "id" => 8,
                "nombre" => "Crear remision queja - Remisorio Interno",
                "estado" => true
            ],
            [
                "id" => 9,
                "nombre" => "Queja interna",
                "estado" => true
            ],
            [
                "id" => 10,
                "nombre" => "Dependencia eje disciplinario",
                "estado" => true
            ],
            [
                "id" => 11,
                "nombre" => "Juzgamiento",
                "estado" => true
            ],
            [
                "id" => 12,
                "nombre" => "Secretaria Común",
                "estado" => true
            ],




        ];
    }
}
