<?php

namespace App\Models;

use App\Http\Resources\Role\FuncionalidadCollection;
use App\Repositories\RepositoryGeneric;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
    use HasFactory;

    protected $table = "mas_modulo";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombre',
        'nombre_mostrar'
    ];


    public function funcionalidades(){
        return $this->hasMany(Funcionalidad::class,'ID_MODULO');
    }
}
