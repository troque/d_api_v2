<?php

namespace Database\Seeders;

use App\Models\EstadoActuacionesModel;
use Illuminate\Database\Seeder;

class MasEstadoActuacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->MasEstadoActuaciones() as $proceso) {
            EstadoActuacionesModel::create($proceso);
        }
    }

    public function MasEstadoActuaciones()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Aprobada",
                "codigo" => "APR",
                "descripcion" => "Actuación en estado aprobada"
            ],
            [
                "id" => 2,
                "nombre" => "Rechazada",
                "codigo" => "RECH",
                "descripcion" => "Actuación en estado rechazada"
            ],
            [
                "id" => 3,
                "nombre" => "Pendiente aprobación",
                "codigo" => "PENAPR",
                "descripcion" => "Actuación en estado pendiente de aprobación"
            ],
            [
                "id" => 4,
                "nombre" => "Solicitud inactivación",
                "codigo" => "SOLANUL",
                "descripcion" => "Actuación en estado de solicitud de inactivación"
            ],
            [
                "id" => 5,
                "nombre" => "Aprobada y pdf definitivo",
                "codigo" => "PDFDEF",
                "descripcion" => "Actuación con estado de aprobada y carga del pdf definitivo"
            ],
            [
                "id" => 6,
                "nombre" => "Actualización del Documento",
                "codigo" => "ACTDOC",
                "descripcion" => "Actuación en estado de actualización del documento"
            ],
            [
                "id" => 7,
                "nombre" => "Solicitud inactivación aceptada",
                "codigo" => "ACTSOLANUL",
                "descripcion" => "Solicitud de inactivación aceptada de actuación"
            ],
            [
                "id" => 8,
                "nombre" => "Solicitud inactivación rechazada",
                "codigo" => "RECHSOLANUL",
                "descripcion" => "Rechazada de inactivación aceptada de actuación"
            ],
            [
                "id" => 9,
                "nombre" => "Documento firmado",
                "codigo" => "DOCFIR",
                "descripcion" => "Documento firmado con éxito"
            ],
            // [
            //     "nombre" => "Remitida",
            //     "codigo" => "RTDA",
            //     "descripcion" => "Documento firmado con éxito"
            // ],
            [
                "id" => 10,
                "nombre" => "Actuación anulada",
                "codigo" => "ACTINACT",
                "descripcion" => "Actuación en estado anulada"
            ],
            [
                "id" => 11,
                "nombre" => "Cambio de etapa",
                "codigo" => "CAMETP",
                "descripcion" => "Cambio de etapa', 'CAMETP', 'Cambio de etapa donde el proceso seguira cuando sea aprobado"
            ],
            [
                "id" => 12,
                "nombre" => "Cambio de lista de actuaciones a inactivar",
                "codigo" => "CDLDAAI",
                "descripcion" => "Cambió de la lista de actuaciones que se inactivaran cuando el proceso se apruebe"
            ],
            [
                "id" => 13,
                "nombre" => "Solicitud activación",
                "codigo" => "SOLACTI",
                "descripcion" => "Solicitud de activación a actuación"
            ],
            [
                "id" => 14,
                "nombre" => "Solicitud activación aceptada",
                "codigo" => "ACTSOLACTI",
                "descripcion" => "Solicitud de activación aceptada de actuación"
            ],
            [
                "id" => 15,
                "nombre" => "Solicitud activación rechazada",
                "codigo" => "RECHSOLACTI",
                "descripcion" => "Rechazada de activación de actuación"
            ],
            [
                "id" => 16,
                "nombre" => "Cambio Fecha Registro",
                "codigo" => "CAMFR",
                "descripcion" => "Cambio de la fecha de registro seleccionado por el usuario"
            ]
        ];
    }
}
