<?php

namespace Database\Seeders;

use App\Models\TipoDerechoPeticionModel;
use App\Models\TipoProcesoDisciplinarioModel;
use Illuminate\Database\Seeder;

class MasTipoProcesoDisciplinarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->tipoProcesoDisciplinario() as $item) {
            TipoProcesoDisciplinarioModel::create($item);
        }
    }

    public function tipoProcesoDisciplinario()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Externa",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "Interna",
                "estado" => true
            ],
        ];
    }
}
