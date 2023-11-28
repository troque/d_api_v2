<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class DependenciaOrigenModel extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "mas_dependencia_origen";

    public $timestamps = true;

    protected $fillable = [
        "id",
        "nombre",
        "created_user",
        "updated_user",
        "deleted_user",
        "id_usuario_jefe",
        "estado",
        "prefijo",
        "codigo_homologado"
    ];

    protected $hidden = [
        "created_at",
        "updated_at",
        "deleted_at",
        "created_user",
        "updated_user",
        "deleted_user",
    ];

    public function usuario_jefe()
    {
        return $this->belongsTo(User::class, "id_usuario_jefe");
    }

    public function accesos()
    {
        return $this->belongsToMany(DependenciaAccesoModel::class, 'mas_dependencia_configuracion', 'id_dependencia_origen', 'id_dependencia_acceso');
    }

    public function porcentajeAsignacion($ids_accesos, $id_dependencia)
    {
        if ($ids_accesos && $id_dependencia) {
            $accesos = explode(',', $ids_accesos);
            $porcentajes_asignados = '';

            foreach ($accesos as $acceso) {
                //$porcentaje = DependenciaConfiguracionModel::where(['id_dependencia_acceso' => $acceso, 'id_dependencia_origen' => $id_dependencia])->get();
                $porcentaje = DB::select("SELECT mdc.porcentaje_asignacion FROM mas_dependencia_configuracion mdc WHERE mdc.id_dependencia_acceso = $acceso AND mdc.id_dependencia_origen = $id_dependencia");
                if ($porcentajes_asignados == '') {
                    $porcentajes_asignados .= $porcentaje[0]->porcentaje_asignacion;
                } else {
                    $porcentajes_asignados .= ',' . $porcentaje[0]->porcentaje_asignacion;
                }
            }

            $accesos_respuesta['ids_accesos'] = $ids_accesos;
            $accesos_respuesta['porcentajes_asignados'] = $porcentajes_asignados;
            return $accesos_respuesta;
        }
    }
}
