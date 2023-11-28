<?php

namespace Database\Seeders;

use App\Models\MasOriginFiling;
use App\Models\OrigenRadicadoModel;
use Illuminate\Database\Seeder;

class MasOrigenRadicadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->origenRadicado() as $origen) {
            OrigenRadicadoModel::create($origen);
        }
    }

    public function origenRadicado()
    {
        return [
            [
                "nombre" => "Interna",
                "estado" => true
            ],
            [
                "nombre" => "Externa",
                "estado" => true
            ],
            [
                "nombre" => "Persona Natural",
                "estado" => true
            ],
        ];
    }
}
