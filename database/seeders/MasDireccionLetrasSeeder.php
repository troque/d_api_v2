<?php

namespace Database\Seeders;

use App\Models\DireccionLetrasModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasDireccionLetrasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('mas_direccion_letras')->delete();

        foreach ($this->tipoDireccionLetras() as $item) {
            DireccionLetrasModel::create($item);
        }
    }

    public function tipoDireccionLetras()
    {
        return [
            ["id" => 1,"nombre" => "A","estado" => true,	"json" => '{"id": 8,"codigo": "PE-CUAA","nombre": "A","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 2,"nombre" => "B","estado" => true,	"json" => '{"id": 9,"codigo": "PE-CUAB","nombre": "B","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 3,"nombre" => "C","estado" => true,	"json" => '{"id": 108,"codigo": "PE-CUAC","nombre": "C","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 4,"nombre" => "D","estado" => true,	"json" => '{"id": 109,"codigo": "PE-CUAD","nombre": "D","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 5,"nombre" => "E","estado" => true,	"json" => '{"id": 253,"codigo": "PE-CUAE","nombre": "E","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 6,"nombre" => "F","estado" => true,	"json" => '{"id": 110,"codigo": "PE-CUAF","nombre": "F","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 7,"nombre" => "G","estado" => true,	"json" => '{"id": 111,"codigo": "PE-CUAG","nombre": "G","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 8,"nombre" => "H","estado" => true,	"json" => '{"id": 112,"codigo": "PE-CUAH","nombre": "H","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 9,"nombre" => "I","estado" => true,	"json" => '{"id": 113,"codigo": "PE-CUAI","nombre": "I","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 10,"nombre" => "J","estado" => true,	"json" => '{"id": 114,"codigo": "PE-CUAJ","nombre": "J","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 11,"nombre" => "K","estado" => true,	"json" => '{"id": 115,"codigo": "PE-CUAK","nombre": "K","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 12,"nombre" => "L","estado" => true,	"json" => '{"id": 116,"codigo": "PE-CUAL","nombre": "L","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 13,"nombre" => "M","estado" => true,	"json" => '{"id": 117,"codigo": "PE-CUAM","nombre": "M","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 14,"nombre" => "N","estado" => true,	"json" => '{"id": 118,"codigo": "PE-CUAN","nombre": "N","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 15,"nombre" => "Ñ","estado" => true,	"json" => '{"id": 131,"codigo": "PE-CUANE","nombre": "Ñ","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 16,"nombre" => "O","estado" => true,	"json" => '{"id": 119,"codigo": "PE-CUAO","nombre": "O","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 17,"nombre" => "P","estado" => true,	"json" => '{"id": 120,"codigo": "PE-CUAP","nombre": "P","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 18,"nombre" => "Q","estado" => true,	"json" => '{"id": 121,"codigo": "PE-CUAQ","nombre": "Q","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 19,"nombre" => "R","estado" => true,	"json" => '{"id": 122,"codigo": "PE-CUAR","nombre": "R","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 20,"nombre" => "S","estado" => true,	"json" => '{"id": 123,"codigo": "PE-CUAS","nombre": "S","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 21,"nombre" => "T","estado" => true,	"json" => '{"id": 124,"codigo": "PE-CUAT","nombre": "T","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 22,"nombre" => "U","estado" => true,	"json" => '{"id": 125,"codigo": "PE-CUAU","nombre": "U","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 23,"nombre" => "V","estado" => true,	"json" => '{"id": 126,"codigo": "PE-CUAV","nombre": "V","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 24,"nombre" => "W","estado" => true,	"json" => '{"id": 127,"codigo": "PE-CUAW","nombre": "W","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 25,"nombre" => "X","estado" => true,	"json" => '{"id": 128,"codigo": "PE-CUAX","nombre": "X","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 26,"nombre" => "Y","estado" => true,	"json" => '{"id": 129,"codigo": "PE-CUAY","nombre": "Y","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 27,"nombre" => "Z","estado" => true,	"json" => '{"id": 130,"codigo": "PE-CUAZ","nombre": "Z","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 28,"nombre" => "Aa","estado" => true,	"json" => '{"id": 132,"codigo": "PE-CUAAA","nombre": "AA","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 29,"nombre" => "Bb","estado" => true,	"json" => '{"id": 133,"codigo": "PE-CUABB","nombre": "BB","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 30,"nombre" => "Cc","estado" => true,	"json" => '{"id": 134,"codigo": "PE-CUACC","nombre": "CC","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 31,"nombre" => "Dc","estado" => true,	"json" => '{"id": 135,"codigo": "PE-CUADD","nombre": "DC","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 32,"nombre" => "Ee","estado" => true,	"json" => '{"id": 254,"codigo": "PE-CUAEE","nombre": "EE","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 33,"nombre" => "Ff","estado" => true,	"json" => '{"id": 136,"codigo": "PE-CUAFF","nombre": "FF","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 34,"nombre" => "Gg","estado" => true,	"json" => '{"id": 137,"codigo": "PE-CUAGG","nombre": "GG","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 35,"nombre" => "Hh","estado" => true,	"json" => '{"id": 138,"codigo": "PE-CUAHH","nombre": "HH","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 36,"nombre" => "Ii","estado" => true,	"json" => '{"id": 139,"codigo": "PE-CUAII","nombre": "II","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 37,"nombre" => "Jj","estado" => true,	"json" => '{"id": 140,"codigo": "PE-CUAJJ","nombre": "JJ","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 38,"nombre" => "Kk","estado" => true,	"json" => '{"id": 141,"codigo": "PE-CUAKK","nombre": "KK","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 39,"nombre" => "Ll","estado" => true,	"json" => '{"id": 142,"codigo": "PE-CUALL","nombre": "LL","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 40,"nombre" => "Mm","estado" => true,	"json" => '{"id": 143,"codigo": "PE-CUAMM","nombre": "MM","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 41,"nombre" => "Nn","estado" => true,	"json" => '{"id": 144,"codigo": "PE-CUANN","nombre": "NN","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 42,"nombre" => "Ññ","estado" => true,	"json" => '{"id": 157,"codigo": "PE-CUANI","nombre": "ÑÑ","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 43,"nombre" => "Oo","estado" => true,	"json" => '{"id": 145,"codigo": "PE-CUAOO","nombre": "OO","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 44,"nombre" => "Pp","estado" => true,	"json" => '{"id": 146,"codigo": "PE-CUAPP","nombre": "PP","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 45,"nombre" => "Qq","estado" => true,	"json" => '{"id": 147,"codigo": "PE-CUAQQ","nombre": "QQ","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 46,"nombre" => "Rr","estado" => true,	"json" => '{"id": 148,"codigo": "PE-CUARR","nombre": "RR","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 47,"nombre" => "Ss","estado" => true,	"json" => '{"id": 149,"codigo": "PE-CUASS","nombre": "SS","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 48,"nombre" => "Tt","estado" => true,	"json" => '{"id": 150,"codigo": "PE-CUATT","nombre": "TT","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 49,"nombre" => "Xx","estado" => true,	"json" => '{"id": 154,"codigo": "PE-CUAXX","nombre": "XX","codPadre": "PE-CUAD","estado": "A "}'],
            ["id" => 50,"nombre" => "Zz","estado" => true,	"json" => '{"id": 156,"codigo": "PE-CUAZZ","nombre": "ZZ","codPadre": "PE-CUAD","estado": "A "}'],
        ];
    }
}
