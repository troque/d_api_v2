<?php

namespace Database\Seeders;

use App\Models\TipoQuejaModel;
use Illuminate\Database\Seeder;

class MasTipoQuejaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoQueja() as $proceso) {
            TipoQuejaModel::create($proceso);
        }
    }

    public function tipoQueja()
    {
        return [
            [
                "nombre" => "Externa",
                "estado" => true
            ],
            [
                "nombre" => "Interna",
                "estado" => true
            ],
        ];
    }
}
