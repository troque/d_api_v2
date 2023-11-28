<?php

namespace Database\Seeders;

use App\Models\FaseModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class EvaluacionFaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('evaluacion_fase')->delete();

        foreach ($this->EvaluacionFase() as $fase) {
            DB::table('evaluacion_fase')->insert(
                array(
                    'id_fase_actual' => $fase['id_fase_actual'],
                    'id_fase_antecesora' => $fase['id_fase_antecesora'],
                    'id_resultado_evaluacion' => $fase['id_resultado_evaluacion'],
                    'id_tipo_expediente' => $fase['id_tipo_expediente'],
                    'id_sub_tipo_expediente' => $fase['id_sub_tipo_expediente'],
                    'orden' => $fase['orden'],
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                )
            );
        }
    }

    public function EvaluacionFase()
    {
        return [
            // QUEJA EXTERNA COMISORIO EJE
            [
                "id" => 1,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 2,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 3,
                "id_fase_actual" => 6,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 4,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 6,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 5,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 6,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],
            [
                "id" => 7,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 7
            ],

            // QUEJA EXTERNA DEVOLUCION ENTIDAD
            [
                "id" => 8,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 9,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 10,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 11,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 12,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],

            // QUEJA EXTERNA INCORPORACION
            [
                "id" => 13,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 14,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 15,
                "id_fase_actual" => 6,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 16,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 6,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 17,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 18,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],
            [
                "id" => 19,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 7
            ],

            // QUEJA EXTERNA REMISORIO EXTERNO
            [
                "id" => 20,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 21,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 22,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 23,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 24,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 25,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],

            // QUEJA EXTERNA REMISORIO INTERNO
            [
                "id" => 26,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 27,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 28,
                "id_fase_actual" => 6,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 29,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 6,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 30,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 31,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],
            [
                "id" => 32,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 1,
                "orden" => 7
            ],

            // TUTELA DIAS
            [
                "id" => 33,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 34,
                "id_fase_actual" => 17,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 35,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 17,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 36,
                "id_fase_actual" => 18,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 37,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 18,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],

            // TUTELA HORAS
            [
                "id" => 38,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],
            [
                "id" => 39,
                "id_fase_actual" => 17,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],
            [
                "id" => 40,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 17,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],
            [
                "id" => 41,
                "id_fase_actual" => 18,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 2,
                "orden" => 4
            ],
            [
                "id" => 42,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 18,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 4,
                "id_sub_tipo_expediente" => 2,
                "orden" => 5
            ],

            // DERECHO DE PETICION COPIAS
            [
                "id" => 43,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 44,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 45,
                "id_fase_actual" => 18,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 46,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 18,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],

            // DERECHO DE PETICION GENERAL
            [
                "id" => 47,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],
            [
                "id" => 48,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],
            [
                "id" => 49,
                "id_fase_actual" => 18,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],
            [
                "id" => 50,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 18,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 2,
                "orden" => 4
            ],

            // DERECHO DE PETICION ALERTA
            [
                "id" => 51,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 3,
                "orden" => 1
            ],
            [
                "id" => 52,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 3,
                "orden" => 2
            ],
            [
                "id" => 53,
                "id_fase_actual" => 18,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 3,
                "orden" => 3
            ],
            [
                "id" => 54,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 18,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 1,
                "id_sub_tipo_expediente" => 3,
                "orden" => 4
            ],

            // QUEJA INTERNA COMISORIO EJE
            [
                "id" => 55,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],
            [
                "id" => 56,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],
            [
                "id" => 57,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // QUEJA INTERNA DEVOLUCION ENTIDAD
            [
                "id" => 58,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],
            [
                "id" => 59,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],
            [
                "id" => 60,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // QUEJA INTERNA INCORPORACION
            [
                "id" => 61,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],
            [
                "id" => 62,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],
            [
                "id" => 63,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // QUEJA INTERNA REMISORIO EXTERNO
            [
                "id" => 64,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],
            [
                "id" => 65,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],
            [
                "id" => 66,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // QUEJA INTERNA REMISORIO INTERNO
            [
                "id" => 67,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],
            [
                "id" => 68,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],
            [
                "id" => 69,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 3,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // PODER PREFERENTE EXTERNA
            [
                "id" => 70,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 2,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 71,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 2,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 72,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 6,
                "id_tipo_expediente" => 2,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],

            // PROCESO DISCIPLINARIO COMISORIO EJE QUEJA EXTERNA
            [
                "id" => 73,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 74,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 75,
                "id_fase_actual" => 6,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 76,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 6,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 77,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 78,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],
            [
                "id" => 79,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 7
            ],

            // PROCESO DISCIPLINARIO DEVOLUCION ENTIDAD  QUEJA EXTERNA
            [
                "id" => 80,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 81,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 82,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 83,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 84,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],

            //  PROCESO DISCIPLINARIO INCORPORACION  QUEJA EXTERNA
            [
                "id" => 85,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 86,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 87,
                "id_fase_actual" => 6,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 88,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 6,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 89,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 90,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],
            [
                "id" => 91,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 7
            ],

            // PROCESO DISCIPLINARIO REMISORIO EXTERNO  QUEJA EXTERNA
            [
                "id" => 92,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 93,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 94,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 95,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 96,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 97,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],

            // PROCESO DISCIPLINARIO COMISORIO EJE QUEJA INTERNA
            [
                "id" => 98,
                "id_fase_actual" => 10,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 1
            ],
            [
                "id" => 99,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 10,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 2
            ],
            [
                "id" => 100,
                "id_fase_actual" => 6,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 3
            ],
            [
                "id" => 101,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 6,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 4
            ],
            [
                "id" => 102,
                "id_fase_actual" => 12,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 5
            ],
            [
                "id" => 103,
                "id_fase_actual" => 8,
                "id_fase_antecesora" => 12,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 6
            ],
            [
                "id" => 104,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 8,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 1,
                "orden" => 7
            ],

            // PROCESO DISCIPLINARIO COMISORIO EJE QUEJA INTERNA
            [
                "id" => 105,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],

            [
                "id" => 106,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],

            [
                "id" => 107,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 1,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // PROCESO DISCIPLINARIO DEVOLUCION ENTIDAD QUEJA INTERNA
            [
                "id" => 108,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],

            [
                "id" => 109,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],

            [
                "id" => 107,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 2,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // PROCESO DISCIPLINARIO INCORPORACION QUEJA INTERNA
            [
                "id" => 111,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],

            [
                "id" => 112,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],

            [
                "id" => 113,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 3,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // PROCESO DISCIPLINARIO  REMISORIO EXTERNO QUEJA INTERNA
            [
                "id" => 114,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],

            [
                "id" => 115,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],

            [
                "id" => 116,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 4,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

            // PROCESO DISCIPLINARIO  REMISORIO EXTERNO QUEJA INTERNA
            [
                "id" => 117,
                "id_fase_actual" => 11,
                "id_fase_antecesora" => 14,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 1
            ],

            [
                "id" => 118,
                "id_fase_actual" => 9,
                "id_fase_antecesora" => 11,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 2
            ],

            [
                "id" => 119,
                "id_fase_actual" => 15,
                "id_fase_antecesora" => 9,
                "id_resultado_evaluacion" => 5,
                "id_tipo_expediente" => 5,
                "id_sub_tipo_expediente" => 2,
                "orden" => 3
            ],

        ];
    }
}
