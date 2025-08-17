<?php

namespace App\Repositories\Provider;

use App\Repositories\AbstractRepository;
use App\Models\Provider;

class ProviderRepository extends AbstractRepository implements ProviderRepositoryInterface
{
    protected $model;

    function __construct(Provider $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $provider = $this->model->create($request);
        return $provider;
    }

    public function update(int $id, array $request)
    {
        $provider = $this->findById($id);
        $provider->update($request);
    }

    public function delete(int $id)
    {
        $provider = $this->findById($id);
        $provider->delete();
    }
}
