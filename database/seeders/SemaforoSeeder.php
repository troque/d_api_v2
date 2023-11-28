<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\SemaforoModel;

class SemaforoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('semaforo')->delete();

        foreach ($this->semaforos() as $semaforo) {
            SemaforoModel::create($semaforo);
        }
    }
    public function semaforos()
    {
        return [
            [
                "id" => 1,
                "nombre" => "PRESCRIPCIÓN",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 7,
                "nombre_campo_fecha" => "Fecha del Auto de Apertura de Investigación Disciplinaria + 1825 días ",
                "estado" => true,
            ],
            [
                "id" => 2,
                "nombre" => "CADUCIDAD",
                "id_mas_evento_inicio" => 1,
                "id_mas_actuacion_inicia" => null,
                "nombre_campo_fecha" => "Fecha hechos + 1825 días sin Auto de Apertura de Invesgación Disciplinaria",
                "estado" => true,
            ],
            [
                "id" => 3,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA EVALUACION DE LA QUEJA",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 1,
                "nombre_campo_fecha" => "Se contará 10 hábiles días a partir de la entrega al abogado comisionado.",
                "estado" => true,
            ],
            [
                "id" => 4,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA INDAGACION PREVIA",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 1,
                "nombre_campo_fecha" => "Se contarán seis (6) meses (180 días a partir de la fecha de apertura del auto de Indagación Previa)",
                "estado" => true,
            ],
            [
                "id" => 5,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA INVESTIGACION DISCIPLINARIA",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 1,
                "nombre_campo_fecha" => "Se contra seis (6) meses (180 días a partir de la fecha de apertura del Auto de Apertura de Investigación Disciplinaria",
                "estado" => true,
            ],
            [
                "id" => 6,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA PRÓRROGA 1  DE LA INVESTIGACIÓN DISCIPLINARIA",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 35,
                "nombre_campo_fecha" => "Fecha del Auto Prórroga + el tiempo definido en al respectivo Auto el cual no puede superar 180 días.",
                "estado" => true,
            ],
            [
                "id" => 7,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA PRÓRROGA 2  DE LA INVESTIGACIÓN DISCIPLINARIA",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 35,
                "nombre_campo_fecha" => "Fecha Auto Prorroga 2 + el tiempo definido en al respectivo Auto el cual no puede superar 90 días.",
                "estado" => true,
            ],
            [
                "id" => 8,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA JUZGAMIENTO PROCESO ORDINARIO TÉRMINO PROBATORIO",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 32,
                "nombre_campo_fecha" => "Fecha del Auto de Pruebas + 90 días",
                "estado" => true,
            ],
            [
                "id" => 9,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA JUZGAMIENTO PROCESO ORDINARIO TÉRMINO PARA FALLAR",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 7,
                "nombre_campo_fecha" => "Fecha de vencimiento del término de traslado para presentar alegatos de conclusión + 30 días hábiles.",
                "estado" => true,
            ],
            [
                "id" => 10,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA JUZGAMIENTO PROCESO VERBAL TÉRMINO PROBATORIO",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 7,
                "nombre_campo_fecha" => "Fecha de audiencia que decreta pruebas + 20 días",
                "estado" => true,
            ],
            [
                "id" => 11,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA JUZGAMIENTO PROCESO VERBAL TÉRMINO PROBATORIO",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 7,
                "nombre_campo_fecha" => "Fecha del vencimiento del término probatorio en proceso verbal + 10 días",
                "estado" => true,
            ],
            [
                "id" => 12,
                "nombre" => "SEGUIMIENTO DE CONTROL ETAPA JUZGAMIENTO PROCESO VERBAL TÉRMINO PARA FALLAR",
                "id_mas_evento_inicio" => 2,
                "id_mas_actuacion_inicia" => 7,
                "nombre_campo_fecha" => "Fecha de la audiencia de presentación de alegatos de conclusión + 15 días",
                "estado" => true,
            ]
        ];
    }
}
