<?php

namespace App\Repositories;

use Error;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Schema;

class RepositoryGeneric
{
    protected $model;

    /**
     * modelRepository constructor.
     *
     * @param Model $model
     */
    public function setModel(Model $model)
    {
        $this->model = $model;
    }

    public function getModel()
    {
        return $this->model;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function paginate($limit)
    {
        return $this->model->paginate($limit);
    }

    public function create(array $data)
    {
        $user = auth()->user();
        
        if ($user != null && Schema::hasColumn($this->model->getTable(), 'created_user') && Schema::hasColumn($this->model->getTable(), 'updated_user')) {
            $data["created_user"] = $user->name;
            $data["updated_user"] = $user->name;
        }
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $model = $this->find($id);
        $user = auth()->user();
        if ($user != null && Schema::hasColumn($this->model->getTable(), 'updated_user')) {
            $data["updated_user"] = $user->name;
        }
        $model->update($data);
        return $model;
    }

    public function delete($id)
    {
        $user = auth()->user();
        if ($user != null && Schema::hasColumn($this->model->getTable(), 'deleted_user')) {
            $data["deleted_user"] = $user->name;
        }
        return $this->model->destroy($id);
    }

    public function find($id)
    {
        if (null == $model = $this->model->find($id)) {
            throw new ModelNotFoundException("Model not found");
        }

        return $model;
    }

    public function customQuery($m)
    {
        return $m($this->model);
    }
}
