<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CondicionModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "condicion";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "inicial",
        "final",
        "color",
        "id_semaforo",
        "estado",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
    ];
    
    public function get_id_semaforo() {
        return $this->belongsTo(SemaforoModel::class,"id_semaforo","id");
    }
}
