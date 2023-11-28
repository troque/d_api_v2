<?php

namespace Database\Seeders;


use App\Models\TipoTransaccionModel;
use Illuminate\Database\Seeder;

class MasTipoTransaccionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->masTipoTransaccion() as $tipoTransaccion) {
         TipoTransaccionModel::create($tipoTransaccion) ;
        }
    }


    public function masTipoTransaccion()
    {
        return [

            [
                "id" => 1,
                "nombre" => "Cierre de etapa",
            ],

            [
                "id" => 2,
                "nombre" => "Anexa Documentos",
            ],

            [
                "id" => 3,
                "nombre" => "Clasificacion tipo de expediente",
            ],

            [
                "id" => 4,
                "nombre" => "Reclasificacion tipo de expediente",
            ],

            [
                "id" => 5,
                "nombre" => "Reasignacion",
            ],

            [
                "id" => 6,
                "nombre" => "Inicio de proceso disciplinario",
            ],
        ];
    }
}
