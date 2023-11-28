<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Funcionalidad extends Model
{
    use HasFactory;

    protected $table = "mas_funcionalidad";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'nombre_mostrar',
        'id_modulo',
    ]; 

    public function roles() {
        return $this->belongsToMany(Role::class, 'funcionalidad_rol', 'funcionalidad_id', 'role_id');
    }

    public function modulo() {
        return $this->hasOne(Modulo::class, 'id', 'id_modulo');
    }
}
