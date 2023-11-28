<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        $this->call([
            RolesSeeder::class,
            MasModuloSeeder::class,
            MasFuncionalidadSeeder::class,
            MasDepartamentoSeeder::class,
            MasParametroSeeder::class,
            MasCiudadSeeder::class,
            MasTipoProcesoSeeder::class,
            MasOrigenRadicadoSeeder::class,
            MasEtapaSeeder::class,
            MasDependenciaOrigenSeeder::class,
            MasTipoExpedienteSeeder::class,
            MasTipoDerechoPeticionSeeder::class,
            MasTerminoRespuestaSeeder::class,
            MasTipoQuejaSeeder::class,
            MasTipoDocumentoSeeder::class,
            MasTipoEntidadSeeder::class,
            MasTipoSujetoProcesalSeeder::class,
            MasLocalidadSeeder::class,
            MasSexoSeeder::class,
            MasGeneroSeeder::class,
            MasOrientacionSexualSeeder::class,
            MasDiasNoLaboralesSeeder::class,
            MasVigenciaSeeder::class,
            MasTipoInteresadoSeeder::class,
            MasTipoRespuestaSeeder::class,
            MasFaseSeeder::class,
            MasTipoEstadoEtapaSeeder::class,
            MasTipoLogSeeder::class,
            MasFormatoSeeder::class,
            //ClasificacionRadicadoSeeder::class,
            MasTipoConductaSeeder::class,
            MasDependenciaAccesoSeeder::class,
            MasDependenciaConfiguracionSeeder::class,
            MasResultadoEvaluacionSeeder::class,
            MasTipoTransaccionSeeder::class,
            EvaluacionExpedienteSeeder::class,
            EvaluacionResultadoSeeder::class,
            MasBusquedaExpedienteSeeder::class,
            MasEstadoActuacionesSeeder::class,
            MasTipoArchivoActuacionesSedeer::class,
            MasActuacionesSeeder::class,
            // MasParametroCamposSeeder::class,
            MasDireccionNomenclaturaSeeder::class,
            MasDireccionLetrasSeeder::class,
            MasDireccionOrientacionSeeder::class,
            MasDireccionComplementoSeeder::class,
            MasDireccionBisSeeder::class,
            ParametroCamposCaratulaSeeder::class,
            MasCaratulaSeeder::class,
            MasParametroCamposSeeder::class,
            MasTipoFirmaSeeder::class,
            MasTipoFuncionario::class,
            CierreEtapaConfiguracionSeeder::class,
            MasEstadoProcesoDisciplinarioSeeder::class,
            MasEventoInicioSeeder::class,
            MasPreguntasDocumentoCierreSeeder::class,
            MasTipoCierreEtapaSeeder::class,
            MasTipoExpedienteMensajesSeeder::class,
            MasTipoUnidadSeeder::class,
            EvaluacionFaseSeeder::class,
            MasGrupoTrabajoSecretariaComunSeeder::class,
            SemaforoSeeder::class,
            CondicionSeeder::class,
            AutoFinalizaSeeder::class,
            MasConsecutivoDesgloseSeeder::class,
            MasModuloGrupoSeeder::class,
        ]);
    }
}
