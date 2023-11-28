<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class UserTipoExpedienteModel extends Model
{

    use HasFactory;
    use SoftDeletes;


    protected $table = "users_tipo_expediente";

    public $timestamps = true;

    protected $fillable = [
        "user_id",
        "tipo_expediente_id",
        "sub_tipo_expediente_id",
        "created_user",
        "updated_user",
        "deleted_user",

    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'user_id';
    protected $keyType = 'number';
    public $incrementing = false;

}
