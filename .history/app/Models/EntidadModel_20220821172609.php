<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EntidadModel extends Model
{
    use HasFactory;

    protected $connection = 'ORA_SINPROC';

    protected $table = "entidad";

    protected $fillable = [
        "identidad",
        "idsector",
        "idsecretaria",
        "created_user",
        "updated_user",
        "deleted_user",
    ];


    public function sector() {
        return $this->belongsTo(SectorModel::class, "idsector");
    }




}
