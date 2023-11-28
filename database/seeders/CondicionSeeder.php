<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\CondicionModel;

class CondicionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('condicion')->delete();

        foreach ($this->condicion() as $condicion) {
            CondicionModel::create($condicion);
        }
    }
    public function condicion()
    {
        return [
            // UNO
            [
                "inicial" => 0,
                "final" => 1095,
                "color" => "Verde",
                "id_semaforo" => 1,
                "estado" => true,
            ],
            [
                "inicial" => 1096,
                "final" => 1460,
                "color" => "Amarillo",
                "id_semaforo" => 1,
                "estado" => true,
            ],
            [
                "inicial" => 1461,
                "final" => 1643,
                "color" => "Rojo",
                "id_semaforo" => 1,
                "estado" => true,
            ],

            // DOS
            [
                "inicial" => 0,
                "final" => 1095,
                "color" => "Verde",
                "id_semaforo" => 2,
                "estado" => true,
            ],
            [
                "inicial" => 1096,
                "final" => 1460,
                "color" => "Amarillo",
                "id_semaforo" => 2,
                "estado" => true,
            ],
            [
                "inicial" => 1461,
                "final" => 1643,
                "color" => "Rojo",
                "id_semaforo" => 2,
                "estado" => true,
            ],

            // TRES
            [
                "inicial" => 0,
                "final" => 5,
                "color" => "Verde",
                "id_semaforo" => 3,
                "estado" => true,
            ],
            [
                "inicial" => 6,
                "final" => 10,
                "color" => "Amarillo",
                "id_semaforo" => 3,
                "estado" => true,
            ],
            [
                "inicial" => 11,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 3,
                "estado" => true,
            ],

            // CUATRO
            [
                "inicial" => 0,
                "final" => 170,
                "color" => "Verde",
                "id_semaforo" => 4,
                "estado" => true,
            ],
            [
                "inicial" => 171,
                "final" => 180,
                "color" => "Amarillo",
                "id_semaforo" => 4,
                "estado" => true,
            ],
            [
                "inicial" => 181,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 4,
                "estado" => true,
            ],

            //CINCO
            [
                "inicial" => 0,
                "final" => 170,
                "color" => "Verde",
                "id_semaforo" => 5,
                "estado" => true,
            ],
            [
                "inicial" => 171,
                "final" => 180,
                "color" => "Amarillo",
                "id_semaforo" => 5,
                "estado" => true,
            ],
            [
                "inicial" => 181,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 5,
                "estado" => true,
            ],

            //SEIS
            [
                "inicial" => 0,
                "final" => 170,
                "color" => "Verde",
                "id_semaforo" => 6,
                "estado" => true,
            ],
            [
                "inicial" => 171,
                "final" => 180,
                "color" => "Amarillo",
                "id_semaforo" => 6,
                "estado" => true,
            ],
            [
                "inicial" => 181,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 6,
                "estado" => true,
            ],

            //Siete
            [
                "inicial" => 0,
                "final" => 80,
                "color" => "Verde",
                "id_semaforo" => 7,
                "estado" => true,
            ],
            [
                "inicial" => 81,
                "final" => 90,
                "color" => "Amarillo",
                "id_semaforo" => 7,
                "estado" => true,
            ],
            [
                "inicial" => 91,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 7,
                "estado" => true,
            ],

            //Ocho
            [
                "inicial" => 0,
                "final" => 80,
                "color" => "Verde",
                "id_semaforo" => 8,
                "estado" => true,
            ],
            [
                "inicial" => 81,
                "final" => 90,
                "color" => "Amarillo",
                "id_semaforo" => 8,
                "estado" => true,
            ],
            [
                "inicial" => 91,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 8,
                "estado" => true,
            ],

            //Nueve
            [
                "inicial" => 0,
                "final" => 25,
                "color" => "Verde",
                "id_semaforo" => 9,
                "estado" => true,
            ],
            [
                "inicial" => 26,
                "final" => 30,
                "color" => "Amarillo",
                "id_semaforo" => 9,
                "estado" => true,
            ],
            [
                "inicial" => 31,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 9,
                "estado" => true,
            ],

            //Diez
            [
                "inicial" => 0,
                "final" => 25,
                "color" => "Verde",
                "id_semaforo" => 10,
                "estado" => true,
            ],
            [
                "inicial" => 26,
                "final" => 30,
                "color" => "Amarillo",
                "id_semaforo" => 10,
                "estado" => true,
            ],
            [
                "inicial" => 31,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 10,
                "estado" => true,
            ],

            //Once
            [
                "inicial" => 0,
                "final" => 8,
                "color" => "Verde",
                "id_semaforo" => 11,
                "estado" => true,
            ],
            [
                "inicial" => 9,
                "final" => 10,
                "color" => "Amarillo",
                "id_semaforo" => 11,
                "estado" => true,
            ],
            [
                "inicial" => 11,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 11,
                "estado" => true,
            ],

            //Doce
            [
                "inicial" => 0,
                "final" => 10,
                "color" => "Verde",
                "id_semaforo" => 12,
                "estado" => true,
            ],
            [
                "inicial" => 11,
                "final" => 15,
                "color" => "Amarillo",
                "id_semaforo" => 12,
                "estado" => true,
            ],
            [
                "inicial" => 16,
                "final" => null,
                "color" => "Rojo",
                "id_semaforo" => 12,
                "estado" => true,
            ],
        ];
    }
}
