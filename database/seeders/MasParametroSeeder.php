<?php

namespace Database\Seeders;

use App\Models\ParametroModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasParametroSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_parametro')->delete();
        
        foreach ($this->parametro() as $proceso) {
            ParametroModel::create($proceso) ;
        }
    }

    public function Parametro()
    {
        return [
            [
                "nombre" => "limite_años_calendario",
                "modulo" => "CapturaYReparto",
                "valor" => "3",
                "estado" => true,
            ],
            [
                "nombre" => "minimo_caracteres_textarea",
                "modulo" => "CapturaYReparto",
                "valor" => "10",
                "estado" => true,
            ],
            [
                "nombre" => "maximo_caracteres_textarea",
                "modulo" => "CapturaYReparto",
                "valor" => "4000",
                "estado" => true,
            ],
            [
                "nombre" => "id_dependencia_que_atiende_solicitud_de_anulacion",
                "modulo" => "Actuaciones",
                "valor" => "413",
                "estado" => true,
            ],
            [
                "nombre" => "id_dependencia_secretaria_comun",
                "modulo" => "Transacciones",
                "valor" => "413",
                "estado" => true,
            ]
        ];
    }
}
