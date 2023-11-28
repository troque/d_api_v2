<?php

namespace Database\Seeders;

use App\Models\FormatoModel;
use Illuminate\Database\Seeder;

class MasFormatoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->TipoFormato() as $formato) {
            FormatoModel::create($formato);
        }
    }

    public function TipoFormato()
    {
        return [
            [
                "nombre" => "doc",
                "estado" => true
            ],
            [
                "nombre" => "docx",
                "estado" => true
            ],
            [
                "nombre" => "pdf",
                "estado" => true
            ],
            [
                "nombre" => "xls",
                "estado" => true
            ],
            [
                "nombre" => "xlsx",
                "estado" => true
            ],
            [
                "nombre" => "zip",
                "estado" => true
            ],
            [
                "nombre" => "rar",
                "estado" => true
            ],
            [
                "nombre" => "png",
                "estado" => true
            ],
            [
                "nombre" => "jpg",
                "estado" => true
            ],
            [
                "nombre" => "mp4",
                "estado" => true
            ],
            [
                "nombre" => "avi",
                "estado" => true
            ],
            [
                "nombre" => "mpeg",
                "estado" => true
            ],
            [
                "nombre" => "mpg",
                "estado" => true
            ],
            [
                "nombre" => "mov",
                "estado" => true
            ],
            [
                "nombre" => "mp3",
                "estado" => true
            ],
            [
                "nombre" => "wav",
                "estado" => true
            ],
            [
                "nombre" => "wma",
                "estado" => true
            ]
        ];
    }
}
