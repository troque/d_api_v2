<?php

namespace Database\Seeders;

use App\Models\ClasificacionRadicadoModel;
use Illuminate\Database\Seeder;

class ClasificacionRadicadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->clasificacionRadicado() as $clasificacion) {
            ClasificacionRadicadoModel::create($clasificacion);
        }
    }

    public function clasificacionRadicado()
    {
        return [
            
        ];
    }
}
