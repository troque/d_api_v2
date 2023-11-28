<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusquedaExpedienteModel;
class MasBusquedaExpedienteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->busquedaExpediente() as $proceso) {
            BusquedaExpedienteModel::create($proceso) ;
        }
    }

    public function busquedaExpediente()
    {
        return [
            [
                "nombre" => "No. expediente",
                "estado" => true
            ],
            [
                "nombre" => "Vigencia",
                "estado" => true
            ],
            [
                "nombre" => "Estado del expediente",
                "estado" => true
            ],
            [
                "nombre" => "Ubicación del expediente",
                "estado" => true
            ],
            [
                "nombre" => "Nombre disciplinado",
                "estado" => true
            ],
            [
                "nombre" => "Identificación disciplinado",
                "estado" => true
            ],
            [
                "nombre" => "Asunto del expediente",
                "estado" => true
            ],
            [
                "nombre" => "Sector",
                "estado" => true
            ],
            [
                "nombre" => "Nombre entidad",
                "estado" => true
            ],
            [
                "nombre" => "Nombre quejoso",
                "estado" => true
            ],
            [
                "nombre" => "Identificación quejoso",
                "estado" => true
            ],
            [
                "nombre" => "Tipo quejoso",
                "estado" => true
            ],
            [
                "nombre" => "Etapa del expediente",
                "estado" => true
            ],
            [
                "nombre" => "Delegada",
                "estado" => true
            ],

        ];
    }
}
