<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DiasNoLaboralesModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_dias_no_laborales";

    public $timestamps = true;

    protected $fillable = [
        "fecha",
        "estado",
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
}
