<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PreguntasDocumentoCierreModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_preguntas_doc_cierre";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "nombre",
        "estado",
        "created_user",
        "updated_user",
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "created_user",
        "updated_user",
    ];
}