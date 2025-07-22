<?php

namespace App\Repositories;

use App\Repositories\RepositoryInterface;

abstract class EloquentRepository implements RepositoryInterface
{
    protected $_model;

    public function __construct()
    {
        $this->setModel();
    }

    abstract public function getModel();

    public function setModel()
    {
        $this->_model = app()->make(
            $this->getModel()
        );
    }

    public function getAll($with = [])
    {
        return $this->_model->with($with)->get();
    }

    public function getByIds(array $ids, array $with = [])
    {
        return $this->_model->with($with)->whereIn('id', $ids)->get();
    }

    public function find($id)
    {
        $result = $this->_model->find($id);

        return $result;
    }

    public function create(array $attributes)
    {
        return $this->_model->create($attributes);
    }

    public function update($id, array $attributes)
    {
        $result = $this->find($id);
        if ($result) {
            $result->update($attributes);
            return $result;
        }

        return false;
    }

    public function delete($id)
    {
        $result = $this->find($id);
        if ($result) {
            $result->delete();

            return true;
        }

        return false;
    }

    public function deleteByIds(array $ids)
    {
        return $this->_model->whereIn('id', $ids)->delete();
    }

    public function findWithTrashed($id)
    {
        return $this->_model::withTrashed()->find($id);
    }

}
