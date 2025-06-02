<?php

namespace App\Repositories\Product;

use App\Repositories\AbstractRepository;
use App\Repositories\Product\ProductRepositoryInterface;
use App\Models\ProductCategory;

class ProductRepository extends AbstractRepository implements ProductRepositoryInterface
{
    protected $model;

    function __construct(ProductCategory $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $product_category = $this->model->create($request);
        return $product_category;
    }

    public function update(int $id, array $request)
    {
        $product_category = $this->findById($id);
        $product_category->update($request);
    }

    public function delete(int $id)
    {
        $product_category = $this->findById($id);
        $product_category->delete();
    }
}
