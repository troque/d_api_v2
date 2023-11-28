<?php

namespace Database\Seeders;

use App\Models\TipoDerechoPeticionModel;
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
        foreach ($this->tipoDerechoPeticion() as $tipo_derecho_peticion) {
            TipoDerechoPeticionModel::create($tipo_derecho_peticion);
        }
    }

    public function tipoProcesoDisciplinario()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Copias",
                "observacion" => "Un Derecho de petición considerado como Copias cuenta con 15 días para ser resuelto.",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "General",
                "observacion" => "un Derecho de petición considerado como General cuenta con 15 días para ser resuelto.",
                "estado" => true
            ],
            [
                "id" => 3,
                "nombre" => "Alerta control político",
                "observacion" => "un Derecho de petición considerado como Alerta de control político cuenta con 3 días para ser resuelto.",
                "estado" => true
            ],
        ];
    }
}
