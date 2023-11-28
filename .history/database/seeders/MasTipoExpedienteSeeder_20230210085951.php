<?php

namespace Database\Seeders;

use App\Models\TipoExpedienteModel;
use Illuminate\Database\Seeder;

class MasTipoExpedienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoExpediente() as $tipo_expediente) {
            TipoExpedienteModel::create($tipo_expediente);
        }
    }

    public function tipoExpediente()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Derecho de petición",
                "termino" => "Termino",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "Poder preferente a solicitud",
                "termino" => "Termino",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "Queja",
                "termino" => "Termino",
                "estado" => true
            ],
            [
                "id" => 4,
                "nombre" => "Tutela",
                "termino" => "Termino",
                "estado" => true
            ],

            [
                "id" => 5,
                "nombre" => "Proceso disciplinario",
                "termino" => "Termino",
                "estado" => false
            ],
        ];
    }
}
