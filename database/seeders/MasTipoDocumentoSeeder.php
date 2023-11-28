<?php

namespace Database\Seeders;

use App\Models\TipoDocumentoModel;
use Illuminate\Database\Seeder;

class MasTipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoDocumento() as $proceso) {
            TipoDocumentoModel::create($proceso);
        }
    }

    public function TipoDocumento()
    {
        return [
            [
                "nombre" => "Cédula de Ciudadanía",
                "estado" => true
            ],
            [
                "nombre" => "Cédula de Extranjería",
                "estado" => true
            ],
            [
                "nombre" => "Pasaporte",
                "estado" => true
            ],

            [
                "nombre" => "No informa",
                "estado" => true
            ],
        ];
    }
}
