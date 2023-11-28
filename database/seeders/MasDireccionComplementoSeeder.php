<?php

namespace Database\Seeders;

use App\Models\DireccionComplementoModel;
use App\Models\DireccionOrientacionModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasDireccionComplementoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_direccion_complemento')->delete();

        foreach ($this->tipoDireccionComplemento() as $item) {
            DireccionComplementoModel::create($item);
        }
    }

    public function tipoDireccionComplemento()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Apartamento",
                "estado" => true,
                "json" => '{"id": 36,"codigo": "TP-COMPA","nombre": "Apartamento","codPadre": "TP-COMP","estado": "A "}'
            ],
            [
                "id" => 2,
                "nombre" => "Bloque",
                "estado" => true,
                "json" => '{"id": 233,"codigo": "TP-COMPBLO","nombre": "Bloque","codPadre": "TP-COMP","estado": "A"}'
            ],
            [
                "id" => 3,
                "nombre" => "Casa",
                "estado" => true,
                "json" => '{"id": 105,"codigo": "TP-COMPC","nombre": "Casa ","codPadre": "TP-COMP","estado": "A "}'
            ],
            [
                "id" => 4,
                "nombre" => "Edificio",
                "estado" => true,
                "json" => '{"id": 106,"codigo": "TP-COMPE","nombre": "Edificio ","codPadre": "TP-COMP","estado": "A "}'
            ],
        ];
    }
}
