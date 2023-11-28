<?php

namespace Database\Seeders;

use App\Models\DependenciaOrigenModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasDependenciaOrigenSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('MAS_DEPENDENCIA_ORIGEN')->delete();
        foreach ($this->dependenciaOrigen() as $dependencia) {
            DependenciaOrigenModel::create($dependencia);
        }
    }

    public function dependenciaOrigen()
    {
        return [
            [
                "id" => "0",
                "nombre" => "--BUSQUEDA DEPENDENCIA--",
                "estado" => 0,
                "codigo_homologado" => "0",
            ],
            [
                "id" => "1",
                "nombre" => "CENTRO ATENCIÓN A LA COMUNIDAD",
                "estado" => 0,
                "codigo_homologado" => "1",
            ],
            [
                "id" => "2",
                "nombre" => "DIRECCIÓN DE TECNOLOGÍAS DE INFORMACIÓN Y COMUNICACIÓN",
                "estado" => 0,
                "codigo_homologado" => "2",
            ],
            [
                "id" => "3",
                "nombre" => "SUPERCADE CRA. 30",
                "estado" => 0,
                "codigo_homologado" => "3",
            ],
            [
                "id" => "4",
                "nombre" => "SUPERCADE AMERICAS",
                "estado" => 0,
                "codigo_homologado" => "4",
            ],
            [
                "id" => "5",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA III",
                "estado" => 1,
                "codigo_homologado" => "5",
            ],
            [
                "id" => "6",
                "nombre" => "PERSONERÍA LOCAL DE BARRIOS UNIDOS",
                "estado" => 0,
                "codigo_homologado" => "6",
            ],
            [
                "id" => "7",
                "nombre" => "PERSONERÍA LOCAL DE ENGATIVA",
                "estado" => 0,
                "codigo_homologado" => "7",
            ],
            [
                "id" => "8",
                "nombre" => "PERSONERÍA LOCAL DE FONTIBÓN",
                "estado" => 0,
                "codigo_homologado" => "8",
            ],
            [
                "id" => "9",
                "nombre" => "PERSONERÍA LOCAL DE SUBA",
                "estado" => 0,
                "codigo_homologado" => "9",
            ],
            [
                "id" => "10",
                "nombre" => "PERSONERÍA LOCAL DE USAQUEN",
                "estado" => 0,
                "codigo_homologado" => "10",
            ],
            [
                "id" => "11",
                "nombre" => "PERSONERÍA LOCAL DE KENNEDY",
                "estado" => 0,
                "codigo_homologado" => "11",
            ],
            [
                "id" => "12",
                "nombre" => "PERSONERÍA LOCAL DE SUMAPAZ",
                "estado" => 0,
                "codigo_homologado" => "12",
            ],
            [
                "id" => "13",
                "nombre" => "PERSONERÍA LOCAL DE BOSA",
                "estado" => 0,
                "codigo_homologado" => "13",
            ],
            [
                "id" => "14",
                "nombre" => "PERSONERÍA LOCAL DE CIUDAD BOLIVAR",
                "estado" => 0,
                "codigo_homologado" => "14",
            ],
            [
                "id" => "15",
                "nombre" => "PERSONERÍA LOCAL DE CHAPINERO",
                "estado" => 0,
                "codigo_homologado" => "15",
            ],
            [
                "id" => "16",
                "nombre" => "PERSONERÍA LOCAL DE TEUSAQUILLO",
                "estado" => 0,
                "codigo_homologado" => "16",
            ],
            [
                "id" => "17",
                "nombre" => "PERSONERÍA LOCAL DE SANTA FE",
                "estado" => 0,
                "codigo_homologado" => "17",
            ],
            [
                "id" => "18",
                "nombre" => "PERSONERÍA LOCAL DE CANDELARIA",
                "estado" => 0,
                "codigo_homologado" => "18",
            ],
            [
                "id" => "19",
                "nombre" => "PERSONERÍA LOCAL DE MÁRTIRES",
                "estado" => 0,
                "codigo_homologado" => "19",
            ],
            [
                "id" => "20",
                "nombre" => "PERSONERÍA LOCAL DE ANTONIO NARIÑO",
                "estado" => 0,
                "codigo_homologado" => "20",
            ],
            [
                "id" => "21",
                "nombre" => "PERSONERÍA LOCAL DE PUENTE ARANDA",
                "estado" => 0,
                "codigo_homologado" => "21",
            ],
            [
                "id" => "22",
                "nombre" => "PERSONERÍA LOCAL DE RAFAEL URIBE URIBE",
                "estado" => 0,
                "codigo_homologado" => "22",
            ],
            [
                "id" => "23",
                "nombre" => "PERSONERÍA LOCAL DE SAN CRISTOBAL",
                "estado" => 0,
                "codigo_homologado" => "23",
            ],
            [
                "id" => "24",
                "nombre" => "PERSONERÍA LOCAL DE USME",
                "estado" => 0,
                "codigo_homologado" => "24",
            ],
            [
                "id" => "25",
                "nombre" => "PERSONERÍA LOCAL DE TUNJUELITO",
                "estado" => 0,
                "codigo_homologado" => "25",
            ],
            [
                "id" => "26",
                "nombre" => "DIRECCIÓN ADMINISTRATIVA Y FINANCIERA",
                "estado" => 0,
                "codigo_homologado" => "26",
            ],
            [
                "id" => "30",
                "nombre" => "SUPERCADE SUBA",
                "estado" => 0,
                "codigo_homologado" => "30",
            ],
            [
                "id" => "31",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA II",
                "estado" => 1,
                "codigo_homologado" => "31",
            ],
            [
                "id" => "32",
                "nombre" => "SUPERCADE BOSA",
                "estado" => 0,
                "codigo_homologado" => "32",
            ],
            [
                "id" => "33",
                "nombre" => "SUPERCADE PUENTE ARANDA",
                "estado" => 0,
                "codigo_homologado" => "33",
            ],
            [
                "id" => "34",
                "nombre" => "OFICINA ASESORA DE DIVULGACIÓN Y PRENSA",
                "estado" => 0,
                "codigo_homologado" => "34",
            ],
            [
                "id" => "35",
                "nombre" => "PERSONERO DE BOGOTA",
                "estado" => 0,
                "codigo_homologado" => "35",
            ],
            [
                "id" => "36",
                "nombre" => "JURISD. (COOR.SUPERCADES)",
                "estado" => 0,
                "codigo_homologado" => "36",
            ],
            [
                "id" => "37",
                "nombre" => "PERSONERÍA DELEGADA PARA LA COORDINACIÓN DE PERSONERÍAS LOCALES",
                "estado" => 0,
                "codigo_homologado" => "37",
            ],
            [
                "id" => "38",
                "nombre" => "SECRETARÍA GENERAL",
                "estado" => 0,
                "codigo_homologado" => "38",
            ],
            [
                "id" => "39",
                "nombre" => "SUPERCADE 20 DE JULIO",
                "estado" => 0,
                "codigo_homologado" => "39",
            ],
            [
                "id" => "50",
                "nombre" => "DIRECCIÓN DE INVESTIGACIONES ESPECIALES Y APOYO TÉCNICO",
                "estado" => 1,
                "codigo_homologado" => "50",
            ],
            [
                "id" => "51",
                "nombre" => "P.D. PARA ASUNTOS POLICIVOS Y CIVILES",
                "estado" => 0,
                "codigo_homologado" => "51",
            ],
            [
                "id" => "52",
                "nombre" => "P.D. PARA ASUNTOS PENALES I",
                "estado" => 0,
                "codigo_homologado" => "52",
            ],
            [
                "id" => "53",
                "nombre" => "P.D. PARA ASUNTOS PENALES II",
                "estado" => 0,
                "codigo_homologado" => "53",
            ],
            [
                "id" => "54",
                "nombre" => "PD PARA LOS SECTORES GESTIÓN PÚBLICA, GESTIÓN JURÍDICA Y GOBIERNO",
                "estado" => 0,
                "codigo_homologado" => "54",
            ],
            [
                "id" => "55",
                "nombre" => "PD PARA EL SECTOR AMBIENTE",
                "estado" => 0,
                "codigo_homologado" => "55",
            ],
            [
                "id" => "56",
                "nombre" => "PD PARA LOS SECTORES PLANEACIÓN Y MOVILIDAD",
                "estado" => 0,
                "codigo_homologado" => "56",
            ],
            [
                "id" => "57",
                "nombre" => "P.D. PARA HABITAT Y SERVICIOS PUBLICOS",
                "estado" => 0,
                "codigo_homologado" => "57",
            ],
            [
                "id" => "58",
                "nombre" => "P.D. PARA ASUNTOS DE EDUCACIÓN CULTURA RECREACIÓN Y DEPORTE",
                "estado" => 0,
                "codigo_homologado" => "58",
            ],
            [
                "id" => "59",
                "nombre" => "P.D. PARA FINANZAS Y DESARROLLO ECONOMICO",
                "estado" => 0,
                "codigo_homologado" => "59",
            ],
            [
                "id" => "60",
                "nombre" => "PD PARA LOS SECTORES MUJERES E INTEGRACIÓN SOCIAL",
                "estado" => 0,
                "codigo_homologado" => "60",
            ],
            [
                "id" => "61",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA IV",
                "estado" => 1,
                "codigo_homologado" => "61",
            ],
            [
                "id" => "62",
                "nombre" => "P.D. PARA LA ASISTENCIA JURÍDICA AL CIUDADANO",
                "estado" => 0,
                "codigo_homologado" => "62",
            ],
            [
                "id" => "64",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA I",
                "estado" => 1,
                "codigo_homologado" => "64",
            ],
            [
                "id" => "65",
                "nombre" => "DIRECCIÓN DE PLANEACIÓN",
                "estado" => 0,
                "codigo_homologado" => "65",
            ],
            [
                "id" => "70",
                "nombre" => "PERSONERÍA AUXILIAR",
                "estado" => 0,
                "codigo_homologado" => "70",
            ],
            [
                "id" => "80",
                "nombre" => "CORRESPONDENCIA",
                "estado" => 0,
                "codigo_homologado" => "80",
            ],
            [
                "id" => "82",
                "nombre" => "OFICINA ASESORA DE JURÍDICA",
                "estado" => 0,
                "codigo_homologado" => "82",
            ],
            [
                "id" => "90",
                "nombre" => "P.D. PARA LA DEFENSA DE LOS DERECHOS HUMANOS",
                "estado" => 0,
                "codigo_homologado" => "90",
            ],
            [
                "id" => "95",
                "nombre" => "OFICINA CONTROL INTERNO",
                "estado" => 1,
                "codigo_homologado" => "95",
            ],
            [
                "id" => "170",
                "nombre" => "DIRECCIÓN DE TALENTO HUMANO",
                "estado" => 0,
                "codigo_homologado" => "170",
            ],
            [
                "id" => "300",
                "nombre" => "P.D. PARA LA SEGUNDA INSTANCIA",
                "estado" => 0,
                "codigo_homologado" => "300",
            ],
            [
                "id" => "301",
                "nombre" => "DESPACHO",
                "estado" => 0,
                "codigo_homologado" => "301",
            ],
            [
                "id" => "302",
                "nombre" => "CADE FONTIBON",
                "estado" => 0,
                "codigo_homologado" => "302",
            ],
            [
                "id" => "303",
                "nombre" => "CAPACITACION",
                "estado" => 0,
                "codigo_homologado" => "303",
            ],
            [
                "id" => "304",
                "nombre" => "OTRAS ENTIDADES",
                "estado" => 0,
                "codigo_homologado" => "304",
            ],
            [
                "id" => "305",
                "nombre" => "FERIA DE SERVICIO AL CIUDADANO",
                "estado" => 0,
                "codigo_homologado" => "305",
            ],
            [
                "id" => "306",
                "nombre" => "PERSONERIA A LA CALLE",
                "estado" => 0,
                "codigo_homologado" => "306",
            ],
            [
                "id" => "307",
                "nombre" => "PERSONEROS DE COLOMBIA (Asuntos de Gobierno)",
                "estado" => 0,
                "codigo_homologado" => "307",
            ],
            [
                "id" => "310",
                "nombre" => "SECRETARÍA COMÚN",
                "estado" => 1,
                "codigo_homologado" => "310",
            ],
            [
                "id" => "311",
                "nombre" => "DIRECCION DE CONCILIACIÓN Y MECANISMOS ALTERNATIVOS DE SOLUCIÓN DE CONFLICTOS",
                "estado" => 0,
                "codigo_homologado" => "311",
            ],
            [
                "id" => "312",
                "nombre" => "PD PARA LA DEFENSA Y PROTECCIÓN DE LOS DERECHOS DEL CONSUMIDOR",
                "estado" => 0,
                "codigo_homologado" => "312",
            ],
            [
                "id" => "313",
                "nombre" => "PD PARA LA PROTECCIÓN DE VÍCTIMAS DEL CONFLICTO ARMADO INTERNO",
                "estado" => 0,
                "codigo_homologado" => "313",
            ],
            [
                "id" => "314",
                "nombre" => "P.D. PARA FAMILIA Y SUJETOS DE ESPECIAL PROTECCIÓN CONSTITUCIONAL",
                "estado" => 1,
                "codigo_homologado" => "314",
            ],
            [
                "id" => "315",
                "nombre" => "P.D. PARA LA SEGURIDAD Y CONVIVENCIA CIUDADANA",
                "estado" => 0,
                "codigo_homologado" => "315",
            ],
            [
                "id" => "316",
                "nombre" => "PD PARA LA COOR. DEL MIN. PÚBLICO Y LOS DERECHOS HUMANOS",
                "estado" => 0,
                "codigo_homologado" => "316",
            ],
            [
                "id" => "317",
                "nombre" => "PD PARA LA COORDINACIÓN DE PREVENCIÓN Y CONTROL A LA FUNCIÓN PÚBLICA",
                "estado" => 0,
                "codigo_homologado" => "317",
            ],
            [
                "id" => "318",
                "nombre" => "PD PARA COORDINACIÓN DE POTESTAD DISCIPLINARIA",
                "estado" => 1,
                "codigo_homologado" => "318",
            ],
            [
                "id" => "320",
                "nombre" => "PERSONERÍA 24 HORAS",
                "estado" => 0,
                "codigo_homologado" => "320",
            ],
            [
                "id" => "321",
                "nombre" => "GRUPO REQUERIMIENTOS CIUDADANOS",
                "estado" => 0,
                "codigo_homologado" => "321",
            ],
            [
                "id" => "325",
                "nombre" => "GRUPO DE CONSTRUCCION DE CIUDADANO",
                "estado" => 0,
                "codigo_homologado" => "325",
            ],
            [
                "id" => "327",
                "nombre" => "GRUPO PAS",
                "estado" => 0,
                "codigo_homologado" => "327",
            ],
            [
                "id" => "330",
                "nombre" => "CENTROS COMERCIALES",
                "estado" => 0,
                "codigo_homologado" => "330",
            ],
            [
                "id" => "331",
                "nombre" => "SUBDIRECCIÓN DE GESTIÓN DOCUMENTAL Y RECURSOS FÍSICOS",
                "estado" => 0,
                "codigo_homologado" => "331",
            ],
            [
                "id" => "332",
                "nombre" => "PROCESOS DISCIPLINARIOS POR ASIGNAR",
                "estado" => 0,
                "codigo_homologado" => "332",
            ],
            [
                "id" => "333",
                "nombre" => "GRUPO RADAR (COORD. VEEDURIAS)",
                "estado" => 0,
                "codigo_homologado" => "333",
            ],
            [
                "id" => "340",
                "nombre" => "CENTRO DIGNIFICAR KENNEDY",
                "estado" => 0,
                "codigo_homologado" => "340",
            ],
            [
                "id" => "345",
                "nombre" => "CENTRO DIGNIFICAR CHAPINERO",
                "estado" => 0,
                "codigo_homologado" => "345",
            ],
            [
                "id" => "350",
                "nombre" => "CENTRO DIGNIFICAR RAFAEL URIBE",
                "estado" => 0,
                "codigo_homologado" => "350",
            ],
            [
                "id" => "355",
                "nombre" => "CENTRO DIGNIFICAR PUNTO TERMINAL",
                "estado" => 0,
                "codigo_homologado" => "355",
            ],
            [
                "id" => "360",
                "nombre" => "CENTRO DIGNIFICAR BOSA",
                "estado" => 0,
                "codigo_homologado" => "360",
            ],
            [
                "id" => "365",
                "nombre" => "CENTRO DIGNIFICAR CIUDAD BOLIVAR",
                "estado" => 0,
                "codigo_homologado" => "365",
            ],
            [
                "id" => "366",
                "nombre" => "CENTRO DIGNIFICAR SUBA",
                "estado" => 0,
                "codigo_homologado" => "366",
            ],
            [
                "id" => "367",
                "nombre" => "GRUPO AUDIENCIAS PUBLICAS DE REQUERIMIENTOS CIUDADANOS",
                "estado" => 0,
                "codigo_homologado" => "367",
            ],
            [
                "id" => "368",
                "nombre" => "COLPENSIONES",
                "estado" => 0,
                "codigo_homologado" => "368",
            ],
            [
                "id" => "369",
                "nombre" => "NO REGISTRA DEPENDENCIA :: FUE REGISTRADO POR UN CIUDADANO",
                "estado" => 0,
                "codigo_homologado" => "369",
            ],
            [
                "id" => "380",
                "nombre" => "SUBDIRECCIÓN DE GESTIÓN DE TALENTO HUMANO",
                "estado" => 0,
                "codigo_homologado" => "380",
            ],
            [
                "id" => "381",
                "nombre" => "SUBDIRECCIÓN DE PRESUPUESTO, CONTABILIDAD Y TESORERÍA.",
                "estado" => 0,
                "codigo_homologado" => "381",
            ],
            [
                "id" => "385",
                "nombre" => "GAEPVD",
                "estado" => 0,
                "codigo_homologado" => "385",
            ],
            [
                "id" => "386",
                "nombre" => "CIMA",
                "estado" => 0,
                "codigo_homologado" => "386",
            ],
            [
                "id" => "387",
                "nombre" => "NO APLICA",
                "estado" => 0,
                "codigo_homologado" => "387",
            ],
            [
                "id" => "388",
                "nombre" => "DESCONGESTIÓN",
                "estado" => 0,
                "codigo_homologado" => "388",
            ],
            [
                "id" => "389",
                "nombre" => "SITIO WEB PERSONERÍA",
                "estado" => 0,
                "codigo_homologado" => "389",
            ],
            [
                "id" => "390",
                "nombre" => "SUBDIRECCIÓN DE DESARROLLO DEL TALENTO HUMANO",
                "estado" => 0,
                "codigo_homologado" => "390",
            ],
            [
                "id" => "391",
                "nombre" => "SUBDIRECCIÓN DE CONTRATACIÓN",
                "estado" => 0,
                "codigo_homologado" => "391",
            ],
            [
                "id" => "392",
                "nombre" => "OFICINA DE CONTROL INTERNO DISCIPLINARIO",
                "estado" => 0,
                "codigo_homologado" => "392",
            ],
            [
                "id" => "393",
                "nombre" => "DIRECCIÓN DE GESTIÓN DEL CONOCIMIENTO E INNOVACIÓN",
                "estado" => 0,
                "codigo_homologado" => "393",
            ],
            [
                "id" => "394",
                "nombre" => "PD PARA LA ORIENTACIÓN Y ASISTENCIA A LAS PERSONAS",
                "estado" => 0,
                "codigo_homologado" => "394",
            ],
            [
                "id" => "395",
                "nombre" => "PD PARA LA ORIENTACIÓN Y ASISTENCIA A LAS PERSONAS",
                "estado" => 0,
                "codigo_homologado" => "394",
            ],
            [
                "id" => "396",
                "nombre" => "PD PARA LA ORIENTACIÓN Y ASISTENCIA A LAS PERSONAS",
                "estado" => 0,
                "codigo_homologado" => "394",
            ],
            [
                "id" => "397",
                "nombre" => "CONTROL INTERNO DISCIPLINARIO",
                "estado" => 1,
                "codigo_homologado" => "397",
            ],
            [
                "id" => "410",
                "nombre" => "DESCONGESTIÓN SECRETARÍA COMÚN",
                "estado" => 1,
                "codigo_homologado" => "410",
            ],

            [
                "id" => "411",
                "nombre" => "PD PARA LA ASISTENCIA EN ASUNTOS JURISDICCIONALES",
                "estado" => 1,
                "codigo_homologado" => "411",
            ],

            [
                "id" => "412",
                "nombre" => "PERSONERIA DELEGADA PARA LA SEGUNDA INSTANCIA",
                "estado" => 1,
                "codigo_homologado" => "412",
            ],

            [
                "id" => "413",
                "nombre" => "SECRETARIA COMUN FORSECURITY",
                "estado" => 1,
                "id_usuario_jefe" => 17,
                "codigo_homologado" => "413",
            ],

            [
                "id" => "414",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA I FORSECURITY",
                "estado" => 1,
                "id_usuario_jefe" => 12,
                "codigo_homologado" => "414",
            ],

            [
                "id" => "415",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA II FORSECURITY",
                "estado" => 1,
                "id_usuario_jefe" => 15,
                "codigo_homologado" => "415",
            ],

            [
                "id" => "416",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA III FORSECURITY",
                "estado" => 1,
                "id_usuario_jefe" => 18,
                "codigo_homologado" => "416",
            ],

            [
                "id" => "417",
                "nombre" => "PD PARA LA POTESTAD DISCIPLINARIA IV FORSECURITY",
                "estado" => 1,
                "id_usuario_jefe" => 19,
                "codigo_homologado" => "417",
            ],

            [
                "id" => "418",
                "nombre" => "CONTROL INTERNO DISCIPLINARIO FORSECURITY",
                "estado" => 1,
                "id_usuario_jefe" => 8,
                "codigo_homologado" => "418",
            ],

            [
                "id" => "9999",
                "nombre" => "SIN IDENTIFICAR",
                "estado" => 0,
                "codigo_homologado" => "9999",
            ]
        ];
    }
}
