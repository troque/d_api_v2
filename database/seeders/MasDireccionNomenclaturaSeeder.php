<?php

namespace Database\Seeders;

use App\Models\DireccionNomenclaturaModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasDireccionNomenclaturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_direccion_nomenclatura')->delete();

        foreach ($this->tipoDireccionNomenclatura() as $item) {
            DireccionNomenclaturaModel::create($item);
        }
    }

    public function tipoDireccionNomenclatura()
    {
        return [
            // [
            //     "id" => 1,
            //     "nombre" => "Apartamento",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => '{"id": 36,"codigo": "TP-COMPA","nombre": "Apartamento","codPadre": "TP-COMP","estado": "A "}'
            // ],
            [
                "id" => 2,
                "nombre" => "Avenida",
                "estado" => true,
                "tipo_via" => true,
                "json" => '{"id": 6,"codigo": "TP-VIAAV","nombre": "Avenida","codPadre": "TP-VIA","estado": "A "}'
            ],
            // [
            //     "id" => 3,
            //     "nombre" => "Autopista",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => ''
            // ],
            // [
            //     "id" => 4,
            //     "nombre" => "Barrio",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => ''
            // ],
            [
                "id" => 5,
                "nombre" => "Calle",
                "estado" => true,
                "tipo_via" => true,
                "json" => '{"id": 3,"codigo": "TP-VIACR","nombre": "Calle","codPadre": "TP-VIA","estado": "A "}'
            ],
            [
                "id" => 6,
                "nombre" => "Carrera",
                "estado" => true,
                "tipo_via" => true,
                "json" => '{"id": 2,"codigo": "TP-VIACL","nombre": "Carrera","codPadre": "TP-VIA","estado": "A "}'
            ],
            [
                "id" => 7,
                "nombre" => "Diagonal",
                "estado" => true,
                "tipo_via" => true,
                "json" => '{"id": 4,"codigo": "TP-VIADI","nombre": "Diagonal","codPadre": "TP-VIA","estado": "A "}'
            ],
            // [
            //     "id" => 8,
            //     "nombre" => "Edificio",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => '{"id": 106,"codigo": "TP-COMPE","nombre": "Edificio ","codPadre": "TP-COMP","estado": "A "}'
            // ],
            // [
            //     "id" => 9,
            //     "nombre" => "Norte",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => ''
            // ],
            // [
            //     "id" => 10,
            //     "nombre" => "Sur",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => ''
            // ],
            [
                "id" => 11,
                "nombre" => "Transversal",
                "estado" => true,
                "tipo_via" => true,
                "json" => '{"id": 5,"codigo": "TP-VIATR","nombre": "Transversal","codPadre": "TP-VIA","estado": "A "}'
            ],
            // [
            //     "id" => 12,
            //     "nombre" => "Casa",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => '{"id": 105,"codigo": "TP-COMPC","nombre": "Casa ","codPadre": "TP-COMP","estado": "A "}'
            // ],
            // [
            //     "id" => 13,
            //     "nombre" => "Torre",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => '{"id": 107,"codigo": "TP-COMPT","nombre": "Torre","codPadre": "TP-COMP","estado": "A "}'
            // ],
            // [
            //     "id" => 14,
            //     "nombre" => "Interior",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => '{"id": 232,"codigo": "TP-COMPINT","nombre": "Interior","codPadre": "TP-COMP","estado": "A"}'
            // ],
            // [
            //     "id" => 15,
            //     "nombre" => "Bloque",
            //     "estado" => true,
            //     "tipo_via" => false,
            //     "json" => '{"id": 233,"codigo": "TP-COMPBLO","nombre": "Bloque","codPadre": "TP-COMP","estado": "A"}'
            // ],
        ];
    }
}
