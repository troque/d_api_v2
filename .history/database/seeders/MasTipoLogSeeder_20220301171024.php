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

    public function MasTipoLog()
    {
        return [

            [
                "id" => "0",
                "nombre" => "Forma de ingreso",
                "estado" => false,
                "id_tipo_proceso" => 1
            ],

            [
                "nombre" => "Captura y Reparto",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],
            [
                "nombre" => "Evaluación",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],
            [
                "nombre" => "Evaluación en PD",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],
            [
                "nombre" => "Investigación Preliminar",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],
            [
                "nombre" => "Investigación Disciplinaria",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],

            [
                "nombre" => "Causa / Juzgamiento",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],

            [
                "nombre" => "Proceso Verbal",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],
            [
                "nombre" => "Segunda Instancia",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],
            [
                "nombre" => "Inicio Proceso Disciplinario",
                "estado" => true,
                "id_tipo_proceso" => 1
            ],



        ];
    }
}
