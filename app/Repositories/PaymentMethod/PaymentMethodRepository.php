<?php

namespace App\Repositories\PaymentMethod;

use App\Repositories\AbstractRepository;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface;
use App\Models\PaymentMethod;

class PaymentMethodRepository extends AbstractRepository implements PaymentMethodRepositoryInterface
{
    function __construct(PaymentMethod $model)
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
