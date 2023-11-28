<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoConductaProcesoDisciplinarioModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "tipo_conducta_proceso_disciplinario";

    public $timestamps = true;

    protected $fillable = [
        "id_proceso_disciplinario",
        "id_tipo_conducta",
        "estado",
        "id_etapa",
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

    protected $primaryKey = 'uuid';
    protected $keyType = 'string';
    public $incrementing = false;


    public function etapa() {
        return $this->belongsTo(EtapaModel::class,"id_etapa");
    }

    public function usuario() {
        return $this->belongsTo(User::class,"created_user","name");
    }
}
