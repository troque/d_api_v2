<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadModel extends Model
{
    use HasFactory;

    protected $connection = 'ORA_SINPROC';

    protected $table = "sector";

    protected $fillable = [
        "idsector",
        "idestado",
        "nombre",
    ];


    public function sector() {
        return $this->belongsTo(TipoExpedienteModel::class, "id_tipo_expediente");
    }




}
