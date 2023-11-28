<?php

namespace Database\Seeders;

use App\Models\VigenciaModel;
use Illuminate\Database\Seeder;

class MasTipoEstadoEtapaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        foreach ($this->vigencia() as $proceso) {
            VigenciaModel::create($proceso) ;
        }
    }

    public function MasTipoEstadoEtapa()
    {
        return [
            ["vigencia" => "2022","estado" => true],
            ["vigencia" => "2021", "estado" => true],
            ["vigencia" => "2020","estado" => true],
            ["vigencia" => "2019","estado" => true],
            ["vigencia" => "2018","estado" => true],
            ["vigencia" => "2017","estado" => true],
            ["vigencia" => "2016","estado" => true],
            ["vigencia" => "2015","estado" => true],
            ["vigencia" => "2014","estado" => true],
            ["vigencia" => "2013","estado" => true],
            ["vigencia" => "2012","estado" => true],
            ["vigencia" => "2011","estado" => true],
            ["vigencia" => "2010","estado" => true],
            ["vigencia" => "2009","estado" => true],
            ["vigencia" => "2008","estado" => true],
            ["vigencia" => "2007","estado" => true],
            ["vigencia" => "2006","estado" => true],
            ["vigencia" => "2005","estado" => true]
        ];
    }
}
