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
                "nombre" => "Cierre de etapa",
            ],

            [
                "nombre" => "fase",
            ],
        ];
    }
}
