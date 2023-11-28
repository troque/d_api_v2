<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->delete();

        foreach ($this->roles() as $rol) {
            Role::create($rol);
        }
    }
    public function roles()
    {
        return [
            [
                "id" => 1,
                "name" => "Administrador"
            ],
            [
                "id" => 2,
                "name" => "Registrar documento de respuesta"
            ],
            [
                "id" => 3,
                "name" => "Abogado"
            ],
            [
                "id" => 4,
                "name" => "Secretaria"
            ],
            [
                "id" => 5,
                "name" => "Aprobación por delegado"
            ],
            [
                "id" => 6,
                "name" => "Tramite de correspondencia y envío"
            ],
            [
                "id" => 7,
                "name" => "Aprobación por coordinación disciplinarios"
            ],
            [
                "id" => 9,
                "name" => "Gestión secretaria"
            ],
        ];
    }
}
