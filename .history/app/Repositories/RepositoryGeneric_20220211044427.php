<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
        if ($user != null) {
            $data["created_user"] = $user->email;
            $data["updated_user"] = $user->email;
        }
        return $this->model->create($data);
    }

    public function update(array $data, $id)
    {
        $model = $this->find($id);
        $user = auth()->user();
        if ($user != null) {
            $data["updated_user"] = $user->email;
        }
        $model->update($data);
        return $model;
    }

    public function delete($id)
    {
        $user = auth()->user();
        if ($user != null) {
            $data["deleted_user"] = $user()->email;
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
