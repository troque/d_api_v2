<?php

namespace Database\Seeders;

use App\Models\TerminoRespuestaModel;
use Illuminate\Database\Seeder;

class MasTerminoRespuestaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoTerminoRespuesta() as $tipo_termino_respuesta) {
            TerminoRespuestaModel::create($tipo_termino_respuesta);
        }
    }

    public function tipoTerminoRespuesta()
    {
        return [
            [
                "id" => 1,
                "nombre" => "días"
            ],
            [
                "id" => 2,
                "nombre" => "horas",
            ],
        ];
    }
}
