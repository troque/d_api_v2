<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\UtilidadesTrait;
use App\Http\Requests\UsuarioFormRequest;
use App\Http\Requests\UsuarioSearchFormRequest;
use App\Http\Resources\Role\RoleResource;
use App\Http\Resources\Usuario\UsuarioCollection;
use App\Http\Resources\Usuario\UsuarioResource;
use App\Http\Resources\Usuario\UsuarioRolesResource;
use App\Http\Resources\Usuario\UsuarioRolResource;
use App\Http\Resources\Usuario\UsuarioTipoExpedienteResource;
use App\Http\Utilidades\Constants;
use App\Models\DependenciaOrigenModel;
use App\Models\ProcesoDiciplinarioModel;
use App\Models\User;
use App\Models\UserRolesModel;
use App\Models\UserTipoExpedienteModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    private $repository;
    use UtilidadesTrait;

    public function __construct(
        RepositoryGeneric $repository,
    ) {
        $this->repository = $repository;
        $this->repository->setModel(new User());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        return UsuarioCollection::make($this->repository->paginate($request->limit ?? 100000));
    }

    public function indexPaginate($paginaActual, $porPagina)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($porPagina, $paginaActual) {
                return User::orderBy('nombre', 'asc')->paginate($porPagina, ['*'], 'usuarios', $paginaActual);
            }
        );
        return  UsuarioCollection::make($query);
    }

    public function getUsarios($estado)
    {
        $query = $this->repository->customQuery(
            function ($model) use ($estado) {
                return $model->where("estado", $estado)->orderBy("nombre", "asc")->get();
            }
        );

        return UsuarioCollection::make($query);
    }

    public function getAllUsuarios()
    {
        $query = $this->repository->customQuery(
            function ($model) {
                return $model->orderBy("nombre", "asc")->get();
            }
        );

        return UsuarioCollection::make($query);
    }


    /**
     * Store a newly created department in storage.
     *
     * @param  App\Http\Requests\UsuarioFormRequest  $request
     * @return App\Http\Resources\Usuario\UsuarioResource
     */
    public function store(UsuarioFormRequest $request)
    {
        try {
            DB::connection()->beginTransaction();
            $datos = $request->validated()["data"]["attributes"];
            //buscamos si no hay otro ya registrado

            $queryYaExiste = $this->repository->customQuery(function ($model) use ($datos) {
                return
                    $model->where('name', $datos["name"])
                    ->orWhere('email', $datos["email"])
                    ->get();
            });

            if (!empty($queryYaExiste[0])) {
                $error['estado'] = false;
                $error['error'] = 'El nombre usuario ' . $datos["name"] . ' o el correo ' . $datos["email"] . ' ya existe en el sistema, digite otro por favor.';
                return json_encode($error);
            }

            // Validar el array de las etapas
            $arrayGrupos = [];

            // Se validan que existan etapas
            if (isset($datos["id_mas_grupo_trabajo_secretaria_comun"])) {
                error_log(json_encode($datos["id_mas_grupo_trabajo_secretaria_comun"]));
                $grupos = $datos["id_mas_grupo_trabajo_secretaria_comun"];

                // Se recorren las etapas seleccionadas
                foreach ($grupos as $key => $value) {

                    // Se captura el id de la etapa
                    $id_grupo = $value["value"];

                    // Se añaden las etapas
                    array_push($arrayGrupos, $id_grupo);
                }

                // Se separa el array por comas
                $datosUusario["id_mas_grupo_trabajo_secretaria_comun"] = implode(",", $arrayGrupos);
            }


            $datosUusario["name"] = $datos["name"];
            $datosUusario["email"] = $datos["email"];
            $datosUusario["nombre"] = $datos["nombre"];
            $datosUusario["apellido"] = $datos["apellido"];
            $datosUusario["id_dependencia"] = $datos["id_dependencia"];
            $datosUusario["identificacion"] = $datos["identificacion"];
            //$datosUusario["id_mas_grupo_trabajo_secretaria_comun"] = $datos["id_mas_grupo_trabajo_secretaria_comun"];
            $datosUusario["estado"] = $datos["estado"];
            $datosUusario["reparto_habilitado"] = $datos["reparto_habilitado"];
            $datosUusario["numero_casos"] = 0;
            $datosUusario["nivelacion"] = 0;

            $users = DB::select(
                '
                SELECT
                    ROUND(AVG(u.numero_casos),1) as promedio
                FROM
                    users u
                WHERE u.id_dependencia = ' . $datosUusario["id_dependencia"] . '
                AND u.estado = 1
                AND u.reparto_habilitado = 1
                '
            );

            if (count($users) > 0) {
                $datosUusario["nivelacion"] = $users[0]->promedio;
            }

            $respuestaUsuario = UsuarioResource::make($this->repository->create($datosUusario));

            //insertamos los roles

            foreach ($datos["roles"] as $rol) {

                $userRolModel = new UserRolesModel();

                $userRol["user_id"] = $respuestaUsuario["id"];
                $userRol["role_id"] = $rol;
                UsuarioRolResource::make($userRolModel->create($userRol));
            }

            //ACUTALIZAMOS LOS TIPOS DE EXPEDIENTE
            if ($datos["expedientes"]) {
                foreach ($datos["expedientes"] as $expediente) {

                    $userExpModel = new UserTipoExpedienteModel();
                    $cadena =  explode("|", $expediente);;
                    $userExpediente["user_id"] = $respuestaUsuario["id"];
                    $userExpediente["tipo_expediente_id"] = $cadena[0];
                    $userExpediente["sub_tipo_expediente_id"] = $cadena[1];
                    UsuarioTipoExpedienteResource::make($userExpModel->create($userExpediente));
                }
            }

            DB::connection()->commit();
            return $respuestaUsuario;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // Se valida en caso que el id sea undefined
        if (empty($id) || $id == "undefined") {

            // Se captura el id del usuario desde el backend
            $id = auth()->user()->id;
        }

        $user = UsuarioRolesResource::make($this->repository->find($id));

        $query = DB::select(DB::raw("select tipo_expediente_id, sub_tipo_expediente_id from users_tipo_expediente e where e.user_id = :userId"), array(
            'userId' => $id,
        ));

        $myArray = array();
        //error_log(json_encode($query));
        if (!empty($query[0])) {


            foreach ($query as $r) {

                array_push($myArray, $r->tipo_expediente_id . '|' . $r->sub_tipo_expediente_id);
            }
            //error_log(json_encode($myArray));
            $user["ids_tipo_expediente"] = $myArray;
        }


        return $user;
    }


    public function getUserByFunctionality(UsuarioFormRequest $request)
    {
        $datos = $request->validated()["data"]["attributes"];

        $query = $this->repository->customQuery(function ($model) use ($datos) {
            return $model
                ->leftJoin('USERS_ROLES', 'Users.id', '=', 'USERS_ROLES.USER_ID')
                ->leftJoin('FUNCIONALIDAD_ROL', 'USERS_ROLES.ROLE_ID', '=', 'FUNCIONALIDAD_ROL.ROLE_ID')
                ->leftJoin('MAS_FUNCIONALIDAD', 'FUNCIONALIDAD_ROL.FUNCIONALIDAD_ID', '=', 'MAS_FUNCIONALIDAD.ID')
                ->leftJoin('MAS_MODULO', 'MAS_FUNCIONALIDAD.ID_MODULO', '=', 'MAS_MODULO.ID')
                ->where('MAS_FUNCIONALIDAD.NOMBRE', $datos["nombre_funcionalidad"])
                ->where('MAS_MODULO.nombre', $datos["nombre_modulo"])
                ->select('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'Users.estado')
                ->get();
        });

        return UsuarioCollection::make($query);
    }

    public function getUsuariosPorDependencia($idDependencia, $idSubTipoExpediente, $idTipoExpediente)
    {

        // error_log('$idDependencia = ' . $idDependencia);
        // error_log('$idSubTipoExpediente = ' . $idSubTipoExpediente);
        // error_log('$idTipoExpediente = ' . $idTipoExpediente);

        $query = $this->repository->customQuery(function ($model) use ($idDependencia, $idSubTipoExpediente, $idTipoExpediente) {
            return $model->where('id_dependencia', $idDependencia)
                ->leftJoin('users_tipo_expediente', 'users_tipo_expediente.USER_ID', '=', 'Users.id')
                ->where('users_tipo_expediente.TIPO_EXPEDIENTE_ID', $idTipoExpediente)
                ->where('users_tipo_expediente.SUB_TIPO_EXPEDIENTE_ID', $idSubTipoExpediente)
                ->select('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'Users.estado')
                ->get();
        });


        return UsuarioCollection::make($query);
    }

    public function getTodosLosUsuariosPorDependencia($idDependencia)
    {
        $query = $this->repository->customQuery(function ($model) use ($idDependencia) {
            return $model->where('id_dependencia', $idDependencia)
                ->where('estado', 1)
                ->select('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'Users.estado')
                ->get();
        });


        return UsuarioCollection::make($query);
    }

    public function getUsuarioPorName($name)
    {
        $query = $this->repository->customQuery(function ($model) use ($name) {
            return $model->where('name', $name)
                ->where('estado', 1)
                ->select(
                    'Users.id',
                    'Users.name',
                    'Users.email',
                    'Users.nombre',
                    'Users.firma_mecanica',
                    'Users.password_firma_mecanica',
                    'Users.id_dependencia',
                    'Users.estado',
                    'Users.id_mas_grupo_trabajo_secretaria_comun'
                )
                ->get();
        });


        return UsuarioCollection::make($query);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(UsuarioFormRequest $request,  $id)
    {

        try {

            DB::connection()->beginTransaction();
            $datos = $request->validated()["data"]["attributes"];

            if ($datos["estado"] == '0') {
                $error = "";
                $error = $this->tieneCasosActivos($datos["name"]);

                //verificamos si el usuario tiene casos asignados
                $obj = json_decode($error);
                if (!empty($obj->{'error'})) {
                    // error_log('entro');
                    return $error;
                }
            }

            // Validar el array de las etapas
            $arrayGrupos = [];

            // Se validan que existan etapas
            if (isset($datos["id_mas_grupo_trabajo_secretaria_comun"])) {
                error_log(json_encode($datos["id_mas_grupo_trabajo_secretaria_comun"]));
                $grupos = $datos["id_mas_grupo_trabajo_secretaria_comun"];

                // Se recorren las etapas seleccionadas
                foreach ($grupos as $key => $value) {

                    // Se captura el id de la etapa
                    $id_grupo = $value["value"];

                    // Se añaden las etapas
                    array_push($arrayGrupos, $id_grupo);
                }

                // Se separa el array por comas
                $datosUusario["id_mas_grupo_trabajo_secretaria_comun"] = implode(",", $arrayGrupos);
            }

            $datosUusario["email"] = $datos["email"];
            $datosUusario["nombre"] = $datos["nombre"];
            $datosUusario["apellido"] = $datos["apellido"];
            $datosUusario["id_dependencia"] = $datos["id_dependencia"];
            //$datosUusario["id_mas_grupo_trabajo_secretaria_comun"] = $datos["id_mas_grupo_trabajo_secretaria_comun"];
            $datosUusario["estado"] = $datos["estado"];
            $datosUusario["reparto_habilitado"] = $datos["reparto_habilitado"];

            $user = $this->repository->find($id);

            if ($user->id_dependencia != $datosUusario["id_dependencia"]) {

                $user_casos_asignados = DB::select("
                    SELECT
                        id_funcionario_actual
                    FROM
                        log_proceso_disciplinario lpd
                    WHERE lpd.id_funcionario_actual = '" . $datos["name"] . "'
                ");

                if (count($user_casos_asignados) > 0) {
                    DB::connection()->rollBack();
                    $error['estado'] = false;
                    $error['error'] = 'NO ES POSIBLE CAMBIAR DE DEPENDENCIA, EL USUARIO ' . $datos["nombre"] . ' ' . $datos["apellido"] . ' AUN CUENTA CON CASOS ACTIVOS';
                    return json_encode($error);
                }
            }

            if (
                ($user->estado == '0' && $datosUusario["estado"] == '1') ||
                (
                    ($user->estado == '1' || $datosUusario["estado"] == '1') &&
                    $user->reparto_habilitado == '0' && $datosUusario["reparto_habilitado"] == '1'
                )
            ) {
                $users = DB::select('
                    SELECT
                        ROUND(AVG(u.numero_casos),1) as promedio
                    FROM
                        users u
                    WHERE u.id_dependencia = ' . $datosUusario["id_dependencia"] . '
                    AND u.id <> ' . $id . '
                    AND u.estado = 1
                    AND u.reparto_habilitado = 1
                ');

                $user_casos_asignados = DB::select('
                    SELECT
                        (SELECT COUNT(*) FROM log_proceso_disciplinario WHERE id_funcionario_actual = u.name) AS casos_asginados
                    FROM
                        users u
                    WHERE u.id_dependencia = ' . $datosUusario["id_dependencia"] . '
                    AND u.id = ' . $id . '
                ');

                if (count($users) > 0) {
                    $datosUusario["nivelacion"] = $users[0]->promedio - $user_casos_asignados[0]->casos_asginados;
                    error_log("Nivelacion = " . $users[0]->promedio . " - " . $user_casos_asignados[0]->casos_asginados);
                    if ($datosUusario["nivelacion"] < 0) {
                        $datosUusario["nivelacion"] = $user_casos_asignados[0]->casos_asginados;
                    }
                    $datosUusario["numero_casos"] = $datosUusario["nivelacion"] + $user_casos_asignados[0]->casos_asginados;
                }
            }

            if ($datos["roles"]) {

                $respuestaUsuario = UsuarioResource::make($this->repository->update($datosUusario, $id));

                //insertamos los roels
                DB::delete(DB::raw("delete from USERS_ROLES where USER_ID = :somevariable"), array(
                    'somevariable' => $id,
                ));

                //foreach (array_unique($datos["roles"]) as $rol) {
                foreach ($datos["roles"] as $rol) {

                    $userRolModel = new UserRolesModel();

                    $userRol["user_id"] = $respuestaUsuario["id"];
                    $userRol["role_id"] = $rol;
                    UsuarioRolResource::make($userRolModel->create($userRol));
                }
            }


            //ACUTALIZAMOS LOS TIPOS DE EXPEDIENTE

            if ($datos["expedientes"]) {
                DB::delete(DB::raw("delete from USERS_TIPO_EXPEDIENTE where USER_ID = :somevariable"), array(
                    'somevariable' => $id,
                ));
                foreach ($datos["expedientes"] as $expediente) {

                    $userExpModel = new UserTipoExpedienteModel();
                    $cadena =  explode("|", $expediente);;
                    $userExpediente["user_id"] = $respuestaUsuario["id"];
                    $userExpediente["tipo_expediente_id"] = $cadena[0];
                    $userExpediente["sub_tipo_expediente_id"] = $cadena[1];
                    UsuarioTipoExpedienteResource::make($userExpModel->create($userExpediente));
                }
            } else {
                // error_log("VACIO");
                DB::table('USERS_TIPO_EXPEDIENTE')->where('USER_ID', $id)->delete();
            }

            DB::connection()->commit();
            return $respuestaUsuario;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $this->repository->delete($id);
        return response()->noContent();
    }


    public function tieneCasosActivos($usuario)
    {

        $repository_proceso = new RepositoryGeneric();
        $repository_proceso->setModel(new ProcesoDiciplinarioModel());

        $query = $repository_proceso->customQuery(function ($model) use ($usuario) {

            return $model->whereRaw('
                    uuid in (
                        select id_proceso_disciplinario from (
                        select lpd.id_proceso_disciplinario, max(lpd.id_funcionario_actual) keep (dense_rank first order by lpd.created_at desc) id_funcionario_actual
                        from log_proceso_disciplinario lpd
                        where lpd.id_tipo_log=1
                        group by lpd.id_proceso_disciplinario
                        ) log
                        where log.ID_FUNCIONARIO_ACTUAL = ? )', $usuario)->get();
        });



        if (!empty($query[0])) {
            $error['estado'] = false;
            $error['error'] = 'No es posible inactivar el usuario, tiene casos activos asociados.';
            return json_encode($error);
        }

        return  json_encode('');
    }


    public function getUsuarioFilter(UsuarioSearchFormRequest $request)
    {

        $datosRequest = $request->validated()["data"]["attributes"];

        $query = User::query();

        if (!empty($datosRequest['nombre']) && $datosRequest['nombre'] != "-1") {
            $query = $query->whereRaw("Translate(upper(nombre),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') like '%" . strtoupper($this->eliminar_tildes($datosRequest['nombre'])) . "%'");
        }
        if (!empty($datosRequest['apellido']) && $datosRequest['apellido'] != "-1") {
            $query = $query->whereRaw("Translate(upper(apellido),'ÁáÉéÍíÓóÚú','AaEeIiOoUu') like '%" . strtoupper($this->eliminar_tildes($datosRequest['apellido'])) . "%'");
        }

        $query = $query->select('users.id', 'users.name', 'users.email', 'users.nombre', 'users.apellido', 'users.id_dependencia')
            ->where('users.id_dependencia', $datosRequest['id_dependencia'])
            ->orderBy('users.NAME', 'asc')->get();

        if (empty($query[0])) {
            $error['estado'] = false;
            $error['error'] = 'No se encontro información relacionada';
            return json_encode($error);
        }

        return UsuarioCollection::make($query);
    }


    /**
     * Se consulta cu
     */
    public function validarJefeDependencia()
    {
        $jefe_dependiencia = DB::select("select id_usuario_jefe from mas_dependencia_origen where id_usuario_jefe = " . auth()->user()->id . "");

        if ($jefe_dependiencia != null) {

            $query = $this->repository->customQuery(function ($model) use ($jefe_dependiencia) {
                return $model
                    ->where('users.id', $jefe_dependiencia[0]->id_usuario_jefe)
                    ->select(
                        'users.id',
                        'users.name',
                        'users.email',
                        'users.nombre',
                        'users.apellido',
                        'users.id_dependencia',
                        'users.estado',
                    )
                    ->get();
            });

            return UsuarioCollection::make($query);
        } else {

            $error['estado'] = false;
            $error['error'] = 'El usuario no es jefe de ninguna dependencia';
            return json_encode($error);
        }
    }


    public function obtenerJefeDeMiDependencia()
    {
        // error_log("obtenerJefeDeMiDependencia usuario: " . auth()->user()->name);
        $user = User::where("name", auth()->user()->name)->first();
        $dependencia = DependenciaOrigenModel::find($user->id_dependencia);
        if ($dependencia != null && $dependencia->id_usuario_jefe != null) {
            $user_jefe = User::find($dependencia->id_usuario_jefe);
            return UsuarioResource::make($user_jefe);
        } else {

            $error['estado'] = false;
            $error['error'] = 'La dependencia no tiene jefe asignado';
            return json_encode($error);
        }
    }


    public function actualizar_firma(UsuarioFormRequest $request,  $id)
    {
        try {
            // Se inicializa la conexion
            DB::connection()->beginTransaction();

            // Se capturan los datos
            $datosRequest = $request->validated()["data"]["attributes"];

            // Validando contraseña
            $longitud = strlen($datosRequest["password_firma_mecanica"]);
            $tieneMayuscula = preg_match("/[A-Z]/", $datosRequest["password_firma_mecanica"]);
            $tieneMinuscula = preg_match("/[a-z]/", $datosRequest["password_firma_mecanica"]);
            $tieneNumero = preg_match("/\d/", $datosRequest["password_firma_mecanica"]);

            $error['mensaje1'] = '* La contraseña debe de contener mínimo 8 caracteres.';
            $error['mensaje2'] = '* La contraseña debe de contener al menos una mayúscula.';
            $error['mensaje3'] = '* La contraseña debe de contener al menos una minúscula.';
            $error['mensaje4'] = '* La contraseña debe de contener al menos un número.';

            if ($longitud > 8) {

                if ($tieneMayuscula) {

                    if ($tieneMinuscula) {

                        if ($tieneNumero) {
                            // $datosUusario["password_firma_mecanica"] = $datosRequest["password_firma_mecanica"];
                        } else {
                            $error['estado'] = false;
                            $error['error'] = 'La contraseña debe de contener al menos un número';
                            return json_encode($error);
                        }
                    } else {
                        $error['estado'] = false;
                        $error['error'] = 'La contraseña debe de contener al menos una minúscula';
                        return json_encode($error);
                    }
                } else {
                    $error['estado'] = false;
                    $error['error'] = 'La contraseña debe de contener al menos una mayúscula';
                    return json_encode($error);
                }
            } else {
                $error['estado'] = false;
                $error['error'] = 'Por favor ingrese una contraseña válida';
                return json_encode($error);
            }

            if (isset($datosRequest['firma_mecanica'])) {

                $baseFolderPath = storage_path() . '/files/templates/firmas/';

                $datosRequest['firma_mecanica'] = str_replace(".png", "_" . substr($this->GUID(), 0, 6) . ".png", $datosRequest['firma_mecanica']);
                //dd($datosRequest['firma_mecanica']);

                $path = $baseFolderPath . $datosRequest['firma_mecanica'];

                $b64 = $datosRequest['firma_mecanica_fileBase64'];
                $bin = base64_decode($b64, true);

                file_put_contents($path, $bin);
            }

            // cifrar password
            $datosRequest["password_firma_mecanica"] = crypt($datosRequest["password_firma_mecanica"], Constants::SALT['salt']);

            error_log("Firma" . $datosRequest["password_firma_mecanica"]);
            $respuestaUsuario = UsuarioResource::make($this->repository->update($datosRequest, $id));

            DB::connection()->commit();
            return $respuestaUsuario;
        } catch (\Exception $e) {
            error_log($e);
            // Woopsy
            DB::connection()->rollBack();
            return response()->json(array(
                'code'      =>  500,
                'message'   =>  $e->getMessage()
            ), 500);
        }
    }


    public function getFirmaMecanica($id)
    {
        $firma = $this->repository->find($id);

        $baseFolderPath = storage_path() . '/files/templates/firmas/';
        $path = $baseFolderPath . $firma->firma_mecanica;

        $datos['file_name'] = $firma->firma_mecanica;
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["base_64"] = base64_encode(file_get_contents($path));

        return response()->json($datos);
    }


    public function getFirmaMecanicaEjemplo()
    {

        $baseFolderPath = storage_path() . '/files/templates/firmas/';
        $path = $baseFolderPath . "firmaEjemplo.png";

        $datos['file_name'] = "firmasEjemplo.png";
        $datos['content_type'] = "application/vnd.openxmlformats-officedocument.wordprocessingml.document";
        $datos["base_64"] = base64_encode(file_get_contents($path));

        return response()->json($datos);
    }

    private function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }

        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
    }

    public function getTodosLosUsuariosPorGrupoTrabajo($idGrupoTrabajo, $id_proceso_disciplinario)
    {
        $query = $this->repository->customQuery(function ($model) use ($idGrupoTrabajo) {
            return $model->where('ID_MAS_GRUPO_TRABAJO_SECRETARIA_COMUN', 'like', '%' . $idGrupoTrabajo . '%')
                ->where('estado', Constants::ESTADOS['activo'])
                ->where('reparto_habilitado', Constants::ESTADOS['activo'])
                ->select('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'users_tipo_expediente.sub_tipo_expediente_id')
                ->leftJoin('users_tipo_expediente', 'Users.id', 'users_tipo_expediente.user_id')
                ->where('users_tipo_expediente.tipo_expediente_id', Constants::TIPO_EXPEDIENTE['proceso_disciplinario'])
                ->get();
        });

        $proceso_disciplinario = DB::select(
            "
                SELECT
                    cr.id_tipo_expediente,
                    cr.id_tipo_queja,
                    cr.created_at
                FROM
                proceso_disciplinario pd
                INNER JOIN clasificacion_radicado cr ON pd.uuid = cr.id_proceso_disciplinario
                WHERE pd.uuid = '$id_proceso_disciplinario'
                ORDER BY cr.created_at DESC
            "
        );

        if (count($proceso_disciplinario) <= 0) {
            $error['estado'] = false;
            $error['error'] = 'ERROR AL MOMENTO DE OBTENER INFORMACIÓN DEL PROCESO.';
            return json_encode($error);
        }

        $tipoQuejaEliminar = '';

        if ($proceso_disciplinario[0]->id_tipo_queja == Constants::TIPO_QUEJA['externa']) {
            $tipoQuejaEliminar = Constants::TIPO_QUEJA['interna'];
        } else {
            $tipoQuejaEliminar = Constants::TIPO_QUEJA['externa'];
        }

        $query = $query->reject(function ($item) use ($tipoQuejaEliminar) {
            return $item->sub_tipo_expediente_id == $tipoQuejaEliminar;
        });


        return UsuarioCollection::make($query);
    }

    public function getTodosLosUsuariosPorDependenciaActuaciones($idDependencia, $id_proceso_disciplinario)
    {

        $proceso_disciplinario = DB::select(
            "
                SELECT
                    cr.id_tipo_expediente,
                    cr.id_tipo_queja,
                    cr.created_at
                FROM
                    proceso_disciplinario pd
                INNER JOIN clasificacion_radicado cr ON pd.uuid = cr.id_proceso_disciplinario
                WHERE pd.uuid = '$id_proceso_disciplinario'
                ORDER BY cr.created_at DESC
            "
        );

        if (count($proceso_disciplinario) <= 0) {
            $error['estado'] = false;
            $error['error'] = 'ERROR AL MOMENTO DE OBTENER INFORMACIÓN DEL PROCESO.';
            return json_encode($error);
        }

        $query = $this->repository->customQuery(function ($model) use ($idDependencia, $proceso_disciplinario) {
            return $model->where('id_dependencia', $idDependencia)
                ->where('estado', Constants::ESTADOS['activo'])
                ->where('reparto_habilitado', Constants::ESTADOS['activo'])
                ->select('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'Users.estado')
                ->leftJoin('users_tipo_expediente', 'Users.id', 'users_tipo_expediente.user_id')
                ->where('users_tipo_expediente.tipo_expediente_id', Constants::TIPO_EXPEDIENTE['proceso_disciplinario'])
                ->where('users_tipo_expediente.sub_tipo_expediente_id', $proceso_disciplinario[0]->id_tipo_queja)
                ->get();
        });


        return UsuarioCollection::make($query);
    }

    public function getAllUsuariosPorDependencia($idDependencia)
    {
        $query = $this->repository->customQuery(function ($model) use ($idDependencia) {
            return $model->where('id_dependencia', $idDependencia)
                ->leftJoin('users_tipo_expediente', 'users_tipo_expediente.USER_ID', '=', 'Users.id')
                ->where('Users.estado', Constants::ESTADOS['activo'])
                ->where('Users.reparto_habilitado', Constants::ESTADOS['activo'])
                ->select('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'Users.estado')
                ->groupBy('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'Users.estado')
                ->get();
        });

        return UsuarioCollection::make($query);
    }

    public function getTodosLosUsuariosPorDependenciaPermisosDisciplinarios($id_proceso_disciplinario, $idDependencia)
    {

        $proceso_disciplinario = DB::select(
            "
                SELECT
                    cr.id_tipo_expediente,
                    cr.id_tipo_queja,
                    cr.created_at
                FROM
                proceso_disciplinario pd
                INNER JOIN clasificacion_radicado cr ON pd.uuid = cr.id_proceso_disciplinario
                WHERE pd.uuid = '$id_proceso_disciplinario'
                ORDER BY cr.created_at DESC
            "
        );

        if (count($proceso_disciplinario) <= 0) {
            $error['estado'] = false;
            $error['error'] = 'ERROR AL MOMENTO DE OBTENER INFORMACIÓN DEL PROCESO.';
            return json_encode($error);
        }

        $query = $this->repository->customQuery(function ($model) use ($idDependencia, $proceso_disciplinario) {
            return $model->join('users_tipo_expediente as ute', 'ute.user_id', '=', 'Users.id')
                ->where('ute.tipo_expediente_id', '=', $proceso_disciplinario[0]->id_tipo_expediente)
                ->where('ute.sub_tipo_expediente_id', '=', $proceso_disciplinario[0]->id_tipo_queja)
                ->where('Users.id_dependencia', $idDependencia)
                ->where('Users.estado', Constants::ESTADOS['activo'])
                ->where('Users.reparto_habilitado', Constants::ESTADOS['activo'])
                ->select('Users.id', 'Users.name', 'Users.email', 'Users.nombre', 'Users.apellido', 'Users.id_dependencia', 'Users.estado')
                ->get();
        });


        return UsuarioCollection::make($query);
    }
}
