<?php

namespace Database\Seeders;

use App\Models\FaseModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class CierreEtapaConfiguracionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        foreach ($this->CierreEtapaConfiguracion() as $fase) {
            DB::table('cierre_etapa_configuracion')->insert(
                array(
                    'id_tipo_proceso_disciplinario' => $fase['id_tipo_proceso_disciplinario'],
                    'id_tipo_expediente' => $fase['id_tipo_expediente'],
                    'id_subtipo_expediente' => $fase['id_subtipo_expediente'],
                    'id_etapa' => $fase['id_etapa'],
                    'id_tipo_cierre_etapa' => $fase['id_tipo_cierre_etapa'],
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                )
            );
        }
    }

    public function CierreEtapaConfiguracion()
    {
        return [
            // SIRIUS DERECHO PETICION COPIAS CAPTURA Y REPARTO  REPARTO ALEATORIO
            [
                "id" => 1,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SIRIUS DERECHO PETICION GENERAL  CAPTURA Y REPARTO  REPARTO ALEATORIO
            [
                "id" => 2,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SIRIUS DERECHO PETICION ALERTA  CAPTURA Y REPARTO  REPARTO ALEATORIO
            [
                "id" => 3,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 3,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SIRIUS DERECHO PETICION COPIAS  EVALUACION CIERRE DEFINITIVO
            [
                "id" => 4,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_fase" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SIRIUS DERECHO PETICION GENERAL EVALUACION CIERRE DEFINITIVO
            [
                "id" => 5,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SIRIUS DERECHO PETICION ALERTA  EVALUACION CIERRE DEFINITIVO
            [
                "id" => 6,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 3,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SIRIUS QUEJA EXTERNA CAPTURA Y REPARTO  REPARTO ALEATORIO
            [
                "id" => 7,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SIRIUS QUEJA INTERNA CAPTURA Y REPARTO ASIGNADO A SI MISMO
            [
                "id" => 8,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 2
            ],
            // SIRIUS QUEJA EXTERNA EVALUACION ASIGNACION DIRIGIDA
            [
                "id" => 9,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 3
            ],
            // SIRIUS QUEJA INTERNA EVALUACION ASIGNACION DIRIGIDA
            [
                "id" => 10,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 3
            ],
            // SIRIUS TUTELA DIAS CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 11,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 4,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SIRIUS TUTELA HORAS CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 12,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 4,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SIRIUS TUTELA HORAS CAPTURA Y REPARTO CIERRE DEFINITIVO
            [
                "id" => 13,
                "id_tipo_proceso_disciplinario" => 1,
                "id_tipo_expediente" => 4,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SINPROC DERECHO PETICION COPIAS CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 14,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SINPROC DERECHO PETICION GENERAL  CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 15,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SINPROC DERECHO PETICION ALERTA  CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 16,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 3,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SINPROC DERECHO PETICION COPIAS EVALUACION CIERRE DEFINITIVO
            [
                "id" => 17,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SINPROC DERECHO PETICION GENERAL EVALUACION CIERRE DEFINITIVO
            [
                "id" => 18,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SINPROC DERECHO PETICION ALERTA  EVALUACION CIERRE DEFINITIVO
            [
                "id" => 19,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 1,
                "id_subtipo_expediente" => 3,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SINPROC QUEJA EXTERNA CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 20,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SINPROC QUEJA INTERNA CAPTURA Y REPARTO ASIGNADO A SI MISMO
            [
                "id" => 21,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 2
            ],
            // SINPROC QUEJA EXTERNA EVALUACION ASIGNACION DIRIGIDA
            [
                "id" => 22,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 3
            ],
            // SINPROC QUEJA INTERNA EVALUACION ASIGNACION DIRIGIDA
            [
                "id" => 23,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 3,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 3
            ],
            // SINPROC TUTELA HORAS CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 24,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 4,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SINPROC TUTELA DIAS CAPTURA Y REPARTO REPARTO ALEATORIO
            [
                "id" => 25,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 4,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],
            // SINPROC TUTELA HORAS EVALUACION CIERRE DEFINITIVO
            [
                "id" => 26,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 4,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // SINPROC TUTELA DIAS EVALUACION CIERRE DEFINITIVO
            [
                "id" => 27,
                "id_tipo_proceso_disciplinario" => 3,
                "id_tipo_expediente" => 4,
                "id_subtipo_expediente" => 2,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 4
            ],
            // PODER PREFERNTE A SOLICITUD EXTERNA CAPTURA Y REPARTO ASIGNADO A SI MISMO
            [
                "id" => 28,
                "id_tipo_proceso_disciplinario" => 4,
                "id_tipo_expediente" => 2,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 2
            ],
            // PODER PREFERNTE A SOLICITUD EXTERNA CAPTURA Y REPARTO  ASIGNACION DIRIGIDA
            [
                "id" => 29,
                "id_tipo_proceso_disciplinario" => 4,
                "id_tipo_expediente" => 2,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 3
            ],

            // DESGLOSE CAPTURA Y REPARTO ASIGNADO ASI MISMO
            [
                "id" => 30,
                "id_tipo_proceso_disciplinario" => 2,
                "id_tipo_expediente" => 5,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 1,
                "id_tipo_cierre_etapa" => 1
            ],

            // DESGLOSE EVALUACION ASIGNADO ASI MISMO
            [
                "id" => 31,
                "id_tipo_proceso_disciplinario" => 2,
                "id_tipo_expediente" => 5,
                "id_subtipo_expediente" => 1,
                "id_etapa" => 2,
                "id_tipo_cierre_etapa" => 3
            ],
        ];
    }
}
