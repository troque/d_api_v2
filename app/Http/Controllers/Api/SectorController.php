<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntidadFormRequest;
use App\Http\Resources\Entidad\EntidadCollection;
use App\Http\Resources\Entidad\EntidadResource;
use App\Http\Resources\Entidad\EntidadListResource;
use App\Http\Resources\Sector\SectorCollection;
use App\Models\EntidadModel;
use App\Models\SectorModel;
use App\Repositories\RepositoryGeneric;
use Illuminate\Http\Request;

class SectorController extends Controller
{
    private $repository;
    public function __construct(RepositoryGeneric $repository)
    {
        $this->repository = $repository;
        $this->repository->setModel(new SectorModel());
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Re
     */
    public function index(Request $request)
    {
        $query = SectorModel::query();
        $query = $query->select(
            'sector.idsector',
            'sector.nombre'
        )->orderBy('sector.nombre', 'asc')->get();


        return SectorCollection::make($query);
    }
}
