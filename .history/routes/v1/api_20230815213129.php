<?php

use App\Http\Controllers\Api\ActivarFuncionalidadesController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\AntecedenteController;
use App\Http\Controllers\Api\DepartamentoController;
use App\Http\Controllers\Api\ClasificacionRadicadoController;
use App\Http\Controllers\Api\CiudadController;
use App\Http\Controllers\Api\DependenciaOrigenController;
use App\Http\Controllers\Api\EntidadInvestigadoController;
use App\Http\Controllers\Api\MisPendientesController;
use App\Http\Controllers\Api\OrigenRadicadoController;
use App\Http\Controllers\Api\ProcesoDiciplinarioController;
use App\Http\Controllers\Api\TipoDerechoPeticionController;
use App\Http\Controllers\Api\TipoExpedienteController;
use App\Http\Controllers\Api\TipoProcesoController;
use App\Http\Controllers\Api\TipoQuejaController;
use App\Http\Controllers\Api\TipoTerminoRespuestaController;
use App\Http\Controllers\Api\DatosInteresadoController;
use App\Http\Controllers\Api\TipoDocumentoController;
use App\Http\Controllers\Api\TipoEntidadController;
use App\Http\Controllers\Api\TipoSujetoProcesalController;
use App\Http\Controllers\Api\LocalidadController;
use App\Http\Controllers\Api\SexoController;
use App\Http\Controllers\Api\GeneroController;
use App\Http\Controllers\Api\OrientacionSexualController;
use App\Http\Controllers\Api\DiasNoLaboralesController;
use App\Http\Controllers\Api\VigenciaController;
use App\Http\Controllers\Api\TipoInteresadoController;
use App\Http\Controllers\Api\RegistraduriaController;
use App\Http\Controllers\Api\DocumentoSiriusController;
use App\Http\Controllers\Api\CierreEtapaController;
use App\Http\Controllers\Api\TipoRespuestaController;
use App\Http\Controllers\Api\EntidadController;
use App\Http\Controllers\Api\RemisionQuejaController;
use App\Http\Controllers\Api\InteresadoEntidadPermitidaController;
use App\Http\Controllers\Api\ComunicacionInteresadoController;
use App\Http\Controllers\Api\DocumentoCierreController;
use App\Http\Controllers\Api\EtapaController;
use App\Http\Controllers\Api\EvaluacionController;
use App\Http\Controllers\Api\LogProcesoDisciplinarioController;
use App\Http\Controllers\Api\ParametroController;
use App\Http\Controllers\Api\UsuarioController;
use App\Http\Controllers\Api\RoleController;
use App\Http\Controllers\Api\FuncionalidadController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FaseController;
use App\Http\Controllers\Api\FormatoController;
use App\Http\Controllers\Api\GestorRespuestaController;
use App\Http\Controllers\Api\TipoConductaController;
use App\Http\Controllers\Api\ResultadoEvaluacionController;
use App\Http\Controllers\Api\OrdenFuncionarioController;
use App\Http\Controllers\Api\BusquedaExpedienteController;
use App\Http\Controllers\Api\ValidarClasificacionController;
use App\Http\Controllers\Api\ParametrizacionActuacionesController;
use App\Http\Controllers\Api\EstadoActuacionesController;
use App\Http\Controllers\Api\ActuacionesController;
use App\Http\Controllers\Api\TrazabilidadActuacionesController;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\WordDocController;
use App\Http\Controllers\Api\TransaccionesController;
use App\Http\Controllers\Api\MasActuacionesController;
use App\Http\Controllers\Api\ArchivoActuacionesController;
use App\Http\Controllers\Api\FasesActivasProcesoDiciplinarioController;
use App\Http\Controllers\Api\InformeCierreController;
use App\Http\Controllers\Api\RequerimientoJuzgadoController;
use App\Http\Controllers\Api\MasParametroCamposController;
use App\Http\Controllers\Api\BuscadorController;
use App\Http\Controllers\Api\DireccionBisController;
use App\Http\Controllers\Api\DireccionComplementoController;
use App\Http\Controllers\Api\DireccionLetrasController;
use App\Http\Controllers\Api\DireccionNomenclaturaController;
use App\Http\Controllers\Api\DireccionOrientacionController;
use App\Http\Controllers\Api\EntidadFuncionarioQuejaInternaController;
use App\Http\Controllers\Api\EstadoProcesoDisciplinarioController;
use App\Http\Controllers\Api\EvaluacionFaseController;
use App\Http\Controllers\Api\RegistroSeguimientoController;
use App\Http\Controllers\Api\TipoConductaProcesoDisciplinarioController;
use App\Http\Controllers\Api\LogConsultasController;
use App\Http\Controllers\Api\MasCaratulasController;
use App\Http\Controllers\Api\TipoExpedienteMensajesController;
use App\Http\Controllers\Api\TipoUnidadController;
use App\Http\Controllers\Api\ParametroCamposCaratulasController;
use App\Http\Controllers\Api\MasTipoFirmaController;
use App\Http\Controllers\Api\SemaforoController;
use App\Http\Controllers\Api\MasEventoInicioController;
use App\Http\Controllers\Api\CondicionController;
use App\Http\Controllers\Api\AutoFinalizaController;
use App\Http\Controllers\Api\MigracionController;
use App\Http\Controllers\Api\PreguntasDocumentoCierreController;
use App\Http\Controllers\Api\ProcesoDisciplinarioPorSemaforoController;
use App\Http\Controllers\Api\TipoFuncionarioController;
use App\Http\Controllers\Api\PortalLogController;
use App\Http\Controllers\Api\PortalNotificacionesController;
use App\Http\Controllers\Api\PortalConfiguracionTipoInteresadoController;
use App\Http\Controllers\Api\GrupoTrabajoSecretariaComunController;
use App\Http\Controllers\Api\TempActuacionesController;
use App\Http\Controllers\Api\TempAntecedentesController;
use App\Http\Controllers\Api\TempEntidadesController;
use App\Http\Controllers\Api\TempInteresadosController;
use App\Http\Controllers\Api\TempProcesoDisciplinarioController;
use App\Http\Controllers\Api\ActuacionPorSemaforoController;
use App\Http\Controllers\Api\CargosController;
use App\Http\Controllers\Api\MasConsecutivoActuacionesController;
use App\Http\Controllers\Api\MasEstadoVisibilidadController;
use App\Http\Controllers\Api\SectorController;
use App\Http\Controllers\Api\CambioEtapaController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\PortalController;
use App\Http\Controllers\Api\SecuenciaController;

Route::middleware(['cors'])->group(function ($route) {
    Route::apiResource("Test", TestController::class);

    //Authorization
    Route::post('Auth/Login',  [AuthController::class, 'login']);
    Route::middleware('auth:sanctum')->get('Auth/user',  [AuthController::class, 'user']);
    Route::middleware('auth:sanctum')->post('Auth/hasAccess',  [AuthController::class, 'hasAccess']);
    Route::middleware('auth:sanctum')->get('Auth/users/{criteria}',  [AuthController::class, 'users']);

    // DASHBOARD - HOME
    Route::middleware('auth:sanctum')->get('inicio/',  [HomeController::class, 'getDashboard']);
    Route::middleware('auth:sanctum')->get('documentos-por firmar',  [HomeController::class, 'DocumentosPendientesDeFirmaPorUsuario']);
    Route::middleware('auth:sanctum')->get('procesos-por-expediente',  [HomeController::class, 'getProcesosPorTipoExpediente']);
    Route::middleware('auth:sanctum')->get('procesos-por-etapa',  [HomeController::class, 'getProcesosPorEtapa']);


    //usuarios
    Route::middleware('auth:sanctum')->apiResource("usuario", UsuarioController::class)->names("api.v1.usuario");
    Route::middleware('auth:sanctum')->post('usuario/get-usuario-funcionalidad/',  [UsuarioController::class, 'getUserByFunctionality']);
    Route::middleware('auth:sanctum')->get('usuario/get-usuarios-dependencia/{idDependencia}/{idSubTipoExpediente}/{idTipoExpediente}',  [UsuarioController::class, 'getUsuariosPorDependencia']);
    Route::middleware('auth:sanctum')->get('usuario/get-all-usuarios-dependencia/{idDependencia}',  [UsuarioController::class, 'getAllUsuariosPorDependencia']);
    Route::middleware('auth:sanctum')->get('usuario/usuario-paginate/{paginaActual}/{porPagina}',  [UsuarioController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->get('getUsarios/{estado}',  [UsuarioController::class, 'getUsarios']);
    Route::middleware('auth:sanctum')->get('getAllUsuarios',  [UsuarioController::class, 'getAllUsuarios']);
    Route::middleware('auth:sanctum')->post('usuario/usuario-filter',  [UsuarioController::class, 'getUsuarioFilter']);
    Route::middleware('auth:sanctum')->post('usuario/get-jefe-dependencia',  [UsuarioController::class, 'validarJefeDependencia']);
    Route::middleware('auth:sanctum')->post('usuario/get-jefe-dependencia-sin-validar',  [UsuarioController::class, 'getJefeDependencia']);
    Route::middleware('auth:sanctum')->post('usuario/get-jefe-de-mi-dependencia',  [UsuarioController::class, 'obtenerJefeDeMiDependencia']);
    Route::middleware('auth:sanctum')->get('usuario/get-todos-usuarios-dependencia/{idDependencia}',  [UsuarioController::class, 'getTodosLosUsuariosPorDependencia']);
    Route::middleware('auth:sanctum')->get('usuario/get-todos-usuarios-dependencia-permisos-disciplinarios/{id_proceso_disciplinario}/{idDependencia}',  [UsuarioController::class, 'getTodosLosUsuariosPorDependenciaPermisosDisciplinarios']);
    Route::middleware('auth:sanctum')->get('usuario/get-todos-usuarios-dependencia-actuaciones/{idDependencia}/{id_proceso_disciplinario}',  [UsuarioController::class, 'getTodosLosUsuariosPorDependenciaActuaciones']);
    Route::middleware('auth:sanctum')->get('usuario/get-usuario-por-name/{name}',  [UsuarioController::class, 'getUsuarioPorName']);
    Route::middleware('auth:sanctum')->put('usuario/set-firma-mecanica/{id}',  [UsuarioController::class, 'actualizar_firma']);
    Route::middleware('auth:sanctum')->post('usuario/get-firma-mecanica/{id}',  [UsuarioController::class, 'getFirmaMecanica']);
    Route::middleware('auth:sanctum')->post('usuario/get-firma-mecanica-ejemplo',  [UsuarioController::class, 'getFirmaMecanicaEjemplo']);
    Route::middleware('auth:sanctum')->get('usuario/get-usuarios-grupotrabajo/{idGrupoTrabajo}/{id_proceso_disciplinario}',  [UsuarioController::class, 'getTodosLosUsuariosPorGrupoTrabajo']);

    //roles
    Route::middleware('auth:sanctum')->apiResource("role", RoleController::class)->names("api.v1.role");
    Route::middleware('auth:sanctum')->get('role/role-paginate/{paginaActual}/{porPagina}',  [RoleController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->get('roles/gestor-respuesta',  [RoleController::class, 'allRolesGestorRespuesta']);

    //funcionalidades
    Route::middleware('auth:sanctum')->apiResource("funcionalidad", FuncionalidadController::class)->names("api.v1.funcionalidad");

    //modulo
    Route::middleware('auth:sanctum')->apiResource("funcionalidad", FuncionalidadController::class)->names("api.v1.funcionalidad");
    Route::middleware('auth:sanctum')->get('modulo/get-modulos',  [FuncionalidadController::class, 'getModulos']);

    Route::middleware('auth:sanctum')->get('modulo/get-funcionalidad-modulo/{nombre}',  [FuncionalidadController::class, 'getFuncionalidadByModulo']);
    Route::middleware('auth:sanctum')->apiResource("modulo-grupo", FuncionalidadController::class)->names("api.v1.modulo-grupo");
    Route::middleware('auth:sanctum')->get("modulo/get-modulo-grupo", [FuncionalidadController::class, 'getGruposPermisos']);


    Route::post('sirius/radicacion', [App\Http\Controllers\Api\SiriusWSController::class, "radicacion"])->name("api.v1.sirius.radicacion");
    Route::get('sirius/search-radicado', [App\Http\Controllers\Api\SiriusWSController::class, "searchRadicado"])->name("api.v1.sirius.search-radicado");
    Route::post('sirius/upload-document', [App\Http\Controllers\Api\SiriusWSController::class, "uploadDocument"])->name("api.v1.sirius.upload-document");

    //*GROUP
    //Departamentos
    Route::middleware('auth:sanctum')->apiResource("departments", DepartamentoController::class)->names("api.v1.departments");
    Route::middleware('auth:sanctum')->apiResource('departamento', DepartamentoController::class)->names("api.v1.departments");
    Route::middleware('auth:sanctum')->get('departamento/departamento-paginate/{paginaActual}/{porPagina}',  [DepartamentoController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->get('getDepartamentos/{estado}',  [DepartamentoController::class, 'getDepartamentos']);
    Route::middleware('auth:sanctum')->get('departamentos-activos',  [DepartamentoController::class, 'getDepartamentosActivos']);

    //parametro
    Route::middleware('auth:sanctum')->apiResource("parametro", ParametroController::class)->names("api.v1.mas-parametro");
    Route::middleware('auth:sanctum')->post('parametro/parametro-nombre',  [ParametroController::class, 'getParameterByName']);
    Route::middleware('auth:sanctum')->get('parametro/parametro-paginate/{paginaActual}/{porPagina}',  [ParametroController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->apiResource("tipo-documento", TipoDocumentoController::class)->names("api.v1.mas-tipo-documento");
    Route::middleware('auth:sanctum')->get('tipo-documento/tipo-documento-paginate/{paginaActual}/{porPagina}',  [TipoDocumentoController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->get('getTipoDocumento/{estado}',  [TipoDocumentoController::class, 'getTipoDocumento']);
    Route::middleware('auth:sanctum')->apiResource("tipo-entidad", TipoEntidadController::class)->names("api.v1.mas-tipo-entidad");
    Route::middleware('auth:sanctum')->apiResource("tipo-sujeto-procesal", TipoSujetoProcesalController::class)->names("api.v1.mas-tipo-sujeto-procesal");
    Route::middleware('auth:sanctum')->apiResource("mas-localidad", LocalidadController::class)->names("api.v1.mas-localidad");
    Route::middleware('auth:sanctum')->get('mas-localidad/localidad-paginate/{paginaActual}/{porPagina}',  [LocalidadController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->get('getMasLocalidad/{estado}',  [LocalidadController::class, 'getMasLocalidad']);
    Route::middleware('auth:sanctum')->apiResource("sexo", SexoController::class)->names("api.v1.mas-sexo");
    Route::middleware('auth:sanctum')->get('getSexo/{estado}',  [SexoController::class, 'getSexo']);
    Route::middleware('auth:sanctum')->apiResource("genero", GeneroController::class)->names("api.v1.mas-genero");
    Route::middleware('auth:sanctum')->get('getGenero/{estado}',  [GeneroController::class, 'getGenero']);
    Route::middleware('auth:sanctum')->apiResource("orientacion-sexual", OrientacionSexualController::class)->names("api.v1.mas-orientacion-sexual");
    Route::middleware('auth:sanctum')->get('getOrientacionSexual/{estado}',  [OrientacionSexualController::class, 'getOrientacionSexual']);
    Route::middleware('auth:sanctum')->apiResource("dias-no-laborales", DiasNoLaboralesController::class)->names("api.v1.mas-dias-no-laborales");
    Route::middleware('auth:sanctum')->apiResource("vigencia", VigenciaController::class)->names("api.v1.mas-vigencia");
    Route::middleware('auth:sanctum')->get('vigencia/vigencia-paginate/{paginaActual}/{porPagina}',  [VigenciaController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->get('getVigencias/{estado}',  [VigenciaController::class, 'getVigencias']);
    Route::middleware('auth:sanctum')->apiResource("tipo-interesado", TipoInteresadoController::class)->names("api.v1.mas-tipo-interesado");
    Route::middleware('auth:sanctum')->apiResource("tipo-respuesta", TipoRespuestaController::class)->names("api.v1.mas-tipo-respuesta");

    //ciudades
    Route::middleware('auth:sanctum')->apiResource("ciudad", CiudadController::class)->names("api.v1.ciudades");
    Route::middleware('auth:sanctum')->get('ciudad/ciudad-paginate/{paginaActual}/{porPagina}',  [CiudadController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->get('getCiudad/{estado}',  [CiudadController::class, 'getCiudad']);
    Route::middleware('auth:sanctum')->post('ciudad/ciudad-por-departamento',  [CiudadController::class, 'getCiudadesPorDepartamento']);

    // CONEXIÓN CON REGISTRADURIA
    Route::middleware('auth:sanctum')->post('registraduria/search-documento/{documento}',  [RegistraduriaController::class, 'validarDocumentoRegistraduria']);

    //Proceso Diciplinario
    Route::middleware('auth:sanctum')->apiResource("proceso-diciplinario", ProcesoDiciplinarioController::class)->names("api.v1.proceso-diciplinario");
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/validar-sirius',  [ProcesoDiciplinarioController::class, 'validarProcesoSirius']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/validar-sinproc',  [ProcesoDiciplinarioController::class, 'validarProcesoSinproc']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/validar-poder-preferente',  [ProcesoDiciplinarioController::class, 'validarProcesoPoderPreferente']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/validar-documento-sinproc/{documento}',  [ProcesoDiciplinarioController::class, 'validarDocumentoSINPROC']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/validar-desglose',  [ProcesoDiciplinarioController::class, 'validarProcesoDesglose']);
    Route::middleware('auth:sanctum')->get('proceso-diciplinario/get-fases-registradas/{id_proceso_disciplinario}',  [FasesActivasProcesoDiciplinarioController::class, 'getFasesProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/traslado-masivo',  [ProcesoDiciplinarioController::class, 'trasladoMasivoCasos']);
    Route::middleware('auth:sanctum')->get('proceso-diciplinario/tipo-proceso-disciplinario/{id_proceso_disciplinario}',  [ProcesoDiciplinarioController::class, 'getTipoProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/usuario-comisionado/{id_usuario_comisionado}/{id_proceso_disciplinario}',  [ProcesoDiciplinarioController::class, 'updateUsuarioComisionado']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/archivar-por-actuacion/{id_proceso_disciplinario}',  [ProcesoDiciplinarioController::class, 'updateEstadoProcesoPorActuacion']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/id-dependencia-duena/{id_dependencia_duena}/{id_proceso_disciplinario}',  [ProcesoDiciplinarioController::class, 'updateIdDependenciaDuena']);
    Route::middleware('auth:sanctum')->post('proceso-diciplinario/validar-sinproc-portal-web',  [ProcesoDiciplinarioController::class, 'validarProcesoSinprocPortalWeb']);
    Route::middleware('auth:sanctum')->get('proceso-diciplinario/set-vigencia/{id_proceso_disciplinario}/{vigencia}',  [ProcesoDiciplinarioController::class, 'establecerVigencia']);
    Route::middleware('auth:sanctum')->get('proceso-diciplinario/usuario-habilitado-transacciones/{id_proceso_disciplinario}/{id_usuario}',  [ProcesoDiciplinarioController::class, 'usuarioHabilitadoParaTransacciones']);
    Route::middleware('auth:sanctum')->get('proceso-diciplinario/encabezadoDelProceso/{id_proceso_disciplinario}',  [ProcesoDiciplinarioController::class, 'encabezadoDelProceso']);

    //Antecedentes
    Route::middleware('auth:sanctum')->apiResource("antecedentes", AntecedenteController::class)->names("api.v1.antencedentes");
    Route::middleware('auth:sanctum')->post('antecedentes/get-antecedentes/{id_proceso_disciplinario}',  [AntecedenteController::class, 'getAllAntecedentesByIdProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->get('antecedentes/get-primer-ultima-antecedentes/{id_proceso_disciplinario}',  [AntecedenteController::class, 'getPrimerYUltimoAntecedente']);

    //Routes MAS
    Route::middleware('auth:sanctum')->apiResource('mas-origen-radicado',  OrigenRadicadoController::class)->names("api.v1.mas-origen-radicado");
    Route::middleware('auth:sanctum')->get('getMasOrigenRadicado/{estado}',  [OrigenRadicadoController::class, 'getMasOrigenRadicado']);
    Route::middleware('auth:sanctum')->apiResource('mas-tipo-proceso',  TipoProcesoController::class)->names("api.v1.mas-tipo-proceso");
    Route::middleware('auth:sanctum')->get('mas-tipo-proceso-activos',  [TipoProcesoController::class, 'getTipoProcesoActivos']);
    Route::middleware('auth:sanctum')->apiResource('mas-dependencia-origen',  DependenciaOrigenController::class)->names("api.v1.mas-dependencia-origen");
    Route::middleware('auth:sanctum')->get('mas-dependencia-origen-activas',  [DependenciaOrigenController::class, 'getDependenciasActivas']);
    Route::middleware('auth:sanctum')->get('mas-dependencia-accesos',  [DependenciaOrigenController::class, 'getDependenciasAcesso']);
    Route::middleware('auth:sanctum')->get('mas-dependencia-configuracion/{idDependencia}',  [DependenciaOrigenController::class, 'validarAccesoSecretariaComun']);
    Route::middleware('auth:sanctum')->get('mas-dependencia-secretaria-comun',  [DependenciaOrigenController::class, 'validarAccesoSecretariaComun']);
    Route::middleware('auth:sanctum')->get('getMasDependenciaOrigen/{estado}',  [DependenciaOrigenController::class, 'getMasDependenciaOrigen']);
    Route::middleware('auth:sanctum')->get('mas-dependencia-filtrado/{idMasDependenciaAcceso}',  [DependenciaOrigenController::class, 'cargarDependenciasSegunConfiguracion']);
    Route::middleware('auth:sanctum')->get('mas-dependencia-filtrado-remision-queja/{idMasDependenciaAcceso}',  [DependenciaOrigenController::class, 'cargarDependenciasSegunConfiguracionRemisionQueja']);
    Route::middleware('auth:sanctum')->get('mas-dependencia-actuacion/{idDependencia}',  [DependenciaOrigenController::class, 'cargarDependenciasConfiguracionActuacion']);
    Route::middleware('auth:sanctum')->get('mas-dependencia-origen/dependencia-paginategeTipoQuejaSinEstado/{paginaActual}/{porPagina}',  [DependenciaOrigenController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->apiResource('mas-tipo-expediente',  TipoExpedienteController::class)->names("api.v1.mas-tipo-expediente");
    Route::middleware('auth:sanctum')->get('getMasTipoExpediente/{estado}',  [TipoExpedienteController::class, 'getMasTipoExpediente']);
    Route::middleware('auth:sanctum')->apiResource('mas-tipo-derecho-peticion',  TipoDerechoPeticionController::class)->names("api.v1.mas-tipo-derecho-peticion");
    Route::middleware('auth:sanctum')->get('getMasTipoDerechoPeticion/{estado}',  [TipoDerechoPeticionController::class, 'getMasTipoDerechoPeticion']);
    Route::middleware('auth:sanctum')->apiResource('mas-termino-respuesta',  TipoTerminoRespuestaController::class)->names("api.v1.mas-termino-respuesta");
    Route::middleware('auth:sanctum')->apiResource('mas-tipo-queja',  TipoQuejaController::class)->names("api.v1.mas-tipo-queja");

    Route::middleware('auth:sanctum')->get('getMasTipoQueja/{estado}',  [TipoQuejaController::class, 'getMasTipoQueja']);
    Route::middleware('auth:sanctum')->get('lista-tipo-expediente/{id_proceso_disciplinario}', [TipoExpedienteController::class, 'getTiposExpedientesHabilitados']);
    Route::middleware('auth:sanctum')->get('lista-tipo-queja/{id_proceso_disciplinario}', [TipoQuejaController::class, 'getTiposQuejaHabilitados']);
    Route::middleware('auth:sanctum')->get('lista-tipo-queja', [TipoQuejaController::class, 'getTiposQueja']);
    Route::middleware('auth:sanctum')->get('lista-terminos-respuesta/{id_proceso_disciplinario}', [TipoTerminoRespuestaController::class, 'getTiposTerminosRespuestaTutelaHabilitados']);
    Route::middleware('auth:sanctum')->get('lista-tipo-derecho-peticion/{id_proceso_disciplinario}', [TipoDerechoPeticionController::class, 'getTiposDerechoPeticionHabilitados']);
    Route::middleware('auth:sanctum')->apiResource('mas-fase',  FaseController::class)->names("api.v1.mas-fase");
    Route::middleware('auth:sanctum')->get('getMasFase/{estado}',  [FaseController::class, 'getMasFase']);
    Route::middleware('auth:sanctum')->get('mas-fase-estado/{id_etapa}',  [FaseController::class, 'getFaseEtapa']);
    Route::middleware('auth:sanctum')->apiResource('mas-etapa',  EtapaController::class)->names("api.v1.mas-etapa");
    Route::middleware('auth:sanctum')->get('getMasEtapa/{estado}',  [EtapaController::class, 'getMasEtapa']);
    Route::middleware('auth:sanctum')->get('mas-etapa-nuevos',  [EtapaController::class, 'getEtapaNuevos']);
    Route::middleware('auth:sanctum')->apiResource('mas-formato',  FormatoController::class)->names("api.v1.mas-formato");
    Route::middleware('auth:sanctum')->get('getMasFormato/{estado}',  [FormatoController::class, 'getMasFormato']);
    Route::middleware('auth:sanctum')->get('mas-formato/formato-paginate/{paginaActual}/{porPagina}',  [FormatoController::class, 'indexPaginate']);
    Route::middleware('auth:sanctum')->apiResource('mas-orden-funcionario',  OrdenFuncionarioController::class)->names("api.v1.mas-orden-funcionario");
    Route::middleware('auth:sanctum')->get('mas-orden-funcionario/lista-roles/{id_evaluacion}/{id_expediente}/{id_sub_expediente}/{id_tercer_expediente}',  [OrdenFuncionarioController::class, 'showListaRoles']);
    Route::middleware('auth:sanctum')->get('mas-orden-funcionario/historico/{id_evaluacion}/{id_expediente}/{id_sub_expediente}/{id_tercer_expediente}',  [OrdenFuncionarioController::class, 'showHistorico']);
    Route::middleware('auth:sanctum')->apiResource('mas-estado-proceso-disciplinario',  EstadoProcesoDisciplinarioController::class)->names("api.v1.mas-estado-proceso-disciplinario");
    Route::middleware('auth:sanctum')->get('dependencias-eje-disciplinario',  [DependenciaOrigenController::class, 'cargarDependenciasEjeDisciplinario']);

    //Route Consulta Procesos Disciplinarios
    Route::middleware('auth:sanctum')->apiResource('mis-pendientes',  MisPendientesController::class)->names("api.v1.mis-pendientes");
    Route::middleware('auth:sanctum')->post('mis-pendientes-filter',  [MisPendientesController::class, 'getMisPendientesFilter']);
    Route::middleware('auth:sanctum')->post('mis-pendientes-filter-actual',  [MisPendientesController::class, 'getMisPendientesFilterMasivo']);

    //entidades
    Route::middleware('auth:sanctum')->apiResource('entidades',  EntidadController::class)->names("api.v1.entidades");
    Route::middleware('auth:sanctum')->apiResource('sectores',  SectorController::class)->names("api.v1.sectores");

    //Route Clasificacion Radicado
    Route::middleware('auth:sanctum')->apiResource('clasificacion-radicado',  ClasificacionRadicadoController::class)->names("api.v1.clasificacion-radicado");
    Route::middleware('auth:sanctum')->post('clasificacion-radicado/get-clasificacion-radicado/{id_proceso_disciplinario}',  [ClasificacionRadicadoController::class, 'getClasificacionRadicadoByIdDisciplinario']);
    Route::middleware('auth:sanctum')->post('clasificacion-radicado/get-clasificacion-radicado-filter/{id_proceso_disciplinario}',  [ClasificacionRadicadoController::class, 'getClasificacionRadicadoFilter']);
    Route::middleware('auth:sanctum')->get('clasificacion-radicado/get-reclasificacion/{id_proceso_disciplinario}',  [ClasificacionRadicadoController::class, 'getReclasificacion']);
    Route::middleware('auth:sanctum')->get('proceso-disciplinario/tipo-expdiente/{id_proceso_disciplinario}',  [TipoExpedienteController::class, 'getExpedientesByTipoProcesoDisciplinario']);

    //Validar clasificado
    Route::middleware('auth:sanctum')->apiResource('validar-clasificacion',  ValidarClasificacionController::class)->names("api.v1.validar-clasificacion");
    Route::middleware('auth:sanctum')->post('clasificacion-radicado/asignar-caso-por-jefe',  [ClasificacionRadicadoController::class, 'asignacionClasificacionByJefe']);
    Route::middleware('auth:sanctum')->post('validar-clasificacion/get-validar-clasificado/{id_proceso_disciplinario}',  [ValidarClasificacionController::class, 'getValidarClasificado']);
    Route::middleware('auth:sanctum')->get('validar-clasificacion/get-validar-clasificacion-jefe/{id_proceso_disciplinario}',  [ClasificacionRadicadoController::class, 'getValidarClasificadoPorJefe']);

    //Route Entidad Investigado
    Route::middleware('auth:sanctum')->apiResource('entidad-investigado',  EntidadInvestigadoController::class)->names("api.v1.entidad-investigado");
    Route::middleware('auth:sanctum')->post('entidad-investigado/get-entidad-investigado/{id_proceso_disciplinario}',  [EntidadInvestigadoController::class, 'getEntidadInvestigadoByIdDisciplinario']);
    Route::middleware('auth:sanctum')->post('entidad-investigado/get-entidad-investigado-filter/{id_proceso_disciplinario}',  [EntidadInvestigadoController::class, 'getEntidadInvestigadoFilter']);
    Route::middleware('auth:sanctum')->apiResource('entidad-investigado-qi',  EntidadFuncionarioQuejaInternaController::class)->names("api.v1.entidad-funcionario-qi");
    Route::middleware('auth:sanctum')->apiResource('tipo-funcionario',  TipoFuncionarioController::class)->names("api.v1.mas-tipo-funcionario");
    Route::middleware('auth:sanctum')->get('get-entidad-investigado-qi/{id_proceso_disciplinario}',  [EntidadFuncionarioQuejaInternaController::class, 'getEntidadesQuejaInternaByProcesoDisciplinario']);

    //Route Consulta Datos Interesado
    Route::middleware('auth:sanctum')->apiResource('datos-interesado',  DatosInteresadoController::class)->names("api.v1.datos-interesado");
    Route::middleware('auth:sanctum')->get('datos-interesado/datos-interesado-id/{id}',  [DatosInteresadoController::class, 'getDatosInteresadoById']);
    Route::middleware('auth:sanctum')->post('datos-interesado/datos-interesado/{id_proceso_disciplinario}',  [DatosInteresadoController::class, 'getDatosInteresadoByIdDisciplinario']);
    Route::middleware('auth:sanctum')->post('datos-interesado/datos-interesado-tipo-numero/',  [DatosInteresadoController::class, 'getInteresadoAntecedenteTipoNumero']);
    Route::middleware('auth:sanctum')->post('datos-interesado/getDatosInteresados/{numeroProcesoSinproc}',  [DatosInteresadoController::class, 'getDatosInteresadoByRadicado']);

    //comunicacion interesado
    Route::middleware('auth:sanctum')->apiResource('comunicacion-interesado',  ComunicacionInteresadoController::class)->names("api.v1.comunicacion-interesado");
    Route::middleware('auth:sanctum')->post('comunicacion-interesado/comunicacion-interesado-proceso/{id}',  [ComunicacionInteresadoController::class, 'getComunicacionInteresadoByProcesoDisciplinario']);

    //Route Consulta Interesado entidad permitida
    Route::middleware('auth:sanctum')->apiResource('mas-entidad-permitida',  InteresadoEntidadPermitidaController::class)->names("api.v1.mas-entidad-permitida");
    Route::middleware('auth:sanctum')->get('mas-entidad-permitida/entidad-permitida-paginate/{paginaActual}/{porPagina}',  [InteresadoEntidadPermitidaController::class, 'indexPaginate']);

    Route::middleware('auth:sanctum')->apiResource("entidad-permitida", InteresadoEntidadPermitidaController::class)->names("api.v1.mas-entidad-permitida");

    //Route Soporte del radicado
    Route::middleware('auth:sanctum')->apiResource('documento-sirius', DocumentoSiriusController::class)->names("api.v1.documento-sirius");
    Route::middleware('auth:sanctum')->post('documento-sirius/get-documentos-radicados/{id_proceso_disciplinario}/{per_page}/{current_page}/{estado}/{solo_sirius}',  [DocumentoSiriusController::class, 'getSoporteRadicadoByIdDisciplinario']);
    Route::middleware('auth:sanctum')->get('documento-sirius/get-nombres-documentos-radicados/{id_proceso_disciplinario}',  [DocumentoSiriusController::class, 'getNombresSoporteRadicadoByIdDisciplinario']);
    Route::middleware('auth:sanctum')->post('documento-sirius/get-documento',  [DocumentoSiriusController::class, 'getDocumento']);
    //Route::middleware('auth:sanctum')->get('documento-sirius/get-documentos-radicados-expediente-vigencia/{num_radicado}/{vigencia}',  [DocumentoSiriusController::class, 'getSoporteRadicadoByExpedienteAndVigencia']);
    Route::middleware('auth:sanctum')->get('documento-sirius/get-documentos-radicados-etapa-fase/{id_proceso_disciplinario}/{id_etapa}/{id_fase}',  [DocumentoSiriusController::class, 'getSoporteRadicadoByEtapaFase']);
    Route::middleware('auth:sanctum')->get('documento-sirius/get-documentos-radicados-expediente/{num_radicado}',  [DocumentoSiriusController::class, 'getSoporteRadicadoByExpediente']);
    Route::middleware('auth:sanctum')->get('documento-sirius/get-documentos-radicados-expediente-portal/{num_radicado}',  [DocumentoSiriusController::class, 'getSoporteRadicadoByExpedienteNotificaciones']);


    //Route Documento Cierre
    Route::middleware('auth:sanctum')->get('documento-cierre/get-documentos-radicados-etapa-fase/{id_proceso_disciplinario}/{id_etapa}/{id_fase}',  [DocumentoCierreController::class, 'showDocumentosCierre']);

    //Route Etapa
    Route::middleware('auth:sanctum')->apiResource('cierre-etapa', CierreEtapaController::class)->names("api.v1.cierre-etapa");
    Route::middleware('auth:sanctum')->post('cierre-etapa/evaluacion',  [CierreEtapaController::class, 'storeEvaluacion']);
    Route::middleware('auth:sanctum')->post('cierre-etapa/get-cierre-etapa',  [CierreEtapaController::class, 'getCierreByIdProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->post('cierre-etapa/procesos-diciplinario-asignados',  [CierreEtapaController::class, 'getProcesosDisciplinariosEnviadosPorUsuario']);
    Route::middleware('auth:sanctum')->get('etapas/poder-preferente',  [EtapaController::class, 'geEtapasPoderPreferenteActivas']);
    Route::middleware('auth:sanctum')->post('cierre-etapa/actuaciones',  [CierreEtapaController::class, 'cierreDeActuaciones']);
    Route::middleware('auth:sanctum')->get('cierre-etapa/tipo-reparto/{id_proceso_disciplinario}',  [CierreEtapaController::class, 'getTipoRepartoCierreEtapa']);


    /*EVALUACIÓN E INCORPORACIÓN*/

    //Route Evaluacion clasificacion radicado
    Route::middleware('auth:sanctum')->apiResource('evaluacion-tipo-expediente', EvaluacionTipoExpedienteController::class)->names("api.v1.evaluacion-tipo-expediente");

    //
    Route::middleware('auth:sanctum')->apiResource('mas-tipo-conducta', TipoConductaController::class)->names("api.v1.mas-tipo-conducta");

    //Route Evaluacion PODER PREFERENTE
    Route::middleware('auth:sanctum')->post('evaluacion-poder-preferente ',  [EvaluacionController::class, 'storeEvalucionPoderPreferente']);

    Route::middleware('auth:sanctum')->apiResource('mas-resultado-evaluacion', ResultadoEvaluacionController::class)->names("api.v1.mas-resultado-evaluacion");
    Route::middleware('auth:sanctum')->get('mas-resultado-evaluaciones/evaluaciones', [ResultadoEvaluacionController::class, 'showAllEvaluaciones']);
    Route::middleware('auth:sanctum')->apiResource('evaluacion', EvaluacionController::class)->names("api.v1.evaluacion");
    Route::middleware('auth:sanctum')->get('evaluacion-por-proceso/{id_proceso_disciplinario}',  [EvaluacionController::class, 'getAllEvalucionByIdProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->post('evaluacion-crear',  [EvaluacionController::class, 'crearEvaluacionRemitida']);
    Route::middleware('auth:sanctum')->post('actualizar-tipo-conducta', [EvaluacionController::class, 'updateTipoConducta']);
    Route::middleware('auth:sanctum')->get('lista-tipo-conducta/{id_proceso_disciplinario}', [TipoConductaController::class, 'getTiposConductasHabilitadas']);
    Route::middleware('auth:sanctum')->get('lista-tipo-evaluacion/{id_proceso_disciplinario}', [ResultadoEvaluacionController::class, 'getResultadoEvaluacionHabilitados']);
    Route::middleware('auth:sanctum')->get('evaluacion/get-estado-evaluacion/{id_proceso_disciplinario}', [EvaluacionController::class, 'getEstadoEvaluacion']);

    //Route remision queja
    Route::middleware('auth:sanctum')->apiResource('remision-queja', RemisionQuejaController::class)->names("api.v1.remision-queja");
    Route::middleware('auth:sanctum')->post('remision-queja/validacion-expediente',  [RemisionQuejaController::class, 'validarExpediente']);

    Route::middleware('auth:sanctum')->apiResource('documento-cierre', DocumentoCierreController::class)->names("api.v1.documento-cierre");
    Route::middleware('auth:sanctum')->apiResource('gestor-respuesta', GestorRespuestaController::class)->names("api.v1.gestor-respuesta");
    Route::middleware('auth:sanctum')->post('gestor-respuesta/subir-documento',  [GestorRespuestaController::class, 'storeWithDocumento']);
    Route::middleware('auth:sanctum')->get('gestor-respuesta/get-gestor-respuesta-proceso-disciplinario/{id_proceso_disciplinario}',  [GestorRespuestaController::class, 'getGestorRespuestaByProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->apiResource('mas-busqueda-expediente', BusquedaExpedienteController::class)->names("api.v1.mas-busqueda-expediente");

    //Route Logs
    Route::middleware('auth:sanctum')->apiResource("log-proceso-disciplinario", LogProcesoDisciplinarioController::class)->names("api.v1.log-proceso-disciplinario");
    Route::middleware('auth:sanctum')->get('log-proceso-disciplinario/get-log-etapa/{id_proceso_disciplinario}',  [LogProcesoDisciplinarioController::class, 'getLogEtapaByIdProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->get('log-proceso-disciplinario/get-log-proceso/{id_proceso_disciplinario}',  [LogProcesoDisciplinarioController::class, 'getLogByIdFaseRegistro']);
    Route::middleware('auth:sanctum')->post('log-proceso-disciplinario/get-documentos/{id_log_proceso_disciplinario}',  [DocumentoSiriusController::class, 'getDocumentosByIdLogProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->post('log-proceso-disciplinario/get-log-cierre-etapa',  [LogProcesoDisciplinarioController::class, 'getLogCierreEtapa']);
    Route::middleware('auth:sanctum')->post('log-proceso-disciplinario/get-casos-por-usuario',  [LogProcesoDisciplinarioController::class, 'getReporteCasosAsignadosPorUsuario']);
    Route::middleware('auth:sanctum')->post('log-proceso-disciplinario/get-reparto-casos',  [LogProcesoDisciplinarioController::class, 'getReporteCasos']);
    Route::middleware('auth:sanctum')->get('log-proceso-disciplinario/getReporteDetallado/{user}',  [LogProcesoDisciplinarioController::class, 'getReporteDetallado']);
    Route::middleware('auth:sanctum')->post('log-proceso-disciplinario/get-casos-por-dependencia',  [LogProcesoDisciplinarioController::class, 'getReporteCasosAsignadosPorDependencia']);
    Route::middleware('auth:sanctum')->get('log-proceso-disciplinario/getReporteDetalladoPorDependencia/{id_dependencia}',  [LogProcesoDisciplinarioController::class, 'getReporteDetalladoPorDependencia']);


    //Parametrizacion Actuaciones
    Route::middleware('auth:sanctum')->apiResource("parametrizacion-actuaciones", ParametrizacionActuacionesController::class)->names("api.v1.parametrizacion-actuaciones");
    Route::middleware('auth:sanctum')->post('parametrizacion-actuaciones/get-all-actuaciones',  [ParametrizacionActuacionesController::class, 'getAllParametrizacionActuaciones']);

    //Estados de las Actuaciones
    Route::middleware('auth:sanctum')->apiResource("estado-actuaciones", EstadoActuacionesController::class)->names("api.v1.estado-actuaciones");
    Route::middleware('auth:sanctum')->post('estado-actuaciones/get-all-estado-actuaciones',  [EstadoActuacionesController::class, 'getAllEstadoActuaciones']);

    //Actuaciones
    Route::middleware('auth:sanctum')->apiResource("actuaciones", ActuacionesController::class)->names("api.v1.actuaciones");
    Route::middleware('auth:sanctum')->get('actuaciones/get-all-actuaciones',  [ActuacionesController::class, 'getAllActuaciones']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-actuaciones-discipl-etapa/{id_proceso_disciplinario}/{id_etapa}/{estado}',  [ActuacionesController::class, 'getActuacionesDisciplinarioEtapa']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-actuaciones-discipl-etapa-documento-final/{id_proceso_disciplinario}/{id_etapa}/{estado}',  [ActuacionesController::class, 'getActuacionesDisciplinarioEtapaYDocumentoFinal']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-actuaciones-discipl-documento-final/{id_proceso_disciplinario}/{estado}',  [ActuacionesController::class, 'getActuacionesDisciplinarioYDocumentoFinal']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-actuaciones-active/{status}/{id_etapa}',  [ActuacionesController::class, 'getActuacionesEstadoActivo']);
    Route::middleware('auth:sanctum')->post('actuaciones/actuaciones-inactivar',  [ActuacionesController::class, 'actuacionesInactivar']);
    Route::middleware('auth:sanctum')->post('actuaciones/agregar-usuario-para-firma-mecanica',  [ActuacionesController::class, 'agregarUsuarioParaFirmaMecanica']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-firmas-por-actuacion/{id}',  [ActuacionesController::class, 'FirmasPorActuacion']);
    Route::middleware('auth:sanctum')->put('actuaciones/set-firmas/{id}',  [ActuacionesController::class, 'CambiarEstadoFirma']);
    Route::middleware('auth:sanctum')->put('actuaciones/set-eliminar-firmas-mecanicas/{id}',  [ActuacionesController::class, 'EliminarFirmaMecanicaDeActuacion']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-firmas-por-usuario/{id}',  [ActuacionesController::class, 'FirmasPorUsuario']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-documentos-pendientes-de-firma/{id}',  [ActuacionesController::class, 'DocumentosPendientesDeFirmaPorUsuario']);

    Route::middleware('auth:sanctum')->get('actuaciones/dependencia-tiene-comisorio-aprobado/{id_dependencia}/{id_proceso_disciplinario}',  [ActuacionesController::class, 'DependenciaConComisorioAprobado']);
    Route::middleware('auth:sanctum')->post('actuaciones/update-campos-finales/{id}',  [ActuacionesController::class, 'actualizarCamposFinales']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-usuario-comisionado/{id_proceso_disciplinario}',  [ActuacionesController::class, 'UsuarioComisionado']);
    Route::middleware('auth:sanctum')->get('actuaciones/existen-actuaciones-etapas/{id_proceso_disciplinario}',  [ActuacionesController::class, 'ExistenActuacionesEtapas']);
    Route::middleware('auth:sanctum')->get('actuaciones/mostrar-iniciar-proceso/{id_proceso_disciplinario}',  [ActuacionesController::class, 'MostrarIniciarProceso']);
    Route::middleware('auth:sanctum')->get('actuaciones/set-etapa/{id_actuacion}/{id_etapa}',  [ActuacionesController::class, 'actualizarEtapa']);
    Route::middleware('auth:sanctum')->get('actuaciones/set-etapa-visibilidad/{id_actuacion}/{id_etapa}',  [ActuacionesController::class, 'actualizarEstadoVisibilidad']);
    Route::middleware('auth:sanctum')->get('actuaciones/set-reporte/{id_actuacion}/{incluir_reporte}',  [ActuacionesController::class, 'actualizarIncluirReporte']);
    Route::middleware('auth:sanctum')->post('actuaciones/set-actuaciones-inactivas',  [ActuacionesController::class, 'establecerActuacionesInactivas']);
    Route::middleware('auth:sanctum')->post('actuaciones/get-actuaciones-inactivas',  [ActuacionesController::class, 'obtenerActuacionesInactivas']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-actuaciones-migradas/{id_proceso_disciplinario}',  [ActuacionesController::class, 'obtenerActuacionesMigradas']);
    Route::middleware('auth:sanctum')->get('actuaciones/get-actuaciones-etapa/{id_etapa}',  [ActuacionesController::class, 'obtenerActuacionesPorEtapa']);
    Route::middleware('auth:sanctum')->get('actuacion-proceso-disciplinario/{id_actuacion}',  [ActuacionesController::class, 'obtenerActuacionProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->get('actuaciones/actuacion-boton-agregar-actuacion-habilitado/{id_proceso_disciplinario}',  [ActuacionesController::class, 'botonAgregarActuacionHabilitado']);

    //Trazabilidad Actuaciones
    Route::middleware('auth:sanctum')->apiResource("trazabilidad-actuaciones", TrazabilidadActuacionesController::class)->names("api.v1.trazabilidad-actuaciones");
    Route::middleware('auth:sanctum')->post('trazabilidad-actuaciones/get-all-trazabilidad-actuaciones',  [TrazabilidadActuacionesController::class, 'getAllTrazabilidadActuaciones']);
    Route::middleware('auth:sanctum')->get('trazabilidad-actuaciones/get-all-trazabilidad-actuaciones-uuid/{uuid}',  [TrazabilidadActuacionesController::class, 'getAllTrazablidadesActuacionesById']);
    Route::middleware('auth:sanctum')->post('trazabilidad-actuaciones/flujo-trazabilidad-aprobar',  [TrazabilidadActuacionesController::class, 'flujoTrazabilidadAprobar']);
    Route::middleware('auth:sanctum')->post('trazabilidad-actuaciones/flujo-trazabilidad-rechazar',  [TrazabilidadActuacionesController::class, 'flujoTrazabilidadRechazar']);

    //Route WordDocuments
    Route::middleware('auth:sanctum')->apiResource("WordDoc", WordDocController::class);
    Route::middleware('auth:sanctum')->post('wordDoc/wordDocFile',  [WordDocController::class, 'wordDocFile']);
    Route::middleware('auth:sanctum')->get('wordDoc/imagen-documento/{nombre}',  [WordDocController::class, 'wordDocImages']);
    Route::middleware('auth:sanctum')->get('wordDoc/addSignature',  [WordDocController::class, 'wordDocAddSignatureTable']);
    // Route::middleware('auth:sanctum')->get('wordDoc/convertWordToPdf',  [WordDocController::class, 'convertWordToPdf']);

    //Route Transacciones
    Route::middleware('auth:sanctum')->post('transacciones/cambiar-usuario-proceso-disciplinario',  [TransaccionesController::class, 'CambiarUsuarioProcesoDisciplinario']);

    //Mas Actuaciones
    Route::middleware('auth:sanctum')->apiResource("mas_actuaciones", MasActuacionesController::class)->names("api.v1.mas_actuaciones");
    Route::middleware('auth:sanctum')->post('mas_actuaciones/plantilla/{id}',  [MasActuacionesController::class, 'getArchivoActuacion']);
    Route::middleware('auth:sanctum')->post('mas_actuaciones/manual/{id}',  [MasActuacionesController::class, 'getArchivoActuacionManual']);
    Route::middleware('auth:sanctum')->post('mas_actuaciones/parametros-plantilla/{idActuacion}/{id_proceso_disciplinario}',  [MasActuacionesController::class, 'getParametrosPlantilla']);
    Route::middleware('auth:sanctum')->post('mas_actuaciones/plantilla-diligenciada/{id}',  [MasActuacionesController::class, 'getPlantillaDiligenciada']);
    Route::middleware('auth:sanctum')->get('mas_actuaciones/actuaciones-etapa/{id}',  [MasActuacionesController::class, 'getActuacionesPorEtapa']);
    Route::middleware('auth:sanctum')->get('mas_actuaciones/getActuacionesByName/{name}/{idEtapa}',  [MasActuacionesController::class, 'getActuacionesPorNombre']);
    Route::middleware('auth:sanctum')->get('mas_actuaciones/convertWord/{idActuacion}',  [MasActuacionesController::class, 'convertWord']);
    Route::middleware('auth:sanctum')->get('mas_actuaciones/validarUsuarioPendienteFirma/{idActuacion}',  [MasActuacionesController::class, 'validarUsuarioPendienteFirma']);
    Route::middleware('auth:sanctum')->get('mas_actuaciones/getRoles/{idActuacion}',  [MasActuacionesController::class, 'obtenerRoles']);
    Route::middleware('auth:sanctum')->get('mas_actuaciones/getEtapas/{idActuacion}',  [MasActuacionesController::class, 'obtenerEtapas']);

    //Archivo actuaciones
    Route::middleware('auth:sanctum')->apiResource("archivo-actuaciones", ArchivoActuacionesController::class)->names("api.v1.archivo-actuaciones");
    Route::middleware('auth:sanctum')->get('archivo-actuaciones/get-archivo-actuaciones-by-uuid/{uuid}',  [ArchivoActuacionesController::class, 'getAllArchivosActuacionesByUuid']);
    Route::middleware('auth:sanctum')->get('archivo-actuaciones/get-documento/{uuid_actuacion}/{extension}',  [ArchivoActuacionesController::class, 'getDocumento']);
    Route::middleware('auth:sanctum')->post('archivo-actuaciones/up-documento',  [ArchivoActuacionesController::class, 'upArchivoDefinitivo']);
    Route::middleware('auth:sanctum')->post('archivo-actuaciones/update-documento/{uuid}/{uuid_actuacion}',  [ArchivoActuacionesController::class, 'updateArchivoActuaciones']);

    // Requerimiento Juzgado
    Route::middleware('auth:sanctum')->apiResource("requerimiento-juzgado", RequerimientoJuzgadoController::class)->names("api.v1.requerimiento_juzgado");
    Route::middleware('auth:sanctum')->get('requerimiento-juzgado/get-requerimiento-by-id-proceso-disciplinario/{id_proceso_disciplinario}',  [RequerimientoJuzgadoController::class, 'getRequerimientoJuzgadoByIdProcesoDisciplinario']);

    // Parametro Campos
    Route::middleware('auth:sanctum')->apiResource("parametro-campos", MasParametroCamposController::class)->names("api.v1.parametro-campos");

    // Mas Transacciones
    Route::middleware('auth:sanctum')->apiResource("TransaccionesAdmin", TransaccionesController::class)->names("api.v1.TransaccionesAdmin");
    Route::middleware('auth:sanctum')->get('mas_transacciones/get-all-transacciones-ordenadas',  [TransaccionesController::class, 'getTransaccionesOrdenado']);

    Route::middleware('auth:sanctum')->apiResource("informe-cierre", InformeCierreController::class)->names("api.v1.informe-Cierre");
    Route::middleware('auth:sanctum')->apiResource("registro-seguimiento", RegistroSeguimientoController::class)->names("api.v1.registro-seguimiento");

    // Busqueda de expedientes
    Route::middleware('auth:sanctum')->apiResource("buscador", BuscadorController::class)->names("api.v1.buscador");
    Route::middleware('auth:sanctum')->post('buscador-general',  [BuscadorController::class, 'buscadorGeneral']);


    Route::middleware('auth:sanctum')->get('informe-cierre/archivar/{id_proceso_disciplinario}',  [InformeCierreController::class, 'storeArchivar']);


    // Tipo de conducta
    Route::middleware('auth:sanctum')->apiResource("tipo-conducta", TipoConductaProcesoDisciplinarioController::class)->names("api.v1.tipo-conducta-proceso-disciplinario");
    Route::middleware('auth:sanctum')->post('tipo-conducta/get-conducta-by-id-proceso-disciplinario/{id}',  [TipoConductaProcesoDisciplinarioController::class, 'showTipoConductaByProcesoDisciplinario']);

    //Route Logs Consultas
    Route::middleware('auth:sanctum')->apiResource("log-consultas", LogConsultasController::class)->names("api.v1.log-consultas");


    // configuracion de evaluacion fase
    Route::middleware('auth:sanctum')->apiResource("configurar-fases-evaluacion", EvaluacionFaseController::class)->names("api.v1.evaluacion-fase");
    Route::middleware('auth:sanctum')->post("guardar-fases-evaluacion-lista", [EvaluacionFaseController::class, 'storeListaEvaluacionFase']);
    Route::middleware('auth:sanctum')->get("eliminar-fases-evaluacion-lista/{idTipoExpediente}/{idSubTipoExpediente}/{idEvaluacion}", [EvaluacionFaseController::class, 'eliminarFases']);
    Route::middleware('auth:sanctum')->get('administracion/evaluacion/lista-configuracion-fases',  [EvaluacionFaseController::class, 'showListaConfiguracion']);
    Route::middleware('auth:sanctum')->get('administracion/evaluacion/lista-expedientes',  [EvaluacionFaseController::class, 'showListaTipoExpedienteEvaluacion']);
    Route::middleware('auth:sanctum')->get('administracion/evaluacion/lista-expedientes-by-id-tipo-expediente/{id_tipo_expediente}/{id_subtipo_expediente}/{id_tipo_evaluacion}',  [EvaluacionFaseController::class, 'getFasesEtapaEvaluacionByIdExpedienteAndIdEvaluacion']);
    Route::middleware('auth:sanctum')->post('administracion/evaluacion/update-fases',  [EvaluacionFaseController::class, 'updateFasesEvaluacion']);

    Route::middleware('auth:sanctum')->get('administracion/nombre-evaluacion/{id_evaluacion}',  [ResultadoEvaluacionController::class, 'getNombreEvaluacion']);
    Route::middleware('auth:sanctum')->get('administracion/nombre-expediente/{id_expediente}',  [TipoExpedienteController::class, 'getNombreTipoExpediente']);
    Route::middleware('auth:sanctum')->get('administracion/nombre-sub-tipo-expediente/{id_expediente}/{id_sub_expediente}',  [TipoExpedienteController::class, 'getNombreSubTipoExpediente']);

    // Mas caraturas
    Route::middleware('auth:sanctum')->apiResource("caratulas", MasCaratulasController::class)->names("api.v1.caratulas");
    Route::middleware('auth:sanctum')->post('caratulas/parametros-plantilla/{caratulaId}',  [MasCaratulasController::class, 'getParametrosPlantilla']);
    Route::middleware('auth:sanctum')->post('caratulas/plantilla-diligenciada/{id}',  [MasCaratulasController::class, 'getPlantillaDiligenciada']);
    Route::middleware('auth:sanctum')->post('caratulas/plantilla/{id}',  [MasCaratulasController::class, 'getArchivoCaratula']);

    // Generar caratula ramas del proceso
    Route::middleware('auth:sanctum')->get('caratulas/caratula-ramas-proceso/{id}',  [MasCaratulasController::class, 'getCaratulaRamasProceso']);

    Route::middleware('auth:sanctum')->get('activar-reclasificacion-tipo-conducta/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'activarClasificacionTiposConducta']);
    Route::middleware('auth:sanctum')->get('activar-reclasificacion-tipo-expediente/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'activarClasificacionTiposExpediente']);
    Route::middleware('auth:sanctum')->get('activar-soporte-radicado/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'activarSoporteRadicado']);
    Route::middleware('auth:sanctum')->get('validar-tipo-expediente-queja-interna/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'validarTipoExpedienteQuejaInterna']);
    Route::middleware('auth:sanctum')->get('activar-registro-fase-evaluacion/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'activarRegistroFaseEvaluacionEtapaEvaluacion']);
    Route::middleware('auth:sanctum')->get('activar-registro-fase-validar-clasificacion/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'activarRegistroValidarClasificacionEtapaEvaluacion']);
    Route::middleware('auth:sanctum')->get('validar-si-es-jefe',  [ActivarFuncionalidadesController::class, 'validarSiEsJefe']);
    Route::middleware('auth:sanctum')->get('nombre-proceso/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'getTituloProceso']);
    Route::middleware('auth:sanctum')->get('validar-crear-clasificacion/{id_proceso_disciplinario}',  [ActivarFuncionalidadesController::class, 'ValidarAgregrarClasificacion']);

    Route::middleware('auth:sanctum')->get('validar-permiso-crear-actuaciones',  [DependenciaOrigenController::class, 'puedeCrearActuaciones']);

    // Tipo Expediente Mensajes
    Route::middleware('auth:sanctum')->apiResource("mas_tipo_expediente_mensajes", TipoExpedienteMensajesController::class)->names("api.v1.mas_tipo_expediente_mensajes");

    // Direccion
    Route::middleware('auth:sanctum')->apiResource("direccion_nomenclatura", DireccionNomenclaturaController::class)->names("api.v1.mas-direccion-nomenclatura");
    Route::middleware('auth:sanctum')->apiResource("direccion_letras", DireccionLetrasController::class)->names("api.v1.mas-direccion-letras");
    Route::middleware('auth:sanctum')->apiResource("direccion_orientacion", DireccionOrientacionController::class)->names("api.v1.mas-direccion-orientacion");
    Route::middleware('auth:sanctum')->apiResource("direccion_complemento", DireccionComplementoController::class)->names("api.v1.mas-direccion-complemento");
    Route::middleware('auth:sanctum')->apiResource("direccion_bis", DireccionBisController::class)->names("api.v1.mas-direccion-bis");

    Route::middleware('auth:sanctum')->get('mas_tipo_expediente_mensajes/{id_tipo_expediente}/{id_sub_tipo_expediente}',  [TipoExpedienteMensajesController::class, 'obtenerInformacionTipoExpediente']);

    // Tipo Unidad
    Route::middleware('auth:sanctum')->apiResource("mas_tipo_unidad", TipoUnidadController::class)->names("api.v1.mas_tipo_unidad");

    // Parametro Campos Caratula
    Route::middleware('auth:sanctum')->apiResource("parametro_campos_caratula", ParametroCamposCaratulasController::class)->names("api.v1.parametro_campos_caratula");

    //Semaforizacion
    Route::middleware('auth:sanctum')->apiResource("semaforo", SemaforoController::class)->names("api.v1.semaforo");
    Route::middleware('auth:sanctum')->get('get-semaforo-actuacion/{id_mas_actuacion}/{id_etapa}',  [SemaforoController::class, 'semaforoPorMasActuacion']);
    Route::middleware('auth:sanctum')->get('semaforo/get-semaforo-por-etapa/{id_etapa}/{estado}',  [SemaforoController::class, 'semaforoPorEtapa']);
    Route::middleware('auth:sanctum')->get('semaforo/get-semaforo-proceso-disciplinario/{id_evento}/{id_proceso_disciplinario}',  [SemaforoController::class, 'obtenerSemaforosProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->get('get-semaforo-evento/{idEventoInicio}',  [SemaforoController::class, 'semaforoPorEventoInicio']);
    Route::middleware('auth:sanctum')->apiResource("mas_evento_inicio", MasEventoInicioController::class)->names("api.v1.mas_evento_inicio");
    Route::middleware('auth:sanctum')->apiResource("condicion", CondicionController::class)->names("api.v1.condicion");
    Route::middleware('auth:sanctum')->get('condiciones-por-semaforo/{id_semaforo}',  [CondicionController::class, 'condicionesPorSemaforo']);
    Route::middleware('auth:sanctum')->apiResource("auto_finaliza", AutoFinalizaController::class)->names("api.v1.auto_finaliza");
    Route::middleware('auth:sanctum')->get('existe-semaforo-con-fecha/{id_semaforo}',  [ProcesoDisciplinarioPorSemaforoController::class, 'existeSemaforoConFecha']);
    Route::middleware('auth:sanctum')->get('actualizar-fecha/{id_semaforo}/{id_proceso_disciplinario}/{fecha}',  [ProcesoDisciplinarioPorSemaforoController::class, 'cambiarFechaInicio']);
    Route::middleware('auth:sanctum')->get('autosfinalizan/{id_semaforo}',  [AutoFinalizaController::class, 'AutoFinalizanPorSemaforo']);
    Route::middleware('auth:sanctum')->get('get-autofinaliza-actuacion/{id_semaforo}/{id_etapa}',  [AutoFinalizaController::class, 'AutoFinalizanPorMasActuacion']);
    Route::middleware('auth:sanctum')->get('getSemaforosPorProceso/{uuid}',  [ProcesoDisciplinarioPorSemaforoController::class, 'getSemaforosPorProceso']);
    Route::middleware('auth:sanctum')->post('set-finaliza-semaforo',  [ProcesoDisciplinarioPorSemaforoController::class, 'FinalizarSemaforo']);
    Route::middleware('auth:sanctum')->get('set-finaliza-semaforo-actuacion/{id_semaforo}/{id_proceso_disciplinario}/{id_actuacion}',  [ProcesoDisciplinarioPorSemaforoController::class, 'FinalizarSemaforoActuacion']);
    Route::middleware('auth:sanctum')->get('getDiasTranscurridos/{uuid}',  [ProcesoDisciplinarioPorSemaforoController::class, 'getDiasTranscurridos']);
    Route::middleware('auth:sanctum')->apiResource("pdxsemaforo", ProcesoDisciplinarioPorSemaforoController::class)->names("api.v1.pdxsemaforo");


    //mas_tipo_firma
    Route::middleware('auth:sanctum')->apiResource("mas-tipo-firma", MasTipoFirmaController::class)->names("api.v1.mas_tipo_firma");

    //documento cierre
    Route::middleware('auth:sanctum')->apiResource("mas-preguntas-documento-cierre", PreguntasDocumentoCierreController::class)->names("api.v1.mas-preguntas-documento-cierre");
    Route::middleware('auth:sanctum')->get("preguntas-doc-cierre", [PreguntasDocumentoCierreController::class, 'preguntas']);
    Route::middleware('auth:sanctum')->post("update-preguntas-doc-cierre", [PreguntasDocumentoCierreController::class, 'updatePreguntas']);
    Route::middleware('auth:sanctum')->get("estado-preguntas", [PreguntasDocumentoCierreController::class, 'estadoPreguntas']);

    Route::middleware('auth:sanctum')->get('condiciones-por-semaforo/{id_semaforo}',  [CondicionController::class, 'condicionesPorSemaforo']);
    Route::middleware('auth:sanctum')->apiResource("auto_finaliza", AutoFinalizaController::class)->names("api.v1.auto_finaliza");
    Route::middleware('auth:sanctum')->get('autos-finalizan-por-semaforo/{id_semaforo}',  [AutoFinalizaController::class, 'AutoFinalizanPorSemaforo']);


    //Secuencias
    Route::middleware('auth:sanctum')->get('iniciar-secuencias',  [SecuenciaController::class, 'iniciarSecuencia']);

    //Migracion
    Route::middleware('auth:sanctum')->get('iniciar-migracion-from-disciplinarios',  [MigracionController::class, 'iniciarMigracionFromDisciplinarios']);
    Route::middleware('auth:sanctum')->get('actualizar-fecha-migracion',  [MigracionController::class, 'actualizarFechaFromDisciplinarios']);

    Route::middleware('auth:sanctum')->post('buscador-migracion',  [MigracionController::class, 'buscadorGeneral']);
    Route::middleware('auth:sanctum')->get('get-fases-orden',  [FaseController::class, 'getFasesOrden']);
    Route::middleware('auth:sanctum')->get('migracion-proceso-disciplinario/{radicado}/{vigencia}',  [MigracionController::class, 'getInfoProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->get('migracion-lista-antecedentes/{radicado}/{vigencia}',  [MigracionController::class, 'getInfoListaAntecedentes']);
    Route::middleware('auth:sanctum')->get('migracion-antecedente/{radicado}/{vigencia}/{item}',  [MigracionController::class, 'getInfoAntecedente']);
    Route::middleware('auth:sanctum')->get('migracion-lista-interesados/{radicado}/{vigencia}',  [MigracionController::class, 'getInfoListaInteresados']);
    Route::middleware('auth:sanctum')->get('migracion-interesado/{radicado}/{vigencia}/{item}',  [MigracionController::class, 'getInfoInteresado']);
    Route::middleware('auth:sanctum')->get('migracion-lista-entidades/{radicado}/{vigencia}',  [MigracionController::class, 'getInfoListaEntidades']);
    Route::middleware('auth:sanctum')->get('migracion-entidad/{radicado}/{vigencia}/{item}',  [MigracionController::class, 'getInfoEntidad']);
    Route::middleware('auth:sanctum')->get('migracion-lista-actuaciones/{radicado}/{vigencia}',  [MigracionController::class, 'getInfoListaActuaciones']);
    Route::middleware('auth:sanctum')->get('migracion-actuacion/{radicado}/{vigencia}/{item}',  [MigracionController::class, 'getInfoActuacion']);
    Route::middleware('auth:sanctum')->get('migracion-definitva/{radicado}/{vigencia}',  [MigracionController::class, 'MigrarProcesoDefinitivo']);
    Route::middleware('auth:sanctum')->get('migracion-version/{radicado}/{vigencia}',  [MigracionController::class, 'getInfoVersion']);
    Route::middleware('auth:sanctum')->get('fases-migracion/{radicado}/{vigencia}',  [MigracionController::class, 'getInfoEstadoFasesMigradas']);

    Route::middleware('auth:sanctum')->apiResource("migrar-proceso-disciplinario", TempProcesoDisciplinarioController::class)->names("api.v1.temp-proceso-disciplinario");
    Route::middleware('auth:sanctum')->get('migrar-proceso-disciplinario/get-proceso/{radicado}/{vigencia}',  [TempProcesoDisciplinarioController::class, 'getTempProceso']);

    Route::middleware('auth:sanctum')->apiResource("migrar-antecedentes", TempAntecedentesController::class)->names("api.v1.temp-antecedentes");
    Route::middleware('auth:sanctum')->get('migrar-antecedentes/get-antecedentes/{radicado}/{vigencia}/{item}',  [TempAntecedentesController::class, 'getTempAntecedentes']);

    Route::middleware('auth:sanctum')->apiResource("migrar-interesados", TempInteresadosController::class)->names("api.v1.temp-interesados");
    Route::middleware('auth:sanctum')->get('migrar-interesados/get-interesados/{radicado}/{vigencia}/{item}',  [TempInteresadosController::class, 'getTempInteresados']);

    Route::middleware('auth:sanctum')->apiResource("migrar-entidades", TempEntidadesController::class)->names("api.v1.temp-entidades");
    Route::middleware('auth:sanctum')->get('migrar-entidades/get-entidades/{radicado}/{vigencia}/{item}',  [TempEntidadesController::class, 'getTempEntidades']);

    Route::middleware('auth:sanctum')->apiResource("migrar-actuaciones", TempActuacionesController::class)->names("api.v1.temp-actuaciones");
    Route::middleware('auth:sanctum')->get('migrar-actuaciones/get-actuaciones/{radicado}/{vigencia}/{item}',  [TempActuacionesController::class, 'getTempActuaciones']);

    Route::middleware('auth:sanctum')->get('migrar-actuaciones/get-actuaciones/{radicado}/{vigencia}/{item}',  [TempActuacionesController::class, 'getTempActuaciones']);

    //Route::middleware('auth:sanctum')->apiResource("migrar-clasificacion-radicado", TempClasificacionRadicadoController::class)->names("api.v1.temp-clasificacion-radicado");

    Route::middleware('auth:sanctum')->apiResource("portal-log", PortalLogController::class)->names("api.v1.portal-log");

    Route::middleware('auth:sanctum')->apiResource("portal-notificaciones", PortalNotificacionesController::class)->names("api.v1.portal-notificaciones");
    Route::middleware('auth:sanctum')->get('portal-notificaciones/get-documento/{uuidNotificaciones}',  [PortalNotificacionesController::class, 'getDocumentoNotificaciones']);

    Route::middleware('auth:sanctum')->apiResource("portal-tipo-interesado", PortalConfiguracionTipoInteresadoController::class)->names("api.v1.portal-tipo-interesado");

    // mas grupo trabajo secretaria comun
    Route::middleware('auth:sanctum')->apiResource("mas_grupo_trabajo", GrupoTrabajoSecretariaComunController::class)->names("api.v1.mas_grupo_trabajo_secretaria_comun");
    Route::middleware('auth:sanctum')->get('mas_grupo_trabajo/repartoAleatorio/{id_grupoTrabajo}/{id_proceso_disciplinario}',  [GrupoTrabajoSecretariaComunController::class, 'repartoAleatorio']);

    // Semaforos desde actuaciones
    Route::middleware('auth:sanctum')->apiResource("actuacionxsemaforo", ActuacionPorSemaforoController::class)->names("api.v1.actuacionxsemaforo");
    Route::middleware('auth:sanctum')->get('getDiasTranscurridos/actuacion/{uuid}',  [ActuacionPorSemaforoController::class, 'getDiasTranscurridos']);
    Route::middleware('auth:sanctum')->get('set-finaliza-asemaforo/{id_semaforo}/{id_acutacion}',  [ProcesoDisciplinarioPorSemaforoController::class, 'FinalizarSemaforo']);

    // Cargos
    Route::middleware('auth:sanctum')->apiResource("cargos", CargosController::class)->names("api.v1.cargos");
    Route::middleware('auth:sanctum')->get('getCargos/{estado}',  [CargosController::class, 'getCargos']);

    Route::middleware('auth:sanctum')->get('contador_desglose/{sinproc}',  [ProcesoDiciplinarioController::class, 'contadorDesgloses']);
    Route::middleware('auth:sanctum')->get('getinfohijos/{sinproc}',  [ProcesoDiciplinarioController::class, 'getInfoHijos']);
    Route::middleware('auth:sanctum')->get('getinfohijo/{id_proceso_disciplinario}',  [ProcesoDiciplinarioController::class, 'getInfoHijo']);

    Route::middleware('auth:sanctum')->get('masActuaciones/convertToPdf',  [MasActuacionesController::class, 'convertToPdf']);

    // Consecutivo desglose
    Route::middleware('auth:sanctum')->get('proceso_radicado/{radicado}',  [ProcesoDiciplinarioController::class, 'getProcesoPorRadicado']);

    // Consecutivo actuaciones
    Route::middleware('auth:sanctum')->apiResource("mas-consecutivo-actuaciones", MasConsecutivoActuacionesController::class)->names("api.v1.mas-consecutivo-actuaciones");

    // Mas Estado Visibilidad
    Route::middleware('auth:sanctum')->apiResource("mas-estado-visibilidad", MasEstadoVisibilidadController::class)->names("api.v1.mas-estado-visabilidad");
    Route::middleware('auth:sanctum')->get('mas-estado-visibilidad/estado/{estado}',  [MasEstadoVisibilidadController::class, 'obtenerEstados']);

    // CambioEtapa
    Route::middleware('auth:sanctum')->get('cambio-etapa/obtener-proceso/{radicado}/{vigencia}',  [CambioEtapaController::class, 'obtenerProcesoDisciplinario']);
    Route::middleware('auth:sanctum')->apiResource("cambio-etapa", CambioEtapaController::class)->names("api.v1.cambiar-etapa");


    Route::middleware('auth:sanctum')->post("portal-web/obtener-documento", [PortalController::class, 'obtenerDocumento']);
});
