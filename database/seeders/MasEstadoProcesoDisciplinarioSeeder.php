<?php

namespace Database\Seeders;

use App\Models\EstadoProcesoDisciplinarioModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasEstadoProcesoDisciplinarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('MAS_ESTADO_PROCESO_DISCIPLINARIO')->delete();
        foreach ($this->TipoFormato() as $formato) {
            EstadoProcesoDisciplinarioModel::create($formato);
        }
    }

    public function TipoFormato()
    {
        return [
            [
                "nombre" => "Activo",
                "estado" => true
            ],
            [
                "nombre" => "Cerrado",
                "estado" => true
            ],
            [
                "nombre" => "Archivado",
                "estado" => true
            ]
        ];
    }
}
