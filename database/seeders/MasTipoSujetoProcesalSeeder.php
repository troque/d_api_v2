<?php

namespace Database\Seeders;

use App\Models\TipoSujetoProcesalModel;
use Illuminate\Database\Seeder;

class MasTipoSujetoProcesalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoSujetoProcesal() as $proceso) {
            TipoSujetoProcesalModel::create($proceso);
        }
    }

    public function TipoSujetoProcesal()
    {
        return [
            [
                "nombre" => "Interesado",
                "estado" => true
            ],
            [
                "nombre" => "Interesado acoso laboral",
                "estado" => true
            ],
            [
                "nombre" => "Disciplinado",
                "estado" => true
            ],
            [
                "nombre" => "Apoderado",
                "estado" => true
            ],
            [
                "nombre" => "Defensor de oficio",
                "estado" => true
            ],
            [
                "nombre" => "Ministerio público",
                "estado" => true
            ],
            [
                "nombre" => "Victimas graves, Violación, Derechos humanos",
                "estado" => true
            ]
        ];
    }
}
