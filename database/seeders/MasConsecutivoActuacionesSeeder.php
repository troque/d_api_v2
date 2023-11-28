<?php

namespace Database\Seeders;

use App\Models\MasConsecutivoActuacionesModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasConsecutivoActuacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_consecutivo_actuaciones')->delete();
        foreach ($this->consecutivos() as $consecutivo) {
            MasConsecutivoActuacionesModel::create($consecutivo);
        }
    }

    public function consecutivos()
    {
        return [
            [
                "id_vigencia" => 1,
                "consecutivo" => 0,
                "id_actuacion" => 1,
                "estado" => true,
            ],
            [
                "id_vigencia" => 2,
                "consecutivo" => 0,
                "id_actuacion" => 1,
                "estado" => true,
            ],
            [
                "id_vigencia" => 3,
                "consecutivo" => 0,
                "id_actuacion" => 1,
                "estado" => true,
            ],
            [
                "id_vigencia" => 4,
                "consecutivo" => 0,
                "id_actuacion" => 1,
                "estado" => true,
            ],
            [
                "id_vigencia" => 5,
                "consecutivo" => 0,
                "id_actuacion" => 1,
                "estado" => true,
            ], 
        ];
    }
}
