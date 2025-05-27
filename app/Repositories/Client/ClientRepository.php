<?php

namespace App\Repositories\Client;

use App\Repositories\AbstractRepository;
use App\Repositories\Client\ClientRepositoryInterface;
use App\Models\Client;

class ClientRepository extends AbstractRepository implements ClientRepositoryInterface
{
    function __construct(Client $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $client = $this->model->create($request);
        return $client;
    }

    public function update(int $id, array $request)
    {
        $client = $this->findById($id);
        $client->update($request);
    }

    public function delete(int $id)
    {
        $client = $this->findById($id);
        $client->delete();
    }
}
