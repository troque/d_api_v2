<?php

namespace Database\Seeders;

use App\Models\EtapaModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasEtapaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_fase')->delete();
        DB::table('mas_etapa')->delete();
        DB::statement("alter sequence MAS_ETAPA_ID_SEQ restart start with 1");

        foreach ($this->MasEtapa() as $process) {
            EtapaModel::create($process);
        }
    }

    public function MasEtapa()
    {
        return [
            [
                "id" => "0",
                "nombre" => "Forma de ingreso",
                "estado" => false,
                "id_tipo_proceso" => 1,
                "orden" => 0,
                "estado_poder_preferente" => 0,
            ],
            [
                "nombre" => "Captura y Reparto",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 1,
            ],
            [
                "nombre" => "Evaluación",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 2
            ],
            [
                "nombre" => "Evaluación en PD",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 3
            ],
            [
                "nombre" => "Investigación Preliminar",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 4
            ],
            [
                "nombre" => "Investigación Disciplinaria",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 5
            ],

            [
                "nombre" => "Causa / Juzgamiento",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 6
            ],
            [
                "nombre" => "Proceso Verbal",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 7
            ],
            [
                "nombre" => "Segunda Instancia",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 8
            ],
            [
                "nombre" => "Inicio Proceso Disciplinario",
                "estado" => true,
                "id_tipo_proceso" => 1,
                "orden" => 9
            ],
        ];
    }
}
