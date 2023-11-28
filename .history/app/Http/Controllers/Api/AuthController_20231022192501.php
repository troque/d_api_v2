<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\User\UserResource;
use App\Http\Requests\LoginFormRequest;
use App\Http\Requests\HasAccessFormRequest;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Http\Utilidades\Constants;
use App\Models\Funcionalidad;
use App\Models\UserRolesModel;
use App\Repositories\RepositoryGeneric;
use \YaLinqo\Enumerable as E;
use Exception;
use Auth;
use Error;
use PhpParser\Node\Expr\Cast\Array_;

class AuthController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new Funcionalidad());
    }

    /**
     * Login authentication
     *
     * @return \Illuminate\Http\Response
     */
    public function login(LoginFormRequest $request)
    {
        $datosRequest = $request->validated()["data"]["attributes"];

        $index = strpos($datosRequest["user"], '@');

        if ($index !== false) {
            $datosRequest["user"] = substr($datosRequest["user"], 0, $index);
        }

        $user_name = $datosRequest["user"];
        $user_pass = $datosRequest["password"];
        // error_log("user: " . $user_name);
        $userResponse = array();
        try {
            $userauth = null;
            if (str_contains($user_name, '@')) {
                $userauth = Auth::attempt(['email' => $user_name, 'password' => $user_pass]);
            } else {
                //busca el correo
                $searchAD = Adldap::search()->users()->find($user_name);
                if (!empty($searchAD)) {
                    $userauth = Auth::attempt(['email' => $searchAD->getUserprincipalname(), 'password' => $user_pass]);
                }
            }

            if ($userauth != null && $userauth == true) {
                // error_log("usuario atenticado con LDAP!");
                $adminUserName = env("ADMIN_USER", 'ForsecurityDiscUno');
                $user = auth()->user();

                // error_log("VALOR DE ESTADO ".$user->estado);

                if ($user->estado == Constants::ESTADOS['inactivo']) {
                    $error['estado'] = false;
                    $error['error'] = 'EL USUARIO NO SE ENCUENTRA ACTIVO. SI EL PROBLEMA PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR.';
                    return json_encode($error);
                }

                //si es usuario administrador lo asigna al rol Administrador
                if ($user->name == $adminUserName) {
                    $user->estado = '1';
                    if (!$user->hasRole('Administrador')) {
                        $userRolModel = new UserRolesModel();
                        $userRol["user_id"] = $user->id;
                        $userRol["role_id"] = 1;
                        $userRol["created_user"] = $user->name;
                        $userRolModel->create($userRol);
                    }
                }

                // error_log("info user: " . json_encode($user));
                // error_log("Estado usuario: " . $user->estado);
                //valida si el usuario está activo
                if ($user->estado != '1')
                    return response('Bad user/password', 400);


                //libera tokens creados
                //$user->tokens()->delete();

                //lista funcionalidades del usuario
                $functionalitiesArray = array();
                foreach ($user->roles as $role) {
                    foreach ($role->funcionalidades as $funcionalidad) {
                        array_push($functionalitiesArray, (object)[
                            'modulo' => $funcionalidad->modulo->nombre,
                            'funcionalidad' => $funcionalidad->nombre,
                        ]);
                    }
                }

                //obtiene lista de roles usuario
                // $listRoles = E::from($user->roles)->select(function($i){ return $i->name; })->toArray();
                // $strRoles = implode(",", $listRoles);
                $userResponse['user'] = UserResource::make($user);
                $userResponse['token'] = $user->createToken($user->email)->plainTextToken;
                $userResponse['funcionalities'] = json_encode($functionalitiesArray);
                //$userResponse['cryptFuncionalities'] = encrypt(json_encode($functionalitiesArray));
                // error_log("info user: " . json_encode($userResponse['user']));

                return response($userResponse, 200);
            } else {
                $error['estado'] = false;
                $error['error'] = 'EL USUARIO O LA CONTRASEÑA NO CORRESPONDEN AL LDAP. SI EL PROBLEMA PERSISTE COMUNÍQUESE CON EL ADMINISTRADOR.';
                return json_encode($error);
            }
        } catch (Exception $e) {
            error_log("Exception ldap login: " . $e->getMessage());
            $userResponse['error'] = $e->getMessage();
            return response($userResponse, 400);
        }
    }

    public function user()
    {
        return UserResource::make(auth()->user());
    }

    public function hasAccess(HasAccessFormRequest $request)
    {
        $user = auth()->user();
        // si es administrador acepta todas las funcionalidades
        if ($user->hasRole('Administrador'))
            response(true, 200);

        // para otros roles
        $datosRequest = $request->validated()["data"]["attributes"];;
        $moduloName = $datosRequest["modulo"];
        $funcionalidadName = $datosRequest["funcionalidad"];

        //DB::enableQueryLog(); // Enable query log
        $funcionalidad = Funcionalidad::where('nombre', $funcionalidadName)->whereHas('modulo', function ($query) use ($moduloName) {
            $query->where('nombre', '=', $moduloName);
        })->first();
        //dd(DB::getQueryLog()); // Show results of log

        if ($funcionalidad == null)
            return response(false, 401);
        else if ($user->hasAccessToFuncionalidad($funcionalidad))
            return response(true, 200);
        return response(false, 401);
    }

    public function users($criteria)
    {
        try {
            $results = Adldap::search()->where('displayname', 'contains', $criteria)->get();
            $findUsers = array();

            $array_antecedentes = json_decode(json_encode($results));
            print_r($array_antecedentes);

            foreach ($results as $result) {

                /*$user = new UserLdap();
                $attributes = $result->getAttributes();

                // Imprime los atributos en la terminal
                print_r($attributes);*/

                $user = new UserLdap();
                $user->displayName = $result->getDisplayName();
                $user->email = $result->getUserprincipalname();
                $user->name = $result->getName();
                $user->dn = $result->getDn();
                $user->nombre = $result["givenname"] != null ? $result["givenname"][0] : "";
                $user->apellido = $result["sn"] != null ? $result["sn"][0] : "";

                array_push($findUsers, $user);
            }
            return $findUsers;
        } catch (Exception $e) {
            //echo ($e->getMessage());
            return "[]";
        }
    }
}


class UserLdap
{
    public $displayName;
    public $email;
    public $name;
    public $dn;
    public $nombre;
    public $apellido;
}
