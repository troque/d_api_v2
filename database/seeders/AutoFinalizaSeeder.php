<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\AutoFinalizaModel;

class AutoFinalizaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('auto_finaliza')->delete();

        foreach ($this->auto() as $auto) {
            AutoFinalizaModel::create($auto);
        }
    }
    public function auto()
    {
        return [
            // UNO
            [
                "id_semaforo" => 1,
                "id_mas_actuacion" => 179,
                "estado" => true,
            ],
            [
                "id_semaforo" => 1,
                "id_mas_actuacion" => 180,
                "estado" => true,
            ],
            [
                "id_semaforo" => 1,
                "id_mas_actuacion" => 167,
                "estado" => true,
            ],
            [
                "id_semaforo" => 1,
                "id_mas_actuacion" => 132,
                "estado" => true,
            ],
            [
                "id_semaforo" => 1,
                "id_mas_actuacion" => 133,
                "estado" => true,
            ],

            // DOS
            [
                "id_semaforo" => 2,
                "id_mas_actuacion" => 7,
                "estado" => true,
            ],
            [
                "id_semaforo" => 2,
                "id_mas_actuacion" => 31,
                "estado" => true,
            ],

            // TRES
            // El excel no tiene nada aqui

            // CUATRO
            // NOSE que poner aqui

            //CINCO
            // NOSE que poner aqui

            //SEIS
            // NOSE que poner aqui

            //Siete
            [
                "id_semaforo" => 7,
                "id_mas_actuacion" => 131,
                "estado" => true,
            ],

            //Ocho
            [
                "id_semaforo" => 8,
                "id_mas_actuacion" => 179,
                "estado" => true,
            ],
            [
                "id_semaforo" => 8,
                "id_mas_actuacion" => 180,
                "estado" => true,
            ],
            [
                "id_semaforo" => 8,
                "id_mas_actuacion" => 167,
                "estado" => true,
            ],
            [
                "id_semaforo" => 8,
                "id_mas_actuacion" => 132,
                "estado" => true,
            ],
            [
                "id_semaforo" => 8,
                "id_mas_actuacion" => 133,
                "estado" => true,
            ],

            //Nueve
            [
                "id_semaforo" => 9,
                "id_mas_actuacion" => 179,
                "estado" => true,
            ],
            [
                "id_semaforo" => 9,
                "id_mas_actuacion" => 180,
                "estado" => true,
            ],
            [
                "id_semaforo" => 9,
                "id_mas_actuacion" => 167,
                "estado" => true,
            ],
            [
                "id_semaforo" => 9,
                "id_mas_actuacion" => 132,
                "estado" => true,
            ],
            [
                "id_semaforo" => 9,
                "id_mas_actuacion" => 133,
                "estado" => true,
            ],

            //Diez
            [
                "id_semaforo" => 10,
                "id_mas_actuacion" => 131,
                "estado" => true,
            ],

            //Once
            [
                "id_semaforo" => 11,
                "id_mas_actuacion" => 131,
                "estado" => true,
            ],

            //Doce
            [
                "id_semaforo" => 12,
                "id_mas_actuacion" => 179,
                "estado" => true,
            ],
            [
                "id_semaforo" => 12,
                "id_mas_actuacion" => 180,
                "estado" => true,
            ],
            [
                "id_semaforo" => 12,
                "id_mas_actuacion" => 167,
                "estado" => true,
            ],
            [
                "id_semaforo" => 12,
                "id_mas_actuacion" => 132,
                "estado" => true,
            ],
            [
                "id_semaforo" => 12,
                "id_mas_actuacion" => 133,
                "estado" => true,
            ],
        ];
    }
}
