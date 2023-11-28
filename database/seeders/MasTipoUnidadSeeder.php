<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MasTipoUnidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('MAS_TIPO_UNIDAD')->delete();

        foreach ($this->mas_tipo_unidad() as $tipo) {
            DB::table('MAS_TIPO_UNIDAD')->insert(
                array(
                    'nombre' => $tipo['nombre'],
                    'codigo_unidad' => $tipo['codigo_unidad'],
                    'descripcion_unidad' => $tipo['descripcion_unidad'],
                    'id_dependencia' => $tipo['id_dependencia'],
                    'estado' => $tipo['estado'],
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                )
            );
        }
    }

    /**
     *
     */
    public function mas_tipo_unidad()
    {
        return [
            [
                "nombre" => "CÓDIGO UNIDAD ADMINISTRATIVA",
                "codigo_unidad" => 15000,
                "descripcion_unidad" => "PERSONERIA DELEGADA PARA LA COORDINACIÖN DE POTESTAD DISCIPLINARIA",
                "id_dependencia" => 413,
                "estado" => 1
            ],
            [
                "nombre" => "CÓDIGO OFICINA PRODUCTORA",
                "codigo_unidad" => null,
                "descripcion_unidad" => "P.D PARA ASUNTOS DISCIPLINARIOS II",
                "id_dependencia" => 413,
                "estado" => 1
            ],
            [
                "nombre" => "CÓDIGO SERIE DOCUMENTAL",
                "codigo_unidad" => 185,
                "descripcion_unidad" => "PROCESO DISCIPLINARIO",
                "id_dependencia" => 413,
                "estado" => 1
            ],
            [
                "nombre" => "CÓDIGO SUBSERIE DOCUMENTAL",
                "codigo_unidad" => 1,
                "descripcion_unidad" => "PROCESO DISCIPLINARIO ORDINARIO",
                "id_dependencia" => 413,
                "estado" => 1
            ],
        ];
    }
}
