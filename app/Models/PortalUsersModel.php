<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PortalUsersModel extends Model
{
    use HasFactory;

    protected $table = "portal_users";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "numero_documento",
        "tipo_documento",
        "email",
        "email_verified_at",
        "estado",
        "password",
        "created_user",
        "updated_user",
        "delete_user",
        "created_at",
        "updated_at",
    ];

    protected $hidden = [
        "created_user",
        "updated_user",
        "deleted_user",
        "created_at",
        "updated_at",
    ];
}