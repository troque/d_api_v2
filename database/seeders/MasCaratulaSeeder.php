<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MasCaratulasModel;

class MasCaratulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->caratulas() as $proceso) {
            MasCaratulasModel::create($proceso);
        }
    }

    public function caratulas()
    {
        return [
            [
                "nombre" => "Carátula N°1'",
                "nombre_plantilla" => "01-FR-01 Plantilla Caratula.docx",
                "estado" => true,
                "created_user" => "ForsecurityDiscUno"
            ],
        ];
    }
}