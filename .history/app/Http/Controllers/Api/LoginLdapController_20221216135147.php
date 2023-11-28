<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Adldap\Laravel\Facades\Adldap;
use App\Models\Funcionalidad;
use App\Repositories\RepositoryGeneric;
use Illuminate\Support\Facades\DB;
use Exception;
use Auth;

use function Psy\debug;

/**
 * @OA\Info(title="API LoginLdap", version="1.0")
 *
 * @OA\Server(url="https://127.0.0.1:8000")
 */
class LoginLdapController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new Funcionalidad());
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user_name = $request->__get("user");
        $user_pass = $request->__get("password");
        $user = array();
        try {
            $resp = Adldap::search()->users()->find($user_name);
            //$resp = $this->ldap->search()->users()->find($user_name);
            if (!empty($resp)) {
                $user['name'] = $resp->getName();
                $user['uid'] =  $resp->getDistinguishedname();
                $user['email'] = $resp->getUserprincipalname();
                $user['groups'] = $resp->getMemberof();
                $ldapReponse = Auth::attempt(['email' => $resp->getUserprincipalname(), 'password' => $user_pass]);
                //eval('Ldap response: ' . $ldapReponse);
                $user['validPassWord'] = $ldapReponse;
                $userauth = auth()->user();
                $user['user'] = $userauth;
                //$user['ldap-dump'] = var_dump($userauth->ldap);
                //$user['user-group'] = $userauth->ldap->getGroups();
                $user['user-commondName'] = auth()->user()->ldap->getCommonName();
                $user['user-ConvertedSid'] = auth()->user()->ldap->getConvertedSid();
                $user['token'] = $userauth->createToken($resp->getUserprincipalname(), ['server:update,create,allroles'])->plainTextToken;
            }
            $results = Adldap::search()->raw()->where('cn', '=', 'ForsecurityDiscUno')->get();
            echo $results;
            return $results;
            //return $user;
        } catch (Exception $e) {
            echo ($e->getMessage());
            return $user;
        }

        //$request->__get("user");
        // $ldapconn = ldap_connect("172.28.4.46")
        // or die("Could not connect to LDAP server.");

        // if ($ldapconn) {

        //     // binding to ldap server
        //     $ldapbind = ldap_bind($ldapconn, "ForsecurityDiscUno", "jNL6mR4BDpT");

        //     // verify binding
        //     if ($ldapbind) {
        //         echo "LDAP bind successful...";
        //     } else {
        //         echo "LDAP bind failed...";
        //     }

        // }

        // return "ok";//response()->json(['message' => 'ok']);


    }

    /**
     * @OA\Get(
     *     path="/api/v1/Login-Ldap/currentuser",
     *     summary="Mostrar usuarios",
     *     @OA\Response(
     *         response=200,
     *         description="Mostrar todos los usuarios."
     *     ),
     *     @OA\Response(
     *         response="default",
     *         description="Ha ocurrido un error."
     *     )
     * )
     */
    public function currentuser()
    {
        $user = auth()->user();
        return $user;
    }

    public function getUserInfo(Request $request)
    {
        DB::enableQueryLog(); // Enable query log
        //echo "-----roles_method2-----";
        echo DB::table('roles_users')->get();
        dd(DB::getQueryLog()); // Show results of log
        return false;
        // $user_name = $request->__get("user");
        // $user = Adldap::search()->users()->where('cn', '=', $user_name)->first();

        // // Single level groups
        // //$groups = $user->getGroups();

        // // All Nested groups
        // $groups = $user->getGroups($fields = ['*'], $recursive = true);
        // return $groups;
        // // $user = auth()->user();
        // // return $user;
    }

    public function getUserHasFuncionalidad(Request $request)
    {
        $funcionalidadName = $request->__get("funcionalidad");
        //$funcionalidad = $this->repository->find($id)->load("departamento"));

        $query = $this->repository->customQuery(function ($model) use ($funcionalidadName) {
            return $model->where('nombre', $funcionalidadName)->get();
        });

        if (empty($query[0])) {
            $response['estado'] = false;
            $response['message'] = 'No existe la funcionalidad: ' . $funcionalidadName;
            return json_encode($response);
        } else {
            $user = auth()->user();
            if ($user->hasAccessToFuncionalidad($query[0])) {
                $response['estado'] = true;
                $response['message'] = 'Funcionalidad asignada al usuario';
            } else {
                $response['estado'] = false;
                $response['message'] = 'No tiene asignada la funcionalidad';
            }
            return json_encode($response);
        }
        return false;
    }
}
