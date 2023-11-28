<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RolMasActuacionModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "tbint_roles_mas_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "id_mas_actuacion",
        "id_rol",
        "created_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "created_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;
}
