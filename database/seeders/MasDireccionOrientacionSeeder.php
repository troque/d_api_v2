<?php

namespace Database\Seeders;

use App\Models\DireccionOrientacionModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasDireccionOrientacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_direccion_orientacion')->delete();

        foreach ($this->tipoDireccionOrientacion() as $item) {
            DireccionOrientacionModel::create($item);
        }
    }

    public function tipoDireccionOrientacion()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Sur",
                "estado" => true,
                'JSON' => '{"id": 86,"codigo": "ORIEN-S","nombre": "Sur","codPadre": "ORIEN","estado": "A "}'
            ],
            [
                "id" => 2,
                "nombre" => "Norte",
                "estado" => true,
                'JSON' => '{"id": 85,"codigo": "ORIEN-N","nombre": "Norte","codPadre": "ORIEN","estado": "A "}'
            ],
            [
                "id" => 3,
                "nombre" => "Este",
                "estado" => true,
                'JSON' => '{"id": 231,"codigo": "ORIENEST","nombre": "Este","codPadre": "ORIEN","estado": "A"}'
            ],
            [
                "id" => 4,
                "nombre" => "Oeste",
                "estado" => true,
                'JSON' => '{"id": 234,"codigo": "ORIENTOR","nombre": "Torre","codPadre": "ORIEN","estado": "A"}'
            ],
        ];
    }
}
