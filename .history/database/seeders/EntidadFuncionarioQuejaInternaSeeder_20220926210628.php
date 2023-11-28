<?php

namespace Database\Seeders;

use App\Models\TipoFuncionarioModel;
use Illuminate\Database\Seeder;

class EntidadFuncionarioQuejaInternaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->vigencia() as $proceso) {
            TipoFuncionarioModel::create($proceso) ;
        }
    }

    public function Vigencia()
    {
        return [
            ["id" => 1,"nombre" => "Planta", "estado"=>true],
            ["id" => 2, "nombre" => "Contratista", "estado"=>true]
        ];
    }
}
