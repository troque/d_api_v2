<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GrupoTrabajoSecretariaComunModel;
use Illuminate\Support\Facades\DB;

class MasGrupoTrabajoSecretariaComunSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_grupo_trabajo_secretaria_comun')->delete();
        
        foreach ($this->grupo() as $proceso) {
            GrupoTrabajoSecretariaComunModel::create($proceso) ;
        }
    }

    public function grupo()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Entrada",
                "estado" => true,
            ],
            [
                "id" => 2,
                "nombre" => "Comunicaciones",
                "estado" => true,
            ],
            [
                "id" => 3,
                "nombre" => "Calidad",
                "estado" => true,
            ],
            [
                "id" => 4,
                "nombre" => "Notificaciones",
                "estado" => true,
            ],
            [
                "id" => 5,
                "nombre" => "Salida",
                "estado" => true,
            ]
        ];
    }
}