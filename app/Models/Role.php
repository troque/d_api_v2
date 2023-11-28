<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $table = "roles";

    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
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


    public function funcionalidades() {
        return $this->belongsToMany(Funcionalidad::class, 'FUNCIONALIDAD_ROL','ROLE_ID', 'FUNCIONALIDAD_ID');

     }

     public function usuarios() {
        return $this->belongsToMany(User::class, 'users_roles', 'role_id', 'user_id');
     }
}
