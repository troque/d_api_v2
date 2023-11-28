<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConsecutivoDesgloseModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_consecutivo_desglose";

    public $timestamps = true;

    protected $fillable = [
        "id_vigencia",
        "consecutivo",
        "estado",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function getVigencia()
    {
        return $this->belongsTo(VigenciaModel::class, "id_vigencia");
    }

}
