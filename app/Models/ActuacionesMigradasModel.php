<?php

namespace App\Models;

use App\Http\Utilidades\Constants;
use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActuacionesMigradasModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "actuaciones_migradas";

    public $timestamps = true;

    protected $fillable = [
        "radicado",
        "vigencia",
        "item",
        "nombre",
        "id_tipo_actuacion",
        "id_etapa",
        "autonumero",
        "fecha",
        "path",
        "dependencia",
        "created_user",
        "created_at"
    ];

    protected $hidden = [
        "updated_at",
        "updated_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;


    public function mas_actuaciones()
    {
        return $this->belongsTo(MasActuacionesModel::class, "id_tipo_actuacion", "id");
    }

    public function mas_dependencia_origen()
    {
        return $this->belongsTo(DependenciaOrigenModel::class, "dependencia", "id");
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, "created_user", "name");
    }

    public function estado()
    {
        return MasEstadoActuacionesModel::where('id', Constants::ESTADOS_ACTUACION['aprobada_pdf_definitivo'])->get()[0];
    }

    public function archivo_pdf()
    {
        return $this->hasOne(ArchivoActuacionesModel::class, "uuid_actuacion", "uuid");
    }

}
