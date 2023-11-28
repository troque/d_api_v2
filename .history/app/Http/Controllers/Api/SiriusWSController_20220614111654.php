<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RadicacionFormRequest;
use App\Http\Requests\SearchRadicadoFormRequest;
use App\Http\Requests\UploadDocumentFormRequest;
use App\Http\Resources\Sirius\RadicacionResource;
use App\Http\Resources\Sirius\SearchRadicadoResource;
use App\Http\Resources\Sirius\UploadDocumentResource;
use App\Services\SiriusWS;
use Illuminate\Http\Request;

class SiriusWSController extends Controller
{
    private $sirius;

    public function __construct(SiriusWS $sirius)
    {
        $this->sirius = $sirius;
    }

    public function login()
    {
        return $this->sirius->login();
    }

    public function radicacion(RadicacionFormRequest $request)
    {
        $this->sirius->login();
        return RadicacionResource::make($this->sirius->radicacion($request->validated()));
    }

    public function uploadDocument(UploadDocumentFormRequest $request)
    {
        $this->sirius->login();
        //return UploadDocumentResource::make($this->sirius->updateDocument($request->validated()));
    }

    public function searchRadicado(SearchRadicadoFormRequest $request)
    {
        $this->sirius->login();
        return SearchRadicadoResource::make($this->sirius->searchRadicado($request->validated()));
    }
}
