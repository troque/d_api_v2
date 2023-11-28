<?php

namespace App\Models;

use App\Models\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoActuacionesModel extends Model
{
    use HasFactory, HasUuid;

    protected $table = "archivo_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "uuid_actuacion",
        "id_tipo_archivo",
        "documento_ruta",
        "created_user",
        "updated_user",
        "deleted_user",
        "nombre_archivo",
        "extension",
        "peso",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;

    public function actuaciones()
    {
        return $this->belongsTo(ActuacionesModel::class, "uuid_actuacion", "uuid");
    }

    public function mas_tipo_archivo_actuaciones()
    {
        return $this->belongsTo(MasTipoArchivoActuacionesModel::class, "id_tipo_archivo", "id");
    }
}