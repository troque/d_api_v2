<?php

namespace Database\Seeders;

use App\Models\TransaccionesModel;
use Illuminate\Database\Seeder;

class MasTransacionesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->transaciones() as $transaciones) {
            TransaccionesModel::create($transaciones);
        }
    }

    public function transaciones(): array
    {
        return [
            [
                'titulo' => 'Dejar en mis pendientes',
                'descripcion' => 'El proceso sigue activo en su lista de pendientes para continuar con la gestión',
            ],
            [
                'titulo' => 'Enviar a alguien de mi dependencia',
                'descripcion' => '',
            ],
            [
                'titulo' => 'Enviar a jefe de la dependencia',
                'descripcion' => '',
            ],
            [
                'titulo' => 'Enviar a otra dependencia',
                'descripcion' => '',
            ],
            [
                'titulo' => 'Regresar proceso al último usuario',
                'descripcion' => '',
            ],


        ];
    }
}
