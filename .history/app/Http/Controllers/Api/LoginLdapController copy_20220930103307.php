<?php

namespace App\Http\Controllers\Api;

use App\Repositories\RepositoryGeneric;
use App\Http\Controllers\Controller;
use Exception;
use App\Models\UsuarioRolModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Exceptions\HttpResponseException;

class MigracionController extends Controller
{

  private $repository;

  public function __construct(RepositoryGeneric $repository)
  {
  }
  public function index(Request $request)
  {
    return null;
  }

  /**
   *
   */
  public function validarDocumentoRegistraduria($request)
  {
    $siValidoSinproc = false;
    $numeroDocumento = $request;
    //error_log('$numeroDocumento ' . $numeroDocumento);
    try {

      $timeout = env("API_REGISTRADURIA_TIMEOUT", 4);
      $url = env("API_REGISTRADURIA_URL") . $numeroDocumento;
      $resultado["url"] = $url;
      $response = Http::timeout($timeout)->get($url);
      // error_log('Encontre en registraduria');

      if ($response->failed() || $response["return"]["datosCedulas"]["datos"]["codError"]) {
        $siValidoSinproc = true;
      } else {
        return $response;
      }
    } catch (Exception  $e) {
      error_log($e);
      $resultado['error_message'] = $e->getMessage();
      $resultado['error_trace'] = $e->getTrace();
      $siValidoSinproc = true;
    }

    try {

      if ($siValidoSinproc) {
        // error_log('NO Encontre en registraduria');
        $responseSinproc = $this->validarDocumentoSINPROC($numeroDocumento);
        $resultado["tipo"] = "sinproc";
        $resultado["return"] = $responseSinproc;
        //error_log(json_encode($resultado));
        return json_encode($resultado);
      }
    } catch (Exception  $e) {
      error_log($e);
    }

    return null;
  }

  public function validarDocumentoSINPROC($documento)
  {
    try {
      //$datosRequest = $request->validated();
      //INICIO PROCESO DE VALIDACION
      //1. Validación por SINPROC
      $repository_usuarioRol = new RepositoryGeneric();
      $repository_usuarioRol->setModel(new UsuarioRolModel());
      $querySinproc = $repository_usuarioRol->customQuery(function ($model) use ($documento) {
        return
          $model->where('cedula', $documento)->take(1)->get();
      });

      if (!empty($querySinproc[0])) {
        return $querySinproc[0];
      }
    } catch (Exception  $e) {
      error_log($e);
    }

    return "";
  }
}
