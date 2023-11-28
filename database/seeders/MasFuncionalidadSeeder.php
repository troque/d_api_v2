<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DateTime;

class MasFuncionalidadSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('funcionalidad_rol')->delete();
        DB::table('mas_funcionalidad')->delete();
        DB::statement("alter sequence MAS_FUNCIONALIDAD_ID_SEQ restart start with 1");

        foreach ($this->funcionalidades() as $func) {
            $idModulo = DB::table('mas_modulo')->where('nombre', '=', $func['modulo'])->value('id');
            if ($idModulo == null) echo 'Error el modulo no existe ' . $func['modulo'];
            DB::table('mas_funcionalidad')->insert(
                array(
                    'nombre' => $func['nombre'],
                    'nombre_mostrar' => $func['nombre_mostrar'],
                    'id_modulo' => $idModulo,
                    'created_at' => new DateTime,
                    'updated_at' => new DateTime
                )
            );
        }
    }

    public function funcionalidades()
    {
        return [
            // MIS PENDIENTES
            [
                "modulo" => "MP_Semaforizacion",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Semaforización"
            ],

            [
                "modulo" => "MP_Historial_Expediente",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Historial del expediente"
            ],

            [
                "modulo" => "MP_RemitirProceso",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "MP_RamasProceso",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "MP_Caratula",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Carátula mis pendientes"
            ],

            [
                "modulo" => "CR_Antecedente",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "Inactivar"
            ],

            [
                "modulo" => "CR_Antecedente",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "CR_Antecedente",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "CR_Interesado",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "Inactivar"
            ],

            [
                "modulo" => "CR_Interesado",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "CR_Interesado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "CR_ClasificacionRadicado",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "Inactivar"
            ],

            [
                "modulo" => "CR_ClasificacionRadicado",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "CR_ClasificacionRadicado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "CR_EntidadInvestigado",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "Inactivar"
            ],

            [
                "modulo" => "CR_EntidadInvestigado",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "CR_EntidadInvestigado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "CR_SoporteRadicado",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "Inactivar"
            ],

            [
                "modulo" => "CR_SoporteRadicado",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "CR_SoporteRadicado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "CR_CierreEtapa",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "CR_CierreEtapa",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "EI_ValidarClasificacion",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "EI_ValidarClasificacion",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "EI_Evaluacion",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "EI_Evaluacion",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "EI_RemisionQueja",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "EI_RemisionQueja",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "E_GestorRespuesta",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "E_GestorRespuesta",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "E_ComunicacionInteresados",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "E_ComunicacionInteresados",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "EI_DocumentoCierre",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "EI_DocumentoCierre",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "EI_InformacionCierre",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "EI_InformacionCierre",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "EI_RequerimientoJuzgado",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "EI_RequerimientoJuzgado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "EI_CierreEtapa",
                "nombre" => "Crear",
                "nombre_mostrar" => "Crear"
            ],

            [
                "modulo" => "EI_CierreEtapa",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar"
            ],

            [
                "modulo" => "SeleccionarDependenciaDuenaDelProceso",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "SELECCIONAR DEPENDENCIA DUEÑA DEL PROCESO"
            ],

            [
                "modulo" => "AprobarYRechazarUnImpedimento",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "APROBAR Y RECHAZAR UN IMPEDIMENTO"
            ],

            [
                "modulo" => "SolicitudDeInactivacion",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "ATENDER SOLICITUDES DE INACTIVACIÓN"
            ],

            [
                "modulo" => "SolicitudDeAprobacion",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "ATENDER SOLICITUDES DE APROBACIÓN"
            ],

            [
                "modulo" => "AgregarUsuarioParaFirmaMecanica",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "AGREGAR USUARIO PARA FIRMA MECÁNICA"
            ],

            [
                "modulo" => "Actuaciones",
                "nombre" => "Crear",
                "nombre_mostrar" => "Actuaciones"
            ],

            [
                "modulo" => "Actuaciones",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "Actuaciones"
            ],

            [
                "modulo" => "EnviarDirigidoSecretariaComun",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Enviar a alguien de mi dependencia dirigidamente (secretaria común)"
            ],

            [
                "modulo" => "EnviarAleatorioSecretariaComun",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Enviar a alguien de mi dependencia aleatoriamente (secretaria común)"
            ],

            [
                "modulo" => "EnviarUsuarioDependencia",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Enviar a alguien de mi dependencia"
            ],

            [
                "modulo" => "EnviarOtraDependencia",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Enviar a otra dependencia"
            ],

            [
                "modulo" => "EnviarAJefe",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Enviar a jefe de la dependencia"
            ],

            [
                "modulo" => "RegresarUltimoUsuario",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Regresar proceso al último usuario"
            ],

            [
                "modulo" => "Buscador",
                "nombre" => "Consultar",
                "nombre_mostrar" => "Consultar Buscador"
            ],

            [
                "modulo" => "CP_Asignado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CONSULTAR LOS PROCESOS COMPLETOS QUE ESTÁN EN MIS PENDIENTES"
            ],

            [
                "modulo" => "CP_NOAsignado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CONSULTAR UN PROCESO COMPLETO DE CUALQUIER USUARIO DE UNA DEPENDENCIA"
            ],

            [
                "modulo" => "CH_ReclasificacionExpediente",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_ReclasificacionExpediente"
            ],

            [
                "modulo" => "CH_ReclasificacionEvaluacion",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_ReclasificacionEvaluacion"
            ],

            [
                "modulo" => "CH_TipoConducta",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_TipoConducta"
            ],

            [
                "modulo" => "CH_UsuarioComisionado",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_UsuarioComisionado"
            ],

            [
                "modulo" => "CH_DeclararmeImpedido",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_DeclararmeImpedido"
            ],

            [
                "modulo" => "CH_DuenaProceso",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_DuenaProceso"
            ],

            [
                "modulo" => "CH_Caratula",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_Caratula"
            ],

            [
                "modulo" => "CH_Transacciones",
                "nombre" => "Consultar",
                "nombre_mostrar" => "CH_Transacciones"
            ],

            [
                "modulo" => "Jefe",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Jefe"
            ],

            [
                "modulo" => "G_IniciarProceso",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Jefe"
            ],

            [
                "modulo" => "ADMIN_Perfiles",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "ADMIN_Perfiles"
            ],

            [
                "modulo" => "ADMIN_Perfiles",
                "nombre" => "Consultar",
                "nombre_mostrar" => "ADMIN_Perfiles"
            ],

            [
                "modulo" => "ADMIN_Perfiles",
                "nombre" => "Crear",
                "nombre_mostrar" => "ADMIN_Perfiles"
            ],

            [
                "modulo" => "ADMIN_ProcesoDisciplinario",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "ADMIN_ProcesoDisciplinario"
            ],

            [
                "modulo" => "ADMIN_ProcesoDisciplinario",
                "nombre" => "Consultar",
                "nombre_mostrar" => "ADMIN_ProcesoDisciplinario"
            ],

            [
                "modulo" => "ADMIN_ProcesoDisciplinario",
                "nombre" => "Crear",
                "nombre_mostrar" => "ADMIN_ProcesoDisciplinario"
            ],

            [
                "modulo" => "ADMIN_Actuaciones",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "ADMIN_Actuaciones"
            ],

            [
                "modulo" => "ADMIN_Actuaciones",
                "nombre" => "Consultar",
                "nombre_mostrar" => "ADMIN_Actuaciones"
            ],

            [
                "modulo" => "ADMIN_Actuaciones",
                "nombre" => "Crear",
                "nombre_mostrar" => "ADMIN_Actuaciones"
            ],

            [
                "modulo" => "ADMIN_Caratula",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "ADMIN_Caratula"
            ],

            [
                "modulo" => "ADMIN_Caratula",
                "nombre" => "Consultar",
                "nombre_mostrar" => "ADMIN_Caratula"
            ],

            [
                "modulo" => "ADMIN_Caratula",
                "nombre" => "Crear",
                "nombre_mostrar" => "ADMIN_Caratula"
            ],

            [
                "modulo" => "ADMIN_TrasladoCasos",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "ADMIN_TrasladoCasos"
            ],

            [
                "modulo" => "ADMIN_InformeGeneral",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "ADMIN_InformeGeneral"
            ],

            [
                "modulo" => "ADMIN_PortalWeb",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "ADMIN_PortalWeb"
            ],

            [
                "modulo" => "ADMIN_PortalWeb",
                "nombre" => "Consultar",
                "nombre_mostrar" => "ADMIN_PortalWeb"
            ],

            [
                "modulo" => "ADMIN_PortalWeb",
                "nombre" => "Crear",
                "nombre_mostrar" => "ADMIN_PortalWeb"
            ],

            [
                "modulo" => "ADMIN_Otros",
                "nombre" => "Inactivar",
                "nombre_mostrar" => "ADMIN_Otros"
            ],

            [
                "modulo" => "ADMIN_Otros",
                "nombre" => "Consultar",
                "nombre_mostrar" => "ADMIN_Otros"
            ],

            [
                "modulo" => "ADMIN_Otros",
                "nombre" => "Crear",
                "nombre_mostrar" => "ADMIN_Otros"
            ],

            [
                "modulo" => "A_CambiarFaseProcesoDisciplinario",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "A_CambiarFaseProcesoDisciplinario"
            ],

            [
                "modulo" => "EnviarAMisPendientes",
                "nombre" => "Gestionar",
                "nombre_mostrar" => "Enviar a mis pendientes"
            ],

        ];
    }
}
