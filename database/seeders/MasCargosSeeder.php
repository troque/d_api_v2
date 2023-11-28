<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CargosModel;

class MasCargosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->cargos() as $ciudad) {
            CargosModel::create($ciudad);
        }
    }

    public function cargos(): array
    {
        return [
            [
                'nombre' => 'Directivo',
                'estado' => true,
            ],
            [
                'nombre' => 'Asesor',
                'estado' => true,
            ],
            [
                'nombre' => 'Profesional',
                'estado' => true,
            ],
            [
                'nombre' => 'Técnico',
                'estado' => true,
            ],
            [
                'nombre' => 'Asistencia',
                'estado' => true,
            ],
            [
                'nombre' => 'Sin identificar',
                'estado' => true,
            ],
        ];
    }
}
