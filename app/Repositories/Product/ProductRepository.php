<?php

namespace App\Repositories\Product;

use App\Repositories\AbstractRepository;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Models\Product;

class ProductRepository extends AbstractRepository implements ProductRepositoryInterface
{
    protected $model;

    function __construct(Product $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $product = $this->model->create($request);
        return $product;
    }

    public function update(int $id, array $request)
    {
        $product = $this->findById($id);
        $product->update($request);
    }

    public function delete(int $id)
    {
        $product = $this->findById($id);
        $product->delete();
    }
}
