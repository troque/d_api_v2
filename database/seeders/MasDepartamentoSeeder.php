<?php

namespace Database\Seeders;

use App\Models\DepartamentoModel;
use Illuminate\Database\Seeder;

class MasDepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->departamentos() as $departamento) {
            DepartamentoModel::create($departamento);
        }
    }

    public function departamentos(): array
    {
        return [
            [
                "nombre" => "Antioquia",
                'estado' => true,
                "codigo_dane" => "05"
            ],
            [
                "nombre" => "Vichada",
                'estado' => true,
                "codigo_dane" => "99"
            ],
            [
                "nombre" => "Vaupés",
                'estado' => true,
                "codigo_dane" => "97"
            ],
            [
                "nombre" => "Guaviare",
                'estado' => true,
                "codigo_dane" => "95"
            ],
            [
                "nombre" => "Guainía",
                'estado' => true,
                "codigo_dane" => "94"
            ],
            [
                "nombre" => "Amazonas",
                'estado' => true,
                "codigo_dane" => "91"
            ],
            [
                "nombre" => "Archipiélago de San Andrés, Providencia y Santa Catalina",
                'estado' => true,
                "codigo_dane" => "88"
            ],
            [
                "nombre" => "Putumayo",
                'estado' => true,
                "codigo_dane" => "86"
            ],
            [
                "nombre" => "Casanare",
                'estado' => true,
                "codigo_dane" => "85"
            ],
            [
                "nombre" => "Arauca",
                'estado' => true,
                "codigo_dane" => "81"
            ],
            [
                "nombre" => "Valle del Cauca",
                'estado' => true,
                "codigo_dane" => "76"
            ],
            [
                "nombre" => "Tolima",
                'estado' => true,
                "codigo_dane" => "73"
            ],
            [
                "nombre" => "Sucre",
                'estado' => true,
                "codigo_dane" => "70"
            ],
            [
                "nombre" => "Atlántico",
                'estado' => true,
                "codigo_dane" => "08"
            ],
            [
                "nombre" => "Cundinamarca",
                'estado' => true,
                "codigo_dane" => "25"
            ],
            [
                "nombre" => "Córdoba",
                'estado' => true,
                "codigo_dane" => "23"
            ],
            [
                "nombre" => "Cesar",
                'estado' => true,
                "codigo_dane" => "20"
            ],
            [
                "nombre" => "Cauca",
                'estado' => true,
                "codigo_dane" => "19"
            ],
            [
                "nombre" => "Caquetá",
                'estado' => true,
                "codigo_dane" => "18"
            ],
            [
                "nombre" => "Caldas",
                'estado' => true,
                "codigo_dane" => "17"
            ],
            [
                "nombre" => "Boyacá",
                'estado' => true,
                "codigo_dane" => "15"
            ],
            [
                "nombre" => "Bolívar",
                'estado' => true,
                "codigo_dane" => "13"
            ],
            [
                "nombre" => "Bogotá",
                'estado' => true,
                "codigo_dane" => "11"
            ],
            [
                "nombre" => "Risaralda",
                'estado' => true,
                "codigo_dane" => "66"
            ],
            [
                "nombre" => "Quindío",
                'estado' => true,
                "codigo_dane" => "63"
            ],
            [
                "nombre" => "Norte de Santander",
                'estado' => true,
                "codigo_dane" => "54"
            ],
            [
                "nombre" => "Nariño",
                'estado' => true,
                "codigo_dane" => "52"
            ],
            [
                "nombre" => "Meta",
                'estado' => true,
                "codigo_dane" => "50"
            ],
            [
                "nombre" => "Magdalena",
                'estado' => true,
                "codigo_dane" => "47"
            ],
            [
                "nombre" => "La Guajira",
                'estado' => true,
                "codigo_dane" => "44"
            ],
            [
                "nombre" => "Huila",
                'estado' => true,
                "codigo_dane" => "41"
            ],
            [
                "nombre" => "Chocó",
                'estado' => true,
                "codigo_dane" => "27"
            ],
            [
                "nombre" => "Santander",
                'estado' => true,
                "codigo_dane" => "68"
            ],

        ];
    }
}