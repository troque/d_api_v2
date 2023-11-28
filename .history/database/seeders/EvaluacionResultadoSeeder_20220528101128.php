<?php

namespace Database\Seeders;

use App\Models\EvaluacionoExpedientePermitidoModel;
use App\Models\MasOriginFiling;
use App\Models\OrigenRadicadoModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class EvaluacionResultadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->evaluacionoExpedientePermitido() as $origen) {


            $idFase = DB::table('mas_fase')->where('nombre', '=', $origen['fase_id'])->value('id');
            $idResultado = DB::table('mas_resultado_evaluacion')->where('nombre', '=', $origen['resultado_evaluacion_id'])->value('id');
            if ($idFase == null) echo 'Error el resultado evaluacion no existe ' . $origen['resultado_evaluacion_id'];
            DB::table('evaluacion_resultado_permitido')->insert(
                array(
                    'resultado_evaluacion_id' => $idResultado,
                    'fase_id' => $idFase,
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                )
            );

        }
    }

    public function evaluacionoExpedientePermitido()
    {
        return [
            //comisorio eje
            [
                "resultado_evaluacion_id" => 'Comisorio Eje',
                "fase_id" => "Validación clasificación"
            ],
            [
                "resultado_evaluacion_id" => "Comisorio Eje",
                "fase_id" => "Evaluación"
            ],
            [
                "resultado_evaluacion_id" =>  "Comisorio Eje",
                "fase_id" => "Remision queja"
            ],
            [
                "resultado_evaluacion_id" => "Comisorio Eje",
                "fase_id" => "Gestor Respuesta"
            ],
            [
                "resultado_evaluacion_id" => "Comisorio Eje",
                "fase_id" => "Comunicacion interesado"
            ],
            [
                "resultado_evaluacion_id" => "Comisorio Eje",
                "fase_id" => "Documento Cierre"
            ],
            [
                "resultado_evaluacion_id" => "Comisorio Eje",
                "fase_id" => "Cierre total"
            ],


            //Devolución Entidad
            [
                "resultado_evaluacion_id" => 'Devolución Entidad',
                "fase_id" => "Validación clasificación"
            ],
            [
                "resultado_evaluacion_id" => "Devolución Entidad",
                "fase_id" => "Evaluación"
            ],
            [
                "resultado_evaluacion_id" =>  "Devolución Entidad",
                "fase_id" => "Remision queja"
            ],
            [
                "resultado_evaluacion_id" => "Devolución Entidad",
                "fase_id" => "Gestor Respuesta"
            ],
            [
                "resultado_evaluacion_id" => "Devolución Entidad",
                "fase_id" => "Comunicacion interesado"
            ],
            [
                "resultado_evaluacion_id" => "Devolución Entidad",
                "fase_id" => "Documento Cierre"
            ],
            [
                "resultado_evaluacion_id" => "Devolución Entidad",
                "fase_id" => "Cierre total"
            ],



            //Incorporación
            [
                "resultado_evaluacion_id" => 'Incorporación',
                "fase_id" => "Validación clasificación"
            ],
            [
                "resultado_evaluacion_id" => "Incorporación",
                "fase_id" => "Evaluación"
            ],
            [
                "resultado_evaluacion_id" =>  "Incorporación",
                "fase_id" => "Remision queja"
            ],
            [
                "resultado_evaluacion_id" => "Incorporación",
                "fase_id" => "Gestor Respuesta"
            ],
            [
                "resultado_evaluacion_id" => "Incorporación",
                "fase_id" => "Comunicacion interesado"
            ],
            [
                "resultado_evaluacion_id" => "Incorporación",
                "fase_id" => "Documento Cierre"
            ],
            [
                "resultado_evaluacion_id" => "Incorporación",
                "fase_id" => "Cierre total"
            ],


            //Remisorio Externo
            [
                "resultado_evaluacion_id" => 'Remisorio Externo',
                "fase_id" => "Validación clasificación"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Externo",
                "fase_id" => "Evaluación"
            ],
            [
                "resultado_evaluacion_id" =>  "Remisorio Externo",
                "fase_id" => "Remision queja"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Externo",
                "fase_id" => "Gestor Respuesta"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Externo",
                "fase_id" => "Comunicacion interesado"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Externo",
                "fase_id" => "Documento Cierre"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Externo",
                "fase_id" => "Cierre total"
            ],



            //Remisorio Interno
            [
                "resultado_evaluacion_id" => 'Remisorio Interno',
                "fase_id" => "Validación clasificación"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Interno",
                "fase_id" => "Evaluación"
            ],
            [
                "resultado_evaluacion_id" =>  "Remisorio Interno",
                "fase_id" => "Remision queja"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Interno",
                "fase_id" => "Gestor Respuesta"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Interno",
                "fase_id" => "Comunicacion interesado"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Interno",
                "fase_id" => "Documento Cierre"
            ],
            [
                "resultado_evaluacion_id" => "Remisorio Interno",
                "fase_id" => "Cierre total"
            ]


        ];
    }
}
