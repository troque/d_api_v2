<?php

namespace Database\Seeders;

use App\Models\FaseModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MasFaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_fase')->delete();
        DB::statement("alter sequence MAS_FASE_ID_SEQ restart start with 1");

        foreach ($this->MasFase() as $fase) {
            $idEtapa = DB::table('mas_etapa')->where('nombre', '=', $fase['etapa'])->value('id');
            DB::table('mas_fase')->insert(
                array(
                    'nombre' => $fase['nombre'],
                    'estado' => $fase['estado'],
                    'id_etapa' => $idEtapa,
                    'orden' => $fase['orden'],
                    'link_consulta_migracion' => $fase['link_consulta_migracion'],
                    'migrar' => $fase['migrar'],
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                )
            );
        }
    }

    public function MasFase()
    {
        return [
            [
                "nombre" => "Antecedentes",
                "estado" => true,
                "etapa" => "Captura y Reparto",
                "orden" => 1,
                "link_consulta_migracion" => "/ListarMigracionAntecedentes",
                "migrar" => true
            ],
            [
                "nombre" => "Datos del interesado",
                "estado" => true,
                "etapa" => "Captura y Reparto",
                "orden" => 2,
                "link_consulta_migracion" => "/ListarMigracionInteresados",
                "migrar" => true
            ],
            [
                "nombre" => "Clasificación del radicado",
                "estado" => true,
                "etapa" => "Captura y Reparto",
                "orden" => 3,
                "link_consulta_migracion" => "/ListarMigracionClasificacionRadicado",
                "migrar" => true
            ],
            [
                "nombre" => "Entidad del investigado",
                "estado" => true,
                "etapa" => "Captura y Reparto",
                "orden" => 4,
                "link_consulta_migracion" => "/ListarMigracionEntidades",
                "migrar" => true
            ],
            [
                "nombre" => "Soporte del radicado",
                "estado" => true,
                "etapa" => "Captura y Reparto",
                "orden" => 5,
                "link_consulta_migracion" => "",
                "migrar" => true
            ],
            [
                "nombre" => "Remisión queja",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 8,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Inicio proceso disciplinario",
                "estado" => true,
                "etapa" => "Inicio Proceso Disciplinario",
                "orden" => 0,
                "link_consulta_migracion" => "/CargarMigracionProcesoDisciplinario",
                "migrar" => true
            ],
            [
                "nombre" => "Documento cierre",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 13,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Gestor respuesta",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 10,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],

            [
                "nombre" => "Validación de la clasificación",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 6,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Evaluación",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 7,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Comunicación del interesado",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 12,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Cierre total",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 0,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],

            [
                "nombre" => "Cierre captura y reparto",
                "estado" => true,
                "etapa" => "Captura y Reparto",
                "orden" => 0,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],

            [
                "nombre" => "Cierre evaluación",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 0,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Actuaciones",
                "estado" => true,
                "etapa" => "Evaluación en PD",
                "orden" => 15,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Requerimiento Juzgado",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 9,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
            [
                "nombre" => "Información Cierre",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 11,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],

            [
                "nombre" => "Registro Seguimiento",
                "estado" => true,
                "etapa" => "Evaluación",
                "orden" => 14,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],

            [
                "nombre" => "Iniciar Proceso",
                "estado" => true,
                "etapa" => "Evaluación en PD",
                "orden" => 0,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],

            [
                "nombre" => "Cierre proceso",
                "estado" => true,
                "etapa" => "Evaluación en PD",
                "orden" => 0,
                "link_consulta_migracion" => "",
                "migrar" => false
            ],
        ];
    }
}
