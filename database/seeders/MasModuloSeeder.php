<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Modulo;

class MasModuloSeeder extends Seeder
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
        DB::table('mas_modulo')->delete();
        DB::statement("alter sequence MAS_MODULO_ID_SEQ restart start with 1");

        foreach ($this->modulos() as $modulo) {
            Modulo::create($modulo);
        }
    }

    public function modulos()
    {
        return [
            [
                "nombre" => "MP_Semaforizacion",
                "nombre_mostrar" => "SEMAFORIZACIÓN",
                "estado" => true,
                "id_mas_grupo" => 1,
                "orden" => 1,
                "mensaje_ayuda" => "El módulo 'SEMAFORIZACIÓN' cuenta con el permiso 'CONSULTAR', el cual brinda acceso a la opción 'SEMAFORIZACIÓN' ubicado en la sección 'SISTEMA DISCIPLINARIOS -> MIS PENDIENTES' en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "MP_Historial_Expediente",
                "nombre_mostrar" => "HISTORIAL DEL EXPEDIENTE",
                "estado" => true,
                "id_mas_grupo" => 1,
                "orden" => 2,
                "mensaje_ayuda" => "El módulo 'HISTORIAL DEL EXPEDIENTE' cuenta con el permiso 'CONSULTAR', el cual brinda acceso a la opción 'HISTORIAL DEL EXPEDIENTE ' ubicado en la sección 'SISTEMA DISCIPLINARIOS -> MIS PENDIENTES' en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "MP_RemitirProceso",
                "nombre_mostrar" => "REMITIR PROCESOS",
                "estado" => true,
                "id_mas_grupo" => 1,
                "orden" => 3,
                "mensaje_ayuda" => "El módulo 'REMITIR PROCESOS' cuenta con el permiso 'CONSULTAR', el cual brinda acceso a la opción 'REMITIR PROCESOS ' ubicado en la sección 'SISTEMA DISCIPLINARIOS -> MIS PENDIENTES' en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "MP_Caratula",
                "nombre_mostrar" => "CARÁTULA",
                "estado" => true,
                "id_mas_grupo" => 1,
                "orden" => 5,
                "mensaje_ayuda" => "El módulo 'CARÁTULA' cuenta con el permiso 'CONSULTAR', el cual brinda acceso a la opción 'CARÁTULA' ubicado en la sección 'SISTEMA DISCIPLINARIOS -> MIS PENDIENTES' en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "MP_RamasProceso",
                "nombre_mostrar" => "RAMAS DEL PROCESO",
                "estado" => true,
                "id_mas_grupo" => 1,
                "orden" => 4,
                "mensaje_ayuda" => "El módulo 'RAMAS DEL PROCESO' cuenta con el permiso 'CONSULTAR', el cual brinda acceso a la opción 'RAMAS DEL PROCESO' ubicado en la sección 'SISTEMA DISCIPLINARIOS -> MIS PENDIENTES' en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "CR_Antecedente",
                "nombre_mostrar" => "ANTECEDENTES",
                "estado" => true,
                "id_mas_grupo" => 2,
                "orden" => 1,
                "mensaje_ayuda" => "En el módulo 'ANTECEDENTES', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR' y 'ACTIVAR/INACTIVAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la lista de antecedentes registrados en el sistema.
                        El permiso 'CREAR' habilita la creación de un nuevo antecedente en el sistema.
                        El permiso 'ACTIVAR/INACTIVAR' posibilita activar o inactivar un antecedente según sea necesario."
            ],

            [
                "nombre" => "CR_Interesado",
                "nombre_mostrar" => "INTERESADO",
                "estado" => true,
                "id_mas_grupo" => 2,
                "orden" => 2,
                "mensaje_ayuda" => "En el módulo 'DATOS DEL INTERESADO', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR' y 'ACTIVAR/INACTIVAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la lista de interesados (quejosos) registrados en el sistema.
                        El permiso 'CREAR' habilita la creación de un nuevo interesado (quejoso) en el sistema.
                        El permiso 'ACTIVAR/INACTIVAR' posibilita activar o inactivar un interesado (quejoso) según sea necesario."
            ],

            [
                "nombre" => "CR_EntidadInvestigado",
                "nombre_mostrar" => "ENTIDAD DEL INVESTIGADO",
                "estado" => true,
                "id_mas_grupo" => 2,
                "orden" => 4,
                "mensaje_ayuda" => "En el módulo 'ENTIDAD DEL INVESTIGADO', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR' y 'ACTIVAR/INACTIVAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la lista de entidades del investigado registrados en el sistema.
                        El permiso 'CREAR' habilita la creación de una nueva entidad del investigado en el sistema.
                        El permiso 'ACTIVAR/INACTIVAR' posibilita activar o inactivar una entidad del investigado según sea necesario."
            ],

            [
                "nombre" => "CR_SoporteRadicado",
                "nombre_mostrar" => "SOPORTE DEL RADICADO",
                "estado" => true,
                "id_mas_grupo" => 2,
                "orden" => 5,
                "mensaje_ayuda" => "En el módulo 'SOPORTE DEL RADICADO', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR' y 'ACTIVAR/INACTIVAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la lista de soportes del radicado en el sistema.
                        El permiso 'CREAR' habilita la creación de una nuevo soporte del radicado en el sistema.
                        El permiso 'ACTIVAR/INACTIVAR' posibilita activar o inactivar un soporte del radicado según sea necesario."
            ],

            [
                "nombre" => "CR_CierreEtapa",
                "nombre_mostrar" => "CIERRE DE ETAPA CAPTURA Y REPARTO",
                "estado" => true,
                "id_mas_grupo" => 2,
                "orden" => 6,
                "mensaje_ayuda" => "En el módulo 'CIERRE DE ETAPA CAPTURA Y REPARTO', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR''.
                        El permiso 'CONSULTAR' permite acceder y consultar a la información de cierre de etapa de captura y reparto.
                        El permiso 'CREAR' permite registrar el cierre de la etapa de captura y reparto."

            ],

            [
                "nombre" => "CR_ClasificacionRadicado",
                "nombre_mostrar" => "CLASIFICACIÓN DEL RADICADO",
                "estado" => true,
                "id_mas_grupo" => 2,
                "orden" => 3,
                "mensaje_ayuda" => "En el módulo 'CLASIFICACIÓN DEL RADICADO', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR' y 'ACTIVAR/INACTIVAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la clasificación del radicado registrado en el sistema.
                        El permiso 'CREAR' habilita la creación de una nueva clasificación de radicado en el sistema.
                        El permiso 'ACTIVAR/INACTIVAR' posibilita activar o inactivar una clasificación de radicado según sea necesario."

            ],

            [
                "nombre" => "EI_RemisionQueja",
                "nombre_mostrar" => "REMISIÓN QUEJA",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 3,
                "mensaje_ayuda" => "En el módulo 'REMISIÓN QUEJA', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la fase 'REMISIÓN QUEJA'.
                        El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "EI_Evaluacion",
                "nombre_mostrar" => "EVALUACIÓN",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 2,
                "mensaje_ayuda" => "En el módulo 'EVALUACIÓN', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la fase ''EVALUACIÓN'.
                        El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "E_GestorRespuesta",
                "nombre_mostrar" => "GESTOR RESPUESTA",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 4,
                "mensaje_ayuda" => "En el módulo 'GESTOR RESPUESTA', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la fase 'GESTOR RESPUESTA'.
                        El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "EI_ValidarClasificacion",
                "nombre_mostrar" => "VALIDAR CLASIFICACIÓN",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 1,
                "mensaje_ayuda" => "En el módulo 'VALIDAR CLASIFICACIÓN', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la fase 'VALIDAR CLASIFICACIÓN'.
                        El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "EI_DocumentoCierre",
                "nombre_mostrar" => "DOCUMENTO CIERRE",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 6,
                "mensaje_ayuda" => "En el módulo 'DOCUMENTO CIERRE', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la fase 'DOCUMENTO CIERRE'.
                        El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "EI_CierreEtapa",
                "nombre_mostrar" => "CIERRE DE ETAPA EVALUACIÓN PQR",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 9,
                "mensaje_ayuda" => "En el módulo 'CIERRE DE ETAPA EVALUACIÓN PQR', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la fase 'CIERRE DE ETAPA EVALUACIÓN PQR'.
                        El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "EI_RequerimientoJuzgado",
                "nombre_mostrar" => "REQUERIMIENTO JUZGADO",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 8,
                "mensaje_ayuda" => "En el módulo 'REQUERIMIENTO JUZGADO', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                        El permiso 'CONSULTAR' permite acceder y consultar la fase 'REQUERIMIENTO JUZGADO'.
                        El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "EI_InformacionCierre",
                "nombre_mostrar" => "INFORMACIÓN CIERRE",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 7,
                "mensaje_ayuda" => ""
            ],

            [
                "nombre" => "E_ComunicacionInteresados",
                "nombre_mostrar" => "COMUNICACIÓN DEL INTERESADO",
                "estado" => true,
                "id_mas_grupo" => 3,
                "orden" => 5,
                "mensaje_ayuda" => "En el módulo 'COMUNICACIÓN DEL INTERESADO', se encuentran disponibles los permisos 'CONSULTAR', 'CREAR'.
                    El permiso 'CONSULTAR' permite acceder y consultar la fase 'COMUNICACIÓN DEL INTERESADO'.
                    El permiso 'CREAR' permite registrar la información correspondiente a esta fase."
            ],

            [
                "nombre" => "SeleccionarDependenciaDuenaDelProceso",
                "nombre_mostrar" => "SELECCIONAR DEPENDENCIA DUEÑA DEL PROCESO",
                "estado" => true,
                "id_mas_grupo" => 4,
                "orden" => 1,
                "mensaje_ayuda" => "En el módulo 'SELECCIONAR DEPENDENCIA DUEÑA DEL PROCESO', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de seleccionar la dependencia dueña del proceso al momento de gestionar una actuación."
            ],

            [
                "nombre" => "AprobarYRechazarUnImpedimento",
                "nombre_mostrar" => "APROBAR Y RECHAZAR UN IMPEDIMENTO",
                "estado" => true,
                "id_mas_grupo" => 4,
                "orden" => 2,
                "mensaje_ayuda" => "En el módulo 'APROBAR Y RECHAZAR UN IMPEDIMENTO', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de aprobar o rechazar un impedimento al momento de gestionar una actuación."
            ],

            [
                "nombre" => "SolicitudDeInactivacion",
                "nombre_mostrar" => "ATENDER SOLICITUDES DE INACTIVACIÓN",
                "estado" => true,
                "id_mas_grupo" => 4,
                "orden" => 3,
                "mensaje_ayuda" => "En el módulo 'ATENDER SOLICITUDES DE INACTIVACIÓN', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de 'GESTIONAR' una actuación al momento de gestionarla."
            ],

            [
                "nombre" => "SolicitudDeAprobacion",
                "nombre_mostrar" => "ATENDER SOLICITUDES DE APROBACIÓN",
                "estado" => true,
                "id_mas_grupo" => 4,
                "orden" => 4,
                "mensaje_ayuda" => "En el módulo 'ATENDER SOLICITUDES DE APROBACIÓN', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de aprobar una actuación al momento de gestionarla."
            ],

            [
                "nombre" => "AgregarUsuarioParaFirmaMecanica",
                "nombre_mostrar" => "FIRMAR ACTUACIÓN",
                "estado" => true,
                "id_mas_grupo" => 4,
                "orden" => 5,
                "mensaje_ayuda" => "En el módulo 'FIRMAR ACTUACIÓN', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de firmar de forma mecánica una actuación al momento de gestionarla."
            ],

            [
                "nombre" => "Actuaciones",
                "nombre_mostrar" => "ACTUACIONES",
                "estado" => true,
                "id_mas_grupo" => 4,
                "orden" => 6,
                "mensaje_ayuda" => "En el módulo 'ACTUACIONES', se encuentran disponibles los permisos 'CREAR' y 'ACTIVAR/INACTIVAR'.
                    El permiso 'CREAR' habilita la creación de una nueva actuación en el sistema.
                    El permiso 'ACTIVAR/INACTIVAR' posibilita activar o inactivar una actuación según sea necesario."
            ],

            [
                "nombre" => "EnviarDirigidoSecretariaComun",
                "nombre_mostrar" => "TRANSFERIR UN PROCESO A UN USUARIO ESPECÍFICO DE MI DEPENDENCIA (SOLO APLICA PARA SECRETARIA COMÚN)",
                "estado" => true,
                "id_mas_grupo" => 5,
                "orden" => 1,
                "mensaje_ayuda" => "En el módulo 'TRANSFERIR UN PROCESO A UN USUARIO ESPECÍFICO DE MI DEPENDENCIA (SOLO APLICA PARA SECRETARIA COMÚN)', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de poder transferir un proceso a un usuario específico de la dependencia 'SECRETARÍA COMÚN'."
            ],

            [
                "nombre" => "EnviarAleatorioSecretariaComun",
                "nombre_mostrar" => "TRANSFERIR UN PROCESO ALEATORIAMENTE ENTRE LOS USUARIOS DE MI DEPENDENCIA (SOLO APLICA PARA SECRETARÍA COMÚN)",
                "estado" => true,
                "id_mas_grupo" => 5,
                "orden" => 2,
                "mensaje_ayuda" => "En el módulo 'TRANSFERIR UN PROCESO ALEATORIAMENTE ENTRE LOS USUARIOS DE MI DEPENDENCIA (SOLO APLICA PARA SECRETARÍA COMÚN)', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de poder transferir un proceso, para ser asignado de manera aleatoria a cualquier usuario de la dependencia 'SECRETARÍA COMÚN'."
            ],

            [
                "nombre" => "EnviarUsuarioDependencia",
                "nombre_mostrar" => "TRANSFERIR UN PROCESO A UN USUARIO DE MI DEPENDENCIA",
                "estado" => true,
                "id_mas_grupo" => 5,
                "orden" => 3,
                "mensaje_ayuda" => "En el módulo 'TRANSFERIR UN PROCESO A UN USUARIO DE MI DEPENDENCIA', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de poder transferir un proceso a un usuario específico de mi dependencia."
            ],

            [
                "nombre" => "EnviarOtraDependencia",
                "nombre_mostrar" => "TRASFERIR UN PROCESO AL JEFE DE OTRA DEPENDENCIA",
                "estado" => true,
                "id_mas_grupo" => 5,
                "orden" => 4,
                "mensaje_ayuda" => "En el módulo 'TRASFERIR UN PROCESO AL JEFE DE OTRA DEPENDENCIA', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de poder transferir un proceso al jefe de otra dependencia."
            ],

            [
                "nombre" => "EnviarAJefe",
                "nombre_mostrar" => "TRASFERIR UN PROCESO AL JEFE DE MI DEPENDENCIA",
                "estado" => true,
                "id_mas_grupo" => 5,
                "orden" => 5,
                "mensaje_ayuda" => "En el módulo 'TRASFERIR UN PROCESO AL JEFE DE MI DEPENDENCIA', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de poder transferir un proceso al jefe de mi dependencia."
            ],

            [
                "nombre" => "RegresarUltimoUsuario",
                "nombre_mostrar" => "TRANSFERIR UN PROCESO AL ÚLTIMO USUARIO QUE LA GESTIONÓ",
                "estado" => true,
                "id_mas_grupo" => 5,
                "orden" => 6,
                "mensaje_ayuda" => "En el módulo 'TRANSFERIR UN PROCESO AL ÚLTIMO USUARIO QUE LA GESTIONÓ', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de poder transferir un proceso al último usuario que gestionó este proceso."
            ],

            [
                "nombre" => "Buscador",
                "nombre_mostrar" => "CONSULTAR BUSCADOR",
                "estado" => true,
                "id_mas_grupo" => 6,
                "orden" => 1,
                "mensaje_ayuda" => "En el módulo 'CONSULTAR BUSCADOR', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' el cual permite acceder al menú 'BUSCADOR DE EXPEDIENTES' ubicado en la sección 'SISTEMA DISCIPLINARIOS' que se encuentra en la parte izquierda de la pantalla.
                    Este permiso también habilita o deshabilita la opción en la pantalla 'RAMAS DEL PROCESO'."
            ],

            [
                "nombre" => "CP_Asignado",
                "nombre_mostrar" => "CONSULTAR LOS PROCESOS COMPLETOS QUE ESTÁN EN MIS PENDIENTES",
                "estado" => true,
                "id_mas_grupo" => 6,
                "orden" => 2,
                "mensaje_ayuda" => "En el módulo 'CONSULTAR LOS PROCESOS COMPLETOS QUE ESTÁN EN MIS PENDIENTES', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le permite al usuario consultar toda la información de los procesos disciplinarios que están en su bandeja de pendientes."
            ],

            [
                "nombre" => "CP_NOAsignado",
                "nombre_mostrar" => "CONSULTAR UN PROCESO COMPLETO DE CUALQUIER USUARIO DE UNA DEPENDENCIA",
                "estado" => true,
                "id_mas_grupo" => 6,
                "orden" => 2,
                "mensaje_ayuda" => "En el módulo 'CONSULTAR UN PROCESO COMPLETO DE CUALQUIER USUARIO DE UNA DEPENDENCIA', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le permite al usuario consultar toda la información de los procesos disciplinarios que no hacen parte de su bandeja de pendientes."
            ],

            [
                "nombre" => "CH_ReclasificacionExpediente",
                "nombre_mostrar" => "RECLASIFICACION DEL TIPO DE EXPEDIENTE",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 1,
                "mensaje_ayuda" => "En el módulo 'RECLASIFICACIÓN DEL TIPO DE EXPEDIENTE', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le habilita al usuario la opción a un usuario jefe la opción de re-clasificar el tipo de expediente de un proceso.
                    Este permiso se puede visualizar en la caja de herramientas disponible en la pantalla 'RAMAS DEL PROCESO'."
            ],

            [
                "nombre" => "CH_ReclasificacionEvaluacion",
                "nombre_mostrar" => "RECLASIFICACION DEL TIPO DE EVALUACIÓN",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 2,
                "mensaje_ayuda" => "El permiso 'CONSULTAR' le habilita al usuario la opción a un usuario jefe la opción de re-clasificar el tipo de evaluación de un proceso.
                    Este permiso se puede visualizar en la caja de herramientas disponible en la pantalla 'RAMAS DEL PROCESO'."
            ],

            [
                "nombre" => "CH_TipoConducta",
                "nombre_mostrar" => "TIPO DE CONDUCTA",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 3,
                "mensaje_ayuda" => "El permiso 'CONSULTAR' le habilita al usuario la opción de registrar un nuevo tipo de conducta al proceso.
                    Este permiso se puede visualizar en la caja de herramientas disponible en la pantalla 'RAMAS DEL PROCESO'."
            ],

            [
                "nombre" => "CH_UsuarioComisionado",
                "nombre_mostrar" => "ASIGNAR USUARIO COMISIONADO",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 4,
                "mensaje_ayuda" => "En el módulo 'ASIGNAR USUARIO COMISIONADO', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le habilita al usuario la opción de asignar un usuario comisionado al proceso.
                    Este permiso se puede visualizar en la caja de herramientas disponible en la pantalla 'RAMAS DEL PROCESO'. "
            ],

            [
                "nombre" => "CH_DeclararmeImpedido",
                "nombre_mostrar" => "DECLARARME IMPEDIDO",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 5,
                "mensaje_ayuda" => "En el módulo ‘DECLARARME IMPEDIDO', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le habilita al usuario la opción de declararse impedido en un proceso.
                    Este permiso se puede visualizar en la caja de herramientas disponible en la pantalla 'RAMAS DEL PROCESO'."
            ],

            [
                "nombre" => "CH_DuenaProceso",
                "nombre_mostrar" => "ASIGNAR NUEVA DEPENDENCIA DUEÑA DEL PROCESO",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 6,
                "mensaje_ayuda" => "En el módulo ‘ASIGNAR NUEVA DEPENDENCIA DUEÑA DEL PROCESO', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le habilita al usuario la opción de asignar una nueva dependencia dueña del proceso.
                    Este permiso se puede visualizar en la caja de herramientas disponible en la pantalla 'RAMAS DEL PROCESO'."
            ],

            [
                "nombre" => "CH_Caratula",
                "nombre_mostrar" => "CARÁTULA",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 7,
                "mensaje_ayuda" => "En el módulo ‘CARÁTULA', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le habilita al usuario la opción de generar la carátula."
            ],

            [
                "nombre" => "CH_Transacciones",
                "nombre_mostrar" => "TRANSACCIONES",
                "estado" => true,
                "id_mas_grupo" => 8,
                "orden" => 8,
                "mensaje_ayuda" => "En el módulo ‘TRANSACCIONES', se encuentra disponible el permiso 'CONSULTAR'.
                    El permiso 'CONSULTAR' le habilita al usuario la opción de acceder a la opción transacciones."
            ],

            [
                "nombre" => "G_IniciarProceso",
                "nombre_mostrar" => "INICIAR PROCESO DISCIPLINARIO",
                "estado" => true,
                "id_mas_grupo" => 9,
                "orden" => 2,
                "mensaje_ayuda" => "El módulo 'INICIAR PROCESO DISCIPLINARIO' tiene el permiso 'GESTIONAR', el cual permite acceder al menú 'INICIAR PROCESO'
                    ubicado en la sección 'SISTEMA DISCIPLINARIOS' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "Jefe",
                "nombre_mostrar" => "ES JEFE",
                "estado" => true,
                "id_mas_grupo" => 9,
                "orden" => 1,
                "mensaje_ayuda" => "El permiso 'ES JEFE' tiene el permiso ‘GESTIONAR’, el cual permite asignar el rol de jefe,
                    y se da la prioridad a este usuario en  ‘GESTOR RESPUESTA’ cuando se realiza el reparto."
            ],

            [
                "nombre" => "ADMIN_Perfiles",
                "nombre_mostrar" => "PERFILES",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 1,
                "mensaje_ayuda" => "El permiso 'PERFILES' tiene el permiso ‘CONSULTAR’, ‘CREAR’ e ‘ACTIVAR/INACTIVAR’,
                    el cual permite acceder al menú 'PERFILES' ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "ADMIN_ProcesoDisciplinario",
                "nombre_mostrar" => "PROCESO DISCIPLINARIO",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 2,
                "mensaje_ayuda" => "El permiso 'PROCESO DISCIPLINARIO' tiene el permiso ‘CONSULTAR’, ‘CREAR’ e ‘ACTIVAR/INACTIVAR’,
                    el cual permite acceder al menú 'PERFILES' ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "ADMIN_Actuaciones",
                "nombre_mostrar" => "ACTUACIONES",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 3,
                "mensaje_ayuda" => "El permiso 'ACTUACIONES' tiene el permiso ‘CONSULTAR’, ‘CREAR’ e ‘ACTIVAR/INACTIVAR’,
                    el cual permite acceder al menú 'ACTUACIONES' ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "ADMIN_Caratula",
                "nombre_mostrar" => "CARÁTULA",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 4,
                "mensaje_ayuda" => "El permiso 'CARÁTULA' tiene el permiso ‘CONSULTAR’, ‘CREAR’ e ‘ACTIVAR/INACTIVAR’,
                    el cual permite acceder al menú 'CARÁTULA'  ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "ADMIN_TrasladoCasos",
                "nombre_mostrar" => "TRASLADO DE CASOS",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 5,
                "mensaje_ayuda" => "El permiso 'TRASLADO DE CASOS' tiene el permiso ‘GESTIONAR‘,
                    el cual permite acceder al menú 'TRASLADO DE CASOS'  ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "ADMIN_InformeGeneral",
                "nombre_mostrar" => "INFORME GENERAL",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 6,
                "mensaje_ayuda" => "El permiso 'INFORMES' tiene el permiso ‘GESTIONAR’,
                    el cual permite acceder al menú 'INFORMES'  ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "ADMIN_PortalWeb",
                "nombre_mostrar" => "PORTAL WEB",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 7,
                "mensaje_ayuda" => "El permiso 'PORTAL WEB' tiene el permiso ‘CONSULTAR’, ‘CREAR’ e ‘ACTIVAR/INACTIVAR’,
                el cual permite acceder al menú 'PORTAL WEB'  ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla."
            ],

            [
                "nombre" => "ADMIN_Otros",
                "nombre_mostrar" => "OTROS",
                "estado" => true,
                "id_mas_grupo" => 10,
                "orden" => 8,
                "mensaje_ayuda" => "El permiso 'OTROS' tiene el permiso ‘CONSULTAR’, ‘CREAR’ e ‘ACTIVAR/INACTIVAR’, el cual permite acceder al menú 'OTROS'  ubicado en la sección 'ADMINISTRACIÓN' que se encuentra en la parte izquierda de la pantalla.
                    Esta opción hace referencia a las tablas maestras del sistema."
            ],


            [
                "nombre" => "A_CambiarFaseProcesoDisciplinario",
                "nombre_mostrar" => "CAMBIAR FASE PROCESO DISCIPLINARIO",
                "estado" => false,
                "id_mas_grupo" => null,
                "orden" => 1,
                "mensaje_ayuda" => ""
            ],

            [
                "nombre" => "EnviarAMisPendientes",
                "nombre_mostrar" => "TRASNFERIR UN PROCESO A MI BANDEJA DE PENDIENTES",
                "estado" => true,
                "id_mas_grupo" => 5,
                "orden" => 5,
                "mensaje_ayuda" => "En el módulo 'TRASNFERIR UN PROCESO A MI BANDEJA DE PENDIENTES', se encuentra disponible el permiso 'GESTIONAR'.
                    El permiso 'GESTIONAR' le habilita al usuario la opción de poder transferir un proceso a la bandeja de mis pendientes."
            ],

        ];
    }
}
