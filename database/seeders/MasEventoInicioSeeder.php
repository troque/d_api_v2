<?php

namespace Database\Seeders;

use App\Models\MasEventoInicioModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasEventoInicioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_evento_inicio')->delete();
        foreach ($this->MasEventoInicio() as $proceso) {
            MasEventoInicioModel::create($proceso) ;
        }
    }

    public function MasEventoInicio()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Fecha de registro del primer antecedente",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "Aprobación de una actuación",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "Fecha seleccionada al aprobar actuación",
                "estado" => true
            ],
            [
                "id" => 4,
                "nombre" => "Asignación a grupo de trabajo secretaría común",
                "estado" => true
            ],
            [
                "id" => 5,
                "nombre" => "Asignación a dependencia",
                "estado" => true
            ],
            [
                "id" => 6,
                "nombre" => "Fecha e interesado seleccionados después de aprobar actuación",
                "estado" => true
            ],
        ];
    }
}
