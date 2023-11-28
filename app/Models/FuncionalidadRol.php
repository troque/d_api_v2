<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuncionalidadRol extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "funcionalidad_rol";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'FUNCIONALIDAD_ID',
        'ROLE_ID',
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


    protected $primaryKey = 'FUNCIONALIDAD_ID';
    protected $keyType = 'number';
    public $incrementing = false;

}
