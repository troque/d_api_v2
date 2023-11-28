<?php

namespace Database\Seeders;
use App\Models\ResultadoEvaluacionModel;
use Illuminate\Database\Seeder;

class MasResultadoEvaluacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->resultadoEvaluacion() as $proceso) {
            ResultadoEvaluacionModel::create($proceso) ;
        }
    }

    public function resultadoEvaluacion()
    {
        return [
            [
                "nombre" => "Comisorio Eje",
                "estado" => true
            ],
            [
                "nombre" => "Devolución Entidad",
                "estado" => true
            ],
            [
                "nombre" => "Incorporación",
                "estado" => true
            ],
            [
                "nombre" => "Remisorio Externo",
                "estado" => true
            ],
            [
                "nombre" => "Remisorio Interno",
                "estado" => true
            ],
            [
                "nombre" => "Sin Evaluación",
                "estado" => false
            ],

        ];
    }
}

