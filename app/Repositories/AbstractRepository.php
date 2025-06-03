<?php

namespace App\Repositories;

abstract class AbstractRepository
{
    public function findById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function find($id)
    {
        return $this->model->find($id);
    }

    public function select($columns = ['*'])
    {
        return $this->model->select($columns);
    }

    public function all()
    {
        return $this->model->all();
    }

    public function where($column, $operator = '=', $value = null)
    {
        return $this->model->where($column, $operator, $value);
    }

    public function whereCallback($callback)
    {
        return $this->model->where($callback);
    }

    public function whereIn($column, $value = [])
    {
        return $this->model->whereIn($column, $value);
    }

    public function whereNull($column)
    {
        return $this->model->whereNull($column);
    }

    public function whereBetween($column, $start, $end)
    {
        return $this->model->whereBetween($column, [$start, $end]);
    }

    public function pluck($name = 'name', $id = '')
    {
        return $id ? $this->model->pluck($name, $id) : $this->model->pluck($name);
    }

    public function with($eagers = [])
    {
        return $this->model->with($eagers);
    }

    public function paginate($limit = 25)
    {
        return $this->model->paginate($limit);
    }

    public function latest()
    {
        return $this->model->latest();
    }

    public function getAjaxResponse($type, $message)
    {
        return response(['message' => $message, 'type' => $type])->header('Content-Type', 'application/json');
    }
}
