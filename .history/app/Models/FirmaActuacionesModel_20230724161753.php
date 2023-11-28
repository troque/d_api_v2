<?php

namespace App\Models;

use App\Http\Utilidades\Constants;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FirmaActuacionesModel extends Model
{

    protected $table = "firma_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "id_actuacion",
        "id_user",
        "tipo_firma",
        "estado",
        "uuid_proceso_disciplinario",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
    ];

    protected $primaryKey = 'id';
    protected $keyType = 'number';
    public $incrementing = false;


    public function usuario()
    {
        return $this->belongsTo(User::class, "id_user", "id");
    }

    public function actuacion()
    {
        return $this->belongsTo(ActuacionesModel::class, "id_actuacion", "uuid");
    }

    public function archivo_actuacion()
    {
        return $this->belongsTo(ArchivoActuacionesModel::class, "id_actuacion", "uuid_actuacion");
    }

    public function proceso_disciplinario()
    {
        return $this->belongsTo(ProcesoDiciplinarioModel::class, "uuid_proceso_disciplinario", "uuid");
    }

    public function get_tipo_firma()
    {
        return $this->belongsTo(TipoFirmaModel::class, "tipo_firma", "id");
    }

    public function nombreActuacion()
    {
        $actuacion = ActuacionesModel::where([
            ['uuid', '=', $this['id_actuacion']]
        ])->get();

        error_log("hola1");

        if (count($actuacion) > 0) {

            error_log($actuacion[0]->id_actuacion);
            $mas_actuaciones = MasActuacionesModel::where([
                ['id', '=', $actuacion[0]->id_actuacion]
            ])->get();

            return $mas_actuaciones[0]->nombre_actuacion;
        } else {
            return null;
        }
    }
}
