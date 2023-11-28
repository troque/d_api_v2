<?php

namespace App\Services;

use ZipArchive;
use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Writer\Word2007;
use App\Models\TipoUnidadModel;
use App\Http\Resources\TrazabilidadActuaciones\TrazabilidadActuacionesResource;
use Illuminate\Support\Facades\DB;
use App\Models\TrazabilidadActuacionesModel;
use App\Models\ArchivoActuacionesModel;
use App\Models\ActuacionesModel;
use App\Models\MasActuacionesModel;
use App\Models\CiudadModel;
use App\Models\DepartamentoModel;
use App\Http\Utilidades\Constants;
use Codedge\Fpdf\Fpdf\Fpdf;
use clsTbsZip;


// Se importa la extension del template processor
use App\Services\TemplateProcessorExtends;
use App\Services\CellFitFpdf;
use ConvertApi\ConvertApi;
use App\Services\htmlToPdf;
use App\Services\EasyTableFpdf;
use App\Services\ExFpdf;

class WordServices
{

    public function get_document_params($filename, $idActuacion = "")
    {
        $striped_content = '';
        $file_content = $this->get_document_content($filename);
        $start_char = '${';
        $end_char = "}";

        // $content = '';
        // $content = str_replace('</w:r></w:p></w:tc><w:tc>', " ", $file_content);
        // $content = str_replace('</w:r></w:p>', "\r\n", $file_content);
        $striped_content = strip_tags($file_content);
        $start_position = strpos($striped_content, $start_char, 0);
        $params = array();

        $end_position = strpos($striped_content, $end_char, $start_position);

        // Se consulta la informacion de los campos en la base de datos
        $arrayInformacion = MasActuacionesModel::where("ID", $idActuacion)->get();

        // Se captura la informacion de los campos
        $campos = !empty($arrayInformacion[0]["campos"]) ? json_decode($arrayInformacion[0]["campos"]) : [];

        // Se convierte el objecto a array
        $campos = (array) $campos;
        $campos = isset($campos["data"]) ? $campos["data"] : [];

        // lad("prueba>>>>>>");
        // lad($start_char);
        // lad($end_char);
        // lad("<<<<<<<<");

        $arrayParametrosInvalidos = [
            "tipoFirmado",
            "nombreUsuario",
            "dependencia",
            "imagen",
            "firmado",
            "/firmado",
            "fechaFirmado"
        ];

        while ($start_position > 0) {
            $end_position = strpos($striped_content, $end_char, $start_position);
            // lad($start_position);
            // lad($end_position);
            if ($end_position > 0) {
                $param = str_replace($start_char, "", substr($striped_content, $start_position, $end_position - $start_position));
                // lad($param);

                // Se valida que el parametro encontrado no sea igual a uno de los parametros del firmado
                if (!in_array($param, $arrayParametrosInvalidos)) {
                    array_push($params, $param);
                }
            } else
                break;
            $start_position = strpos($striped_content, $start_char, $end_position);
        }

        $array = array(
            "params" => $params,
            "campos" => $campos
        );

        // Se retorna el array
        return $array;
    }

    public function replace_document_params($filename, $params)
    {
        $guid =  bin2hex(openssl_random_pseudo_bytes(16));
        $pathToSave = storage_path() . '/files/temp/';
        $pathToFile = $pathToSave . $guid . '.docx';

        if (!file_exists($pathToSave)) {
            mkdir($pathToSave, 0777, true);
        }

        error_log("path -> " . $pathToFile);
        error_log("filename -> " . $filename);

        $templateProcessor = new TemplateProcessor($filename);

        error_log("Word params: " . json_encode($params));

        foreach ($params as $p) {

            if (empty($p["value"]) || $p["value"] == null || $p["value"] == "") {
                $p["value"] = "";
            }

            $templateProcessor->setValue($p["param"], htmlspecialchars($p["value"]));
        }

        $templateProcessor->saveAs($pathToFile);

        return $pathToFile;
    }

    // Metodo encargado de generar el pdf de la caratula principal
    public function replace_document_params_pdf($filename, $params, $parametroCamposCaratula)
    {

        // Se genera un uuid de 16 hex
        $guid =  bin2hex(openssl_random_pseudo_bytes(16));
        $nombrePdf = $guid . '.pdf';

        // Se genera la ruta donde se guardara el documento pdf
        $pathToSave = storage_path() . '/files/temp/';
        $pathToFile = $pathToSave . $nombrePdf;
        $rutaCabecera = storage_path() . '/files/templates/caratulas/cabecera.png';

        // Se valida que el archivo de la imagen de la cabecera exista
        if (!file_exists($rutaCabecera)) {
            return [
                "error" => "La imagen de la cabecera no existe o la ruta no es valida"
            ];
        }

        // Se valida que si no existe se ce cree el archivo
        if (!file_exists($pathToSave)) {
            mkdir($pathToSave, 0777, true);
        }

        // Se inicializa el array a recorrer en el pdf
        $arrayGeneral = [];

        // Se recorre el array de parametros
        foreach ($params as $value) {

            // Se captura el parametro y su valor
            $parametro = $value["param"];
            $valor = $value["value"];

            // Se recorre el array de parametros de la base de datos
            foreach ($parametroCamposCaratula as $paramBd) {

                // Se captura el parametro y su estado
                $parametroBaseDatos = $paramBd["nombre_campo"];
                $estadoParametro = $paramBd["estado"];

                // Se compara el parametro con el parametro de la base de datos y que se encuentre activo
                if ($estadoParametro == 1 && ($parametro == $parametroBaseDatos)) {

                    // Se añade el parametro y su valor al array general
                    array_push(
                        $arrayGeneral,
                        [
                            "parametro" => $parametro,
                            "valor" => $valor,
                        ]
                    );
                }
            }
        }

        // Se inicializa la clase FPDF
        $pdf = new ExFpdf();

        // Se añade una pagina
        $pdf->AddPage();
        $pdf->SetY(55);

        // Se añade la cabecera
        $this->cabecera($pdf, $rutaCabecera);

        // Se inicializa la posicion x del documento abajo de la imagen
        $pdf->SetY(70);
        $pdf->SetFont('Arial', '', 10);

        // Se genera la tabla
        $this->tablaPdf($pdf, $arrayGeneral, 1);

        // Se guarda el pdf
        $pdf->Output('F', storage_path() . '/files/temp/' . $nombrePdf, true);

        // Se retorna la ruta
        return [
            "pdf" => $pathToFile
        ];
    }

    // Funcion encargada de generar la tabla
    function tablaPdf($pdf, $array, $tipoCaratula = 0, $datosAntecedentes = [], $datosInteresados = [], $datosEntidades = [])
    {

        // Se valida el tipo de caratula a generar
        if ($tipoCaratula == 1) {

            // Se inicializa la tabla con el pdf y sus estilos
            $table = new EasyTableFpdf($pdf, '{35, 130}', 'width:170; border-color:#000000; font-size:12; border:1; paddingY:2;');
            $n = count($array) - 1;

            // Se recorre el array
            for ($i = 0; $i < $n; $i++) {

                // Se captura la informacion
                $parametro = $array[$i]["parametro"];
                $valor = $array[$i]["valor"];

                // Se convierte a UTF8 el texto para que permita tildes y demas
                $valor = iconv('UTF-8', 'windows-1252', $valor);

                // Se generan las columnas o celdas
                $table->easyCell("<b>" . $parametro . "</b>", 'valign: C; bgcolor:#ffffff;');
                $table->easyCell($valor);
                $table->printRow();

                // Se valida que sea la ultima posicion para pintar quien lo genero
                if (($n - 1) == $i) {

                    // Se da un salto de linea al final
                    $pdf->Ln();

                    // Se ajusta la altura de esta fila
                    $table->rowStyle('min-height: 30; min-width: 130; font-size:12; margin-top:5;');

                    // Se captura el nombre del usuario logeado
                    $nombre = !empty(auth()->user()->nombre) ? auth()->user()->nombre : "";
                    $apellido = !empty(auth()->user()->apellido) ? auth()->user()->apellido : "";
                    $nombreCompleto = $nombre . " " . $apellido;

                    // Se dibuja quien lo genero
                    $table->easyCell("<b>Generado por: </b>", 'border: 0; ');
                    $table->easyCell($nombreCompleto, 'border: 0; ');
                    $table->printRow();
                }
            }

            // Se finaliza la tabla
            $table->endTable($n);
        } else if ($tipoCaratula == 2) {

            // Se inicializa el array de los nombre de las unidades
            $arrayGeneralNombres = [
                "NOMBRE UNIDAD ADMINISTRATIVA",
                "NOMBRE OFICINA PRODUCTORA",
                "NOMBRE SERIE DOCUMENTAL",
                "NOMBRE SUBSERIE DOCUMENTAL"
            ];

            // Se inicializa la tabla con el pdf y sus estilos
            $table = new EasyTableFpdf($pdf, '{80, 15, 130}', 'width:170; border-color:#000000; font-size:10; border:1; paddingY:2;');

            // Se inicializa el array de los nombre de las unidades
            $arrayGeneralNombres = [
                "NOMBRE UNIDAD ADMINISTRATIVA",
                "NOMBRE OFICINA PRODUCTORA",
                "NOMBRE SERIE DOCUMENTAL",
                "NOMBRE SUBSERIE DOCUMENTAL"
            ];

            // Se recorre el array
            foreach ($array as $key => $col) {

                // Se captura el parametro
                $nombre = isset($col["nombre"]) ? $col["nombre"] : "";
                $codigoUnidad = isset($col["codigo_unidad"]) ? $col["codigo_unidad"] : "";
                $descripcionUnidad = isset($col["descripcion_unidad"]) ? $col["descripcion_unidad"] : "";

                // Se convierte a UTF8 el texto para que permita tildes y demas
                $nombre = iconv('UTF-8', 'windows-1252', $nombre);
                $codigoUnidad = iconv('UTF-8', 'windows-1252', $codigoUnidad);
                $descripcionUnidad = iconv('UTF-8', 'windows-1252', $descripcionUnidad);

                // Se generan las columnas con su información
                $table->easyCell($nombre);
                $table->easyCell($codigoUnidad, 'align:C;');

                // Se da estilo a la ultima columna
                $table->easyCell("<b>" . $arrayGeneralNombres[$key] . "</b> \n" . "<s margin-top: 10;align:{C}; text-align: center; font-style:B; font-size:10; font-color:#000000> $descripcionUnidad </s>");
                $table->printRow();
            }

            // Se cierra la tabla
            $table->endTable(10);

            // Se inicializa la posicion x del documento abajo de la imagen
            $pdf->SetY(150);

            // Arial bold 15
            $pdf->SetFont('Arial', 'B', 14);

            // Movernos a la derecha
            $pdf->Cell(80);

            // Se convierte a UTF8 el texto para que permita tildes y demas
            $str = iconv('UTF-8', 'windows-1252', 'DESCRIPCIÓN DEL EXPEDIENTE');

            // Título
            $pdf->Cell(24, 10, $str, 0, 0, 'C');

            // Salto de línea
            $pdf->Ln(20);

            // Se inicializa la posicion x del documento abajo de la imagen
            $pdf->SetY(170);

            // Se inicializa la cantidad de expedientes
            $arrayExpedienteTituloAntecedentes = [
                "TITULO" => "DETALLE ANTECEDENTES",
            ];

            // Se inicializa la cantidad de expedientes
            $arrayExpedienteTituloInteresados = [
                "TITULO" => "DETALLE INTERESADOS",
            ];

            // Se inicializa la cantidad de expedientes
            $arrayExpedienteTituloEntidadesInvestigado = [
                "TITULO" => "DETALLE ENTIDADES DEL INVESTIGADO",
            ];

            // Se inicializa la tabla con el pdf y sus estilos
            $tableB = new EasyTableFpdf($pdf, '{80, 120}', 'width:170; height:170; border-color:#000000; font-size:10; border:1; paddingY:2;');

            // Se cuenta la cantidad de antecedentes
            $cantidadAntecedentes = count($datosAntecedentes);

            // Se cuenta la cantidad de interesados
            $cantidadInteresados = count($datosInteresados);

            // Se cuenta la cantidad de entidades
            $cantidadEntidades = count($datosEntidades);

            // Se genera la tabla de antecedentes
            foreach ($arrayExpedienteTituloAntecedentes as $k => $col) {

                // Se captura el titulo
                $titulo = $col;
                $titulo = iconv('UTF-8', 'windows-1252', $titulo);

                // Se inicializa la variable
                $nombreGen = "";

                // Se recorre el array de antecedentes
                foreach ($datosAntecedentes as $key => $value) {

                    // Se captura el parametro
                    $fechaRegistro = isset($value["fecha_registro"]) ? $value["fecha_registro"] : "";
                    $descripcion = isset($value["descripcion"]) ? strtoupper($value["descripcion"]) : "";

                    // Se valida para no concadenar cuando son varios
                    if ($cantidadAntecedentes == 1) {

                        // Se concadena
                        $nombreGen = "FECHA REGISTRO: " . $fechaRegistro . " \n DETALLE: " . $descripcion . " \n ";
                    } else {

                        // Se concadena cada vez que encuentra con un salto de linea
                        $nombreGen .= "FECHA REGISTRO: " . $fechaRegistro . " \n DETALLE: " . $descripcion . " \n \n ";
                    }

                    // Se valida cuando la cantidad sea igual al numero de investigados para poner el valor final
                    if (($cantidadAntecedentes - 1) == $key) {

                        // Se genera la fuente a la primera columna
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->SetX(20);

                        // Se generan las columnas con su información
                        $tableB->easyCell($titulo, 'align:C;');
                        $tableB->easyCell($nombreGen, 'align:C;');
                        $tableB->printRow();
                    }
                }
            }

            // Se genera la tabla de la interesados
            foreach ($arrayExpedienteTituloInteresados as $k => $col) {

                // Se captura el titulo
                $titulo = $col;
                $titulo = iconv('UTF-8', 'windows-1252', $titulo);

                // Se inicializa la variable
                $nombreGen = "";

                // Se recorre el array de antecedentes
                foreach ($datosInteresados as $key => $value) {

                    // Se captura el parametro
                    $primerNombre = isset($value["primer_nombre"]) ? $value["primer_nombre"] . " " : "";
                    $segundoNombre = isset($value["segundo_nombre"]) ? $value["segundo_nombre"] . " " : "";
                    $primerApellido = isset($value["primer_apellido"]) ? $value["primer_apellido"] . " " : "";
                    $segundoApellido = isset($value["segundo_apellido"]) ? $value["segundo_apellido"] : "";
                    $nombreCompleto = $primerNombre . $segundoNombre . $primerApellido . $segundoApellido;
                    $ciudadId = isset($value["id_ciudad"]) ? $value["id_ciudad"] : "";
                    $direccion = isset($value["direccion"]) ? $value["direccion"] : "-";
                    $departamentoId = isset($value["id_ciudad"]) ? $value["id_departamento"] : "";
                    $telefonoCelular = isset($value["telefono_celular"]) ? $value["telefono_celular"] : "";

                    // Se busca la informacion de la ciudad
                    $informacionCiudad = CiudadModel::where("id", $ciudadId)->first();
                    $nombreCiudad = isset($informacionCiudad->nombre) ? $informacionCiudad->nombre : "";

                    // Se busca la informacion del departamento
                    $informacionDepartamento = DepartamentoModel::where("id", $departamentoId)->first();
                    $nombreDepartamento = isset($informacionDepartamento->nombre) ? $informacionDepartamento->nombre : "";

                    // Se convierte a UTF8 el texto para que permita tildes y demas
                    $nombreCompleto = iconv('UTF-8', 'windows-1252', $nombreCompleto);
                    $direccionLabel = iconv('UTF-8', 'windows-1252', "DIRECCIÓN: ");
                    $nombreCiudad = iconv('UTF-8', 'windows-1252', $nombreCiudad);
                    $direccion = iconv('UTF-8', 'windows-1252', $direccion);
                    $nombreDepartamento = iconv('UTF-8', 'windows-1252', $nombreDepartamento);

                    // Se valida para no concadenar cuando son varios
                    if ($cantidadInteresados == 1) {

                        // Se concadena
                        $nombreGen = "NOMBRE: " . $nombreCompleto . " \n CIUDAD: " . $nombreCiudad . " / " . $nombreDepartamento . " \n $direccionLabel " . $direccion . " \n CELULAR: " . $telefonoCelular;
                    } else {

                        // Se concadena cada vez que encuentra con un salto de linea
                        $nombreGen .= "NOMBRE: " . $nombreCompleto . " \n CIUDAD: " . $nombreCiudad . " / " . $nombreDepartamento . " \n $direccionLabel " . $direccion . " \n CELULAR: " . $telefonoCelular . " \n \n";
                    }

                    // Se valida cuando la cantidad sea igual al numero de investigados para poner el valor final
                    if (($cantidadInteresados - 1) == $key) {

                        // Se genera la fuente a la primera columna
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->SetX(20);

                        // Se generan las columnas con su información
                        $tableB->easyCell($titulo, 'align:C;');
                        $tableB->easyCell($nombreGen, 'align:C;');
                        $tableB->printRow();
                    }
                }
            }

            // Se genera la tabla de entidades
            foreach ($arrayExpedienteTituloEntidadesInvestigado as $k => $col) {

                // Se captura el titulo
                $titulo = $col;
                $titulo = iconv('UTF-8', 'windows-1252', $titulo);

                // Se inicializa la variable
                $nombreGen = "";

                // Se recorre el array de entidades del investigado
                foreach ($datosEntidades as $key => $value) {

                    // Se captura el parametro
                    $nombreInvestigado = isset($value["nombre_investigado"]) ? strtoupper($value["nombre_investigado"]) : "NO_APLICA";
                    $cargo = isset($value["cargo"]) ? strtoupper($value["cargo"]) : "NO_APLICA";

                    // Se convierte a UTF8 el texto para que permita tildes y demas
                    $nombreInvestigado = iconv('UTF-8', 'windows-1252', $nombreInvestigado);
                    $cargo = iconv('UTF-8', 'windows-1252', $cargo);

                    // Se valida para no concadenar cuando son varios
                    if ($cantidadEntidades == 1) {

                        // Se concadena
                        $nombreGen = "INVESTIGADO: " . $nombreInvestigado . " \n CARGO: " . $cargo;
                    } else {

                        // Se concadena cada vez que encuentra con un salto de linea
                        $nombreGen .= "INVESTIGADO: " . $nombreInvestigado . " \n CARGO: " . $cargo . "\n \n";
                    }

                    // Se valida cuando la cantidad sea igual al numero de investigados para poner el valor final
                    if (($cantidadEntidades - 1) == $key) {

                        // Se genera la fuente a la primera columna
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->SetX(20);

                        // Se generan las columnas con su información
                        $tableB->easyCell($titulo, 'align:C;');
                        $tableB->easyCell($nombreGen, 'align:C;');
                        $tableB->printRow();
                    }
                }
            }

            // Se cierra la tabla
            $tableB->endTable(4);
        }
    }

    // Cabecera de la página
    function cabecera($pdf, $rutaCabecera)
    {
        // Logo
        $pdf->Image($rutaCabecera, 36.5, 10, 130, 38, 'png');

        // Arial bold 15
        $pdf->SetFont('Arial', 'B', 14);

        // Movernos a la derecha
        $pdf->Cell(80);

        // Se convierte a UTF8 el texto para que permita tildes y demas
        $str = iconv('UTF-8', 'windows-1252', 'CARÁTULA SISTEMAS DISCIPLINARIOS');

        // Título
        $pdf->Cell(24, 10, $str, 0, 0, 'C');

        // Salto de línea
        $pdf->Ln(20);
    }

    public function replaceDocumentParamsArchivo($filename, $params, $nombreArchivo)
    {
        // Se captura la fecha
        $año = date("Y");
        $mes = date("m");
        $dia = date("d");
        $hor = date("h");
        $min = date("i");
        $sec = date("s");
        $actuacionesNombreCarpeta = Constants::ACTUACIONES_NOMBRE_CARPETA;
        $temp = $año . $mes . $dia . $hor . $min . $sec . '_' . $nombreArchivo;
        $rutaTemporalBaseDatos = "/files/" . $actuacionesNombreCarpeta . "/" . $temp;

        $pathToSave = storage_path() . '/files/' . $actuacionesNombreCarpeta . "/";
        $pathToFile = $pathToSave . $temp;

        // Si no exista la ruta se crea con permisos
        if (!file_exists($pathToSave)) {
            mkdir($pathToSave, 0777, true);
        }

        $templateProcessor = new TemplateProcessor($filename);

        foreach ($params as $p) {

            if (empty($p["value"]) || $p["value"] == null || $p["value"] == "") {
                $p["value"] = "";
            }

            $templateProcessor->setValue($p["param"], $p["value"]);
        }

        $templateProcessor->saveAs($pathToFile);

        return $rutaTemporalBaseDatos;
    }

    public function get_document_content($filename)
    {
        $zip = new ZipArchive();
        if ($zip->open($filename)) {
            $fp = $zip->getStream('word/document.xml'); //file inside archive
            if (!$fp)
                die("Error: can't get stream to zipped file");
            $stat = $zip->statName('word/document.xml');

            $buf = ""; //file buffer
            ob_start(); //to capture CRC error message
            while (!feof($fp)) {
                $buf .= fread($fp, 2048); //reading more than 2156 bytes seems to disable internal CRC32 verification (bug?)
            }
            $s = ob_get_contents();
            ob_end_clean();
            if (stripos($s, "CRC error") != FALSE) {
                echo 'CRC32 mismatch, current ';
                printf("%08X", crc32($buf)); //current CRC
                echo ', expected ';
                printf("%08X", $stat['crc']); //expected CRC
            }

            fclose($fp);
            $zip->close();
            //Done, unpacked file is stored in $buf
        }

        return $buf;
    }

    /**
     * Retorna plantilla diligenciada con valores de parametros enviados en request
     */
    public function wordDocImages($data)
    {

        // Se inicializa la informacion en variables
        $idActuacion = $data["id_actuacion"];
        $nombreArchivoDocumento = $data["nombre_documento"];
        $nombreArchivoFirma = $data["ruta_image"];
        $nombreUsuario = $data["nombreUsuario"];
        $nombreDependencia = $data["dependenciaUsuario"];
        $nombreFirmado = $data["nombreFirmado"];
        $tamanoFirmado = $data["tamanoFirmado"];
        $nombreFirmadoDocumento = $data["estadoFirmado"];
        $descripcionFirmadoDocumento = $data["descripcionFirmado"];
        $idFirmadoDocumento = $data["idFirmaDocumento"];
        $nombreArchivo = $data["nombreArchivo"];
        $delegadoFirma = $data["delegadoFirma"];

        // Se busca el documento
        if($delegadoFirma){
            $nombreTemplateFirmas = "Plantilla General Parametros Delegado.docx";
        }
        else{
            $nombreTemplateFirmas = "Plantilla General Parametros.docx";
        }

        if($nombreTemplateFirmas == null || $nombreTemplateFirmas == '')
        {
            return [
                    "error" => "El documento general de la firma no existe"
            ];
        }

        if($nombreArchivoDocumento == null || $nombreArchivoDocumento == '')
        {
            return [
                "error" => "El documento a firmar no existe o la ruta no es valida"
            ];
        }
        

        if($nombreArchivoFirma == null || $nombreArchivoFirma == '')
        {
            return [
                "error" => "La imagen de la firma no existe o la ruta no es valida"
            ];
        }
        

        $pathTemplateFirmas = storage_path() . '/files/templates/actuaciones/' . $nombreTemplateFirmas;
        $pathDocumento = storage_path() . $nombreArchivoDocumento;
        $pathImages = storage_path() . '/files/templates/firmas/' . $nombreArchivoFirma;

        // Se captura la fecha
        $fecha = date("Ymdhis");

        // Se valida que exista el documento general de la firma
        if (!file_exists($pathTemplateFirmas)) {
            return [
                "error" => "El documento general de la firma no existe"
            ];
        }

        // Se valida que exista el documento al que se le va a poner la firma
        if (!file_exists($pathDocumento)) {
            return [
                "error" => "El documento a firmar no existe o la ruta no es valida"
            ];
        }

        // Se valida que el archivo de la imagen de la firma exista
        if (!file_exists($pathImages)) {
            return [
                "error" => "La imagen de la firma no existe o la ruta no es valida"
            ];
        }

        /*
            SE EJECUTA LA FUNCIONALIDAD ENCARGADA DE PONER LOS PARAMETROS DENTRO DEL DOCUMENTO
        */

        // Ruta de almacenado
        $actuacionesNombreCarpeta = "/files/" . Constants::ACTUACIONES_NOMBRE_CARPETA . "/";
        $tempZip = $fecha . '_' . $nombreArchivo;
        $pathNuevoZip = storage_path() .  $actuacionesNombreCarpeta . $tempZip;

        // Se unen los documentos con el de la firma principal
        $rutaArchivo = $this->unirDocumentosWord($pathDocumento, $pathTemplateFirmas, $pathNuevoZip, $delegadoFirma);

        // error_log("rutaArchivo -> " . json_encode($rutaArchivo));

        /*
            -------------------------------------------------------------------------
        */

        // Se inicializan las variables nuevamente
        $filename = $rutaArchivo[0]["Ruta"];

        // Se carga el archivo word
        $templateProcessor = new TemplateProcessorExtends($filename);

        // Se sobreescriben los valores
        $templateProcessor->setValue('tipoFirmado', $nombreFirmado, 1);
        $templateProcessor->setValue('nombreUsuario', $nombreUsuario, 1);
        if($delegadoFirma){
            $templateProcessor->setValue('dependenciaDelegado', $nombreDependencia, 1);
        }
        else{
            $templateProcessor->setValue('dependencia', $nombreDependencia, 1);
        }

        // Se captura la fecha del sistema para enviar la hora actual de firmado
        $date = date("Y-m-d h:i:s A");
        $templateProcessor->setValue('fechaFirmado', $date, 1);

        // Se inicializan las variables de ancho y alto
        $width = 0;
        $heigth = 0;

        // Se valida el tipo de imagen para poner su ancho y alto
        /*
            2. Grande
            1. Mediano
            0. Pequeño
        */
        if ($tamanoFirmado == 2 || $tamanoFirmado == 4) {
            $width = 200;
            $heigth = 90;
        } else if ($tamanoFirmado == 1) {
            $width = 160;
            $heigth = 70;
        } else if ($tamanoFirmado == 0) {
            $width = 120;
            $heigth = 50;
        }

        // Se añade la imagen
        $templateProcessor->setImageValue(
            'imagen',
            [
                'path' => $pathImages,
                'width' => $width,
                'height' => $heigth,
                'ratio' => false,
            ],
            1
        );

        // Se captura la fecha nuevamente
        $fecha = date("Ymdhis");

        // Ruta de almacenado
        $actuacionesNombreCarpeta = "/files/" . Constants::ACTUACIONES_NOMBRE_CARPETA . "/";
        $temp = $fecha . '_' . $nombreArchivo;
        $pathNuevo = storage_path() .  $actuacionesNombreCarpeta . $temp;
        $documentoRutaTablas = $actuacionesNombreCarpeta . $temp;

        // Se guarda el documento
        $templateProcessor->saveAs($pathNuevo);

        // Se elimina el archivo antiguo de word
        if (unlink($pathDocumento)) {
            error_log("Se elimino el word antiguo");
        }

        // Se retorna la ruta del array del pdf y word para eliminarlos
        $links = [
            "word" => $documentoRutaTablas,
            "data" => "ok",
        ];

        // Se actualiza la ruta del documento en la tabla de actuaciones y en la tabla de trazabilidad del archivo de las actuaciones
        ArchivoActuacionesModel::where('uuid_actuacion', $idActuacion)->update(['documento_ruta' => $documentoRutaTablas]);
        ActuacionesModel::where("uuid", $idActuacion)->update(['documento_ruta' => $documentoRutaTablas]);

        // Se crea los datos para la tabla de trazabilidad de las actuaciones
        $datosRequestTrazabilidad["uuid_actuacion"] = $idActuacion;
        $datosRequestTrazabilidad["id_estado_actuacion"] = $idFirmadoDocumento;
        $datosRequestTrazabilidad["observacion"] = "El usuario " . $nombreUsuario . " ha añadido la firma tipo $nombreFirmado. (" . $descripcionFirmadoDocumento . ").";
        $datosRequestTrazabilidad["estado"] = true;
        $datosRequestTrazabilidad['created_user'] = auth()->user()->name;
        $datosRequestTrazabilidad['id_dependencia'] = auth()->user()->id_dependencia;

        // Se manda el array del modelo con su informacion para crearlo en su tabla
        $TrazabilidadActuacionesModel = new TrazabilidadActuacionesModel();
        TrazabilidadActuacionesResource::make($TrazabilidadActuacionesModel->create($datosRequestTrazabilidad));

        // Se guarda la ejecucion con un commit para que se ejecute
        DB::connection()->commit();

        // Se retorna el valor de la ruta
        return $links;
    }

    /**
     * Metodo encargado de generar la segunda caratula dentro de ramas del proceso
     */
    public function generarCaratula($arrayInformacionProcesoDisciplinario = [])
    {
        // Se genera un uuid de 16 hex
        $guid =  bin2hex(openssl_random_pseudo_bytes(16));
        $nombrePdf = $guid . '.pdf';

        // Se genera la ruta donde se guardara el documento pdf
        $pathToSave = storage_path() . '/files/temp/';
        $pathToFile = $pathToSave . $nombrePdf;
        $rutaCabecera = storage_path() . '/files/templates/caratulas/cabecera.png';

        // Se valida que el archivo de la imagen de la cabecera exista
        if (!file_exists($rutaCabecera)) {
            return [
                "error" => "La imagen de la cabecera no existe o la ruta no es valida"
            ];
        }

        // Se consulta la informacion de tipos de unidades
        $arrayInformacion = TipoUnidadModel::where("estado", "1")->get();

        // Se inicializa la clase FPDF
        $pdf = new ExFPDF();

        // Se añade una pagina
        $pdf->AddPage();
        $pdf->SetY(55);

        // Se crea la cabecera
        $this->cabecera($pdf, $rutaCabecera);

        // Se inicializa la posicion x del documento abajo de la imagen
        $pdf->SetY(75);

        // Se captura la informacion del expediente
        $informacionGeneralProcesoDisciplinario = isset($arrayInformacionProcesoDisciplinario["informacionGeneralProcesoDisciplinario"][0]) ? $arrayInformacionProcesoDisciplinario["informacionGeneralProcesoDisciplinario"] : [];
        $informacionAntecedentes = isset($arrayInformacionProcesoDisciplinario["informacionAntecedentes"][0]) ? $arrayInformacionProcesoDisciplinario["informacionAntecedentes"] : [];
        $informacionInteresadosInvestigado = isset($arrayInformacionProcesoDisciplinario["informacionInteresadosInvestigado"][0]) ? $arrayInformacionProcesoDisciplinario["informacionInteresadosInvestigado"] : [];
        $informacionEntidadInvestigado = isset($arrayInformacionProcesoDisciplinario["informacionEntidadInvestigado"][0]) ? $arrayInformacionProcesoDisciplinario["informacionEntidadInvestigado"] : [];

        // Se genera la tabla
        $this->tablaPdf($pdf, $arrayInformacion, 2, $informacionAntecedentes, $informacionInteresadosInvestigado, $informacionEntidadInvestigado);

        // Se guarda el pdf
        $pdf->Output('F', storage_path() . '/files/temp/' . $nombrePdf, true);

        // Se retorna la ruta
        return [
            "pdf" => $pathToFile
        ];
    }

    public function unirDocumentosWord($documentoInicial, $documentoSegundo, $nombreDocumentiFinal, $delegadoFirma)
    {
        // Documentos principales
        $pathToFile = $documentoInicial;
        $pathToTemplateSignature = $documentoSegundo;

        // Nombre del nuevo documento generado
        $pathToNewFile = $nombreDocumentiFinal;
        $rutaArchivo = [];

        // Clase
        $zip = new clsTbsZip();

        // Abre el primer documento
        $zip->Open($pathToTemplateSignature);
        $content1 = $zip->FileRead('word/document.xml');
        $zip->Close();

        // Extrae el contenido del primer documento
        $p = strpos($content1, '<w:body');
        if ($p === false) exit("Tag <w:body> not found in document 1.");
        $p = strpos($content1, '>', $p);
        $content1 = substr($content1, $p + 1);
        $p = strpos($content1, '</w:body>');
        if ($p === false) exit("Tag </w:body> not found in document 1.");
        $content1 = substr($content1, 0, $p);
        $content1 = explode("<w:sectPr", $content1);

        // Inserta el contenido del segundo documento en el primer documento
        $zip->Open($pathToFile);
        $content2 = $zip->FileRead('word/document.xml');
        //$p = strpos($content2, '</w:body>');
        $p = strpos($content2, '<w:sectPr');
        if ($p === false) exit("Tag </w:body> not found in document 2.");
        if($delegadoFirma){
            $content2 = str_replace("<w:t>INSERTAR_FIRMA_DELEGADO</w:t>", $content1[0], $content2);
        }
        else{
            $content2 = substr_replace($content2, $content1[0], $p, 0);
        }
        $zip->FileReplace('word/document.xml', $content2, TBSZIP_STRING);

        // Save the merge into a third file
        $zip->Flush(TBSZIP_FILE, $pathToNewFile);
        // Se valida que la ruta no sea nula o vacia
        if (!empty($pathToNewFile)) {

            // Se añade al array
            array_push(
                $rutaArchivo,
                [
                    "Ruta" => $pathToNewFile,
                    "RetornoXml" => $content2
                ]
            );
        }

        // Se retorna la informacion de la ruta
        return $rutaArchivo;
    }
}
