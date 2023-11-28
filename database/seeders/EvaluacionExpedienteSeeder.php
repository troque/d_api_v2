<?php

namespace Database\Seeders;

use App\Models\EvaluacionoExpedientePermitidoModel;
use App\Models\MasOriginFiling;
use App\Models\OrigenRadicadoModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class EvaluacionExpedienteSeeder extends Seeder
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
            if ($idFase == null) echo 'Error la fase no existe ' . $origen['fase_id'];
            DB::table('evaluacion_expediente_permitido')->insert(
                array(
                    'tipo_expediente_id' => $origen['tipo_expediente_id'],
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
            [
                "tipo_expediente_id" => 1,
                "fase_id" => "Evaluación"
            ],
            [
                "tipo_expediente_id" => 1,
                "fase_id" => "Gestor respuesta"
            ],
            [
                "tipo_expediente_id" => 1,
                "fase_id" => "Documento cierre"
            ],
            [
                "tipo_expediente_id" => 1,
                "fase_id" => "Cierre total"
            ]
        ];
    }
}
