<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Adldap\Laravel\Traits\HasLdapUser;
use App\Models\Traits\HasFuncionalidadTrait;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasLdapUser;

    //use HasFuncionalidadTrait;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'objectguid',
        'nombre',
        'apellido',
        'id_dependencia',
        'id_mas_grupo_trabajo_secretaria_comun',
        'estado',
        'reparto_habilitado',
        'nivelacion',
        'numero_casos',
        'firma_mecanica',
        'password_firma_mecanica',
        'identificacion'

    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function dependencia()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "id_dependencia");
    }


    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles', 'user_id', 'role_id');
    }



    public function hasAccessToFuncionalidad($funcionalidad)
    {
        foreach ($funcionalidad->roles as $role) {
            if ($this->roles->contains($role)) {
                return true;
            }
        }
        return false;
    }

    public function hasRole(...$roles)
    {
        foreach ($roles as $role) {
            if ($this->roles->contains('name', $role)) {
                return true;
            }
        }
        return false;
    }

    public function getGrupoTrabajoSecretariaComun()
    {
        return $this->belongsTo(GrupoTrabajoSecretariaComunModel::class, "id_mas_grupo_trabajo_secretaria_comun");
    }
}
