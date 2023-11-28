<?php

namespace Database\Seeders;

use App\Models\DireccionBisModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasDireccionBisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_direccion_bis')->delete();

        foreach ($this->tipoDireccionBis() as $item) {
            DireccionBisModel::create($item);
        }
    }

    public function tipoDireccionBis()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Bis",
                "estado" => true,
                'JSON' => '{"id":88,"codigo":"BIS-0","nombre":"BIS","codPadre":"BIS","estado":"A"}'
            ],
        ];
    }
}
