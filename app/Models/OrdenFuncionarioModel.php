<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrdenFuncionarioModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_orden_funcionario";

    public $timestamps = true;

    protected $fillable = [
        "orden",
        "estado",
        "id_funcionario",
        "grupo",
        "id_evaluacion",
        "id_expediente",
        "id_sub_expediente",
        "id_tercer_expediente",
        "funcionario_siguiente",
        "unico_rol",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function rol() {
        return $this->hasOne(Role::class, 'id', 'id_funcionario');
    }

    public function user(){
        return $this->hasOne(User::class, 'name', 'created_user');
    }

}
