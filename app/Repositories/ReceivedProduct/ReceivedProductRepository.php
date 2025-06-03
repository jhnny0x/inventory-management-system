<?php

namespace App\Repositories\ReceivedProduct;

use App\Repositories\AbstractRepository;
use App\Models\ReceivedProduct;

class ReceivedProductRepository extends AbstractRepository implements ReceivedProductRepositoryInterface
{
    protected $model;

    function __construct(ReceivedProduct $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $received_product = $this->model->create($request);
        return $received_product;
    }

    public function update(int $id, array $request)
    {
        $received_product = $this->findById($id);
        $received_product->update($request);
    }

    public function delete(int $id)
    {
        $received_product = $this->findById($id);
        $received_product->delete();
    }
}
