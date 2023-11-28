<?php

namespace Database\Seeders;

use App\Models\TipoConductaModel;
use Illuminate\Database\Seeder;

class MasTipoConductaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoConducta() as $proceso) {
            TipoConductaModel::create($proceso);
        }
    }

    public function tipoConducta()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Abandono De Cargo",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "Abuso De Autoridad",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "Acoso Laboral",
                "estado" => true
            ],
            [
                "id" => 4,
                "nombre" => "Acoso Sexual",
                "estado" => true
            ],
            [
                "id" => 5,
                "nombre" => "Adopción E Implementación Sistema De Control Interno",
                "estado" => true
            ],
            [
                "id" => 6,
                "nombre" => "Asesoramiento Ilegal",
                "estado" => true
            ],
            [
                "id" => 7,
                "nombre" => "Cese De Actividades",
                "estado" => true
            ],
            [
                "id" => 8,
                "nombre" => "Consumo De Sustancias Prohibidas",
                "estado" => true
            ],
            [
                "id" => 9,
                "nombre" => "Convenios",
                "estado" => true
            ],
            [
                "id" => 10,
                "nombre" => "Cumplimiento Horario",
                "estado" => true
            ],
            [
                "id" => 11,
                "nombre" => "Derechos Humanos",
                "estado" => true
            ],
            [
                "id" => 12,
                "nombre" => "Detrimento Patrimonial",
                "estado" => true
            ],
            [
                "id" => 13,
                "nombre" => "Doble Vinculación / Doble Asignacion Salarial",
                "estado" => true
            ],
            [
                "id" => 14,
                "nombre" => "Extralimitación De Funciones",
                "estado" => true
            ],
            [
                "id" => 15,
                "nombre" => "Hallazgo Con Incidencia Disciplinaria",
                "estado" => true
            ],
            [
                "id" => 16,
                "nombre" => "Incumplimiento De Deberes",
                "estado" => true
            ],
            [
                "id" => 17,
                "nombre" => "Incumplimiento Derechos De Peticion",
                "estado" => true
            ],
            [
                "id" => 18,
                "nombre" => "Incumplimiento Fallo Judicial",
                "estado" => true
            ],
            [
                "id" => 19,
                "nombre" => "Incursión en prohibiciones",
                "estado" => true
            ],
            [
                "id" => 20,
                "nombre" => "Información Inexacta En Hoja De Vida",
                "estado" => true
            ],
            [
                "id" => 21,
                "nombre" => "Inhabilidad, Incompatibilidad Y Conflicto De Interes",
                "estado" => true
            ],
            [
                "id" => 22,
                "nombre" => "Maltrato",
                "estado" => true
            ],
            [
                "id" => 23,
                "nombre" => "Medio Ambiente / Espacio Público",
                "estado" => true
            ],
            [
                "id" => 24,
                "nombre" => "Mora En Trámites",
                "estado" => true
            ],
            [
                "id" => 25,
                "nombre" => "Mora Sistemática En La Sustanciación",
                "estado" => true
            ],
            [
                "id" => 26,
                "nombre" => "Pagos Indebidos",
                "estado" => true
            ],
            [
                "id" => 27,
                "nombre" => "Participación En Política",
                "estado" => true
            ],
            [
                "id" => 28,
                "nombre" => "Pérdida de bienes",
                "estado" => true
            ],
            [
                "id" => 29,
                "nombre" => "Provecho Patrimonial Indebido",
                "estado" => true
            ],
            [
                "id" => 30,
                "nombre" => "Publicaciones Periódicas De Información Y Gestión",
                "estado" => true
            ],
            [
                "id" => 31,
                "nombre" => "Quejas Por Atencion Al Público",
                "estado" => true
            ],
            [
                "id" => 32,
                "nombre" => "Querella Policiva",
                "estado" => true
            ],
            [
                "id" => 33,
                "nombre" => "Realizar Una Conducta Consagrada Como Delito",
                "estado" => true
            ],
            [
                "id" => 34,
                "nombre" => "Régimen O Sistema Nacional De Contabilidad Pública",
                "estado" => true
            ],
            [
                "id" => 35,
                "nombre" => "Régimen Penitenciario",
                "estado" => true
            ],
            [
                "id" => 36,
                "nombre" => "Regimen Presupuestal",
                "estado" => true
            ],
            [
                "id" => 37,
                "nombre" => "Servicio Educacion",
                "estado" => true
            ],
            [
                "id" => 38,
                "nombre" => "Servicio Salud",
                "estado" => true
            ],
            [
                "id" => 39,
                "nombre" => "Servicios Públicos",
                "estado" => true
            ],
            [
                "id" => 40,
                "nombre" => "Supervisión Contratos",
                "estado" => true
            ],
            [
                "id" => 41,
                "nombre" => "Suspensión O Perturbación Del Servicio",
                "estado" => true
            ],
            [
                "id" => 42,
                "nombre" => "Tráfico De Influencias",
                "estado" => true
            ],
            [
                "id" => 43,
                "nombre" => "Trámite Y Resolución De Quejas",
                "estado" => true
            ],
            [
                "id" => 44,
                "nombre" => "Uso Indebido De Bienes Públicos",
                "estado" => true
            ],

            [
                "id" => 45,
                "nombre" => "Violación Al Debido Proceso",
                "estado" => true
            ],
            [
                "id" => 46,
                "nombre" => "Violación Al Regimen De Contratacion",
                "estado" => true
            ],
            [
                "id" => 47,
                "nombre" => "Violación De Reserva Legal",
                "estado" => true
            ],
            [
                "id" => 45,
                "nombre" => "Violacion Ley Carrera Administrativa",
                "estado" => true
            ],
            [
                "nombre" => "Violacion Normas De Tránsito",
                "estado" => true
            ],
            [
                "nombre" => "Sin Clasificar",
                "estado" => true
            ],

        ];
    }
}
