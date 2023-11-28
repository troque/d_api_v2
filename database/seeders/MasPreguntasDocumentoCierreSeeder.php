<?php

namespace Database\Seeders;

use App\Models\PreguntasDocumentoCierreModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasPreguntasDocumentoCierreSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_preguntas_doc_cierre')->delete();

        foreach ($this->PreguntasDocumentoCierre() as $item) {
            PreguntasDocumentoCierreModel::create($item);
        }
    }

    public function PreguntasDocumentoCierre()
    {
        return [
            [
                "id" => 1,
                "nombre" => "Requiere documentos",
                "estado" => true
            ],
            [
                "id" => 2,
                "nombre" => "Compulsan copias",
                "estado" => true
            ],
        ];
    }
}