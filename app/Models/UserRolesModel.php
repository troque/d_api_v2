<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class UserRolesModel extends Model
{

    use HasFactory;
    use SoftDeletes;


    protected $table = "users_roles";

    public $timestamps = true;

    protected $fillable = [
        "user_id",
        "role_id",
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

    protected $primaryKey = 'user_id';
    protected $keyType = 'number';
    public $incrementing = false;

}
