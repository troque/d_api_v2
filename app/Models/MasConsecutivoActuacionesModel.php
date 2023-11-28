<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\VigenciaModel;

class MasConsecutivoActuacionesModel extends Model
{
    use HasFactory;

    protected $table = "mas_consecutivo_actuaciones";

    public $timestamps = true;

    protected $fillable = [
        "id_vigencia",
        "consecutivo",
        "id_actuacion",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "updated_user",
        "deleted_user",
    ];

    public function vigencia()
    {
        return $this->belongsTo(VigenciaModel::class, "id_vigencia", "id");
    }
}
