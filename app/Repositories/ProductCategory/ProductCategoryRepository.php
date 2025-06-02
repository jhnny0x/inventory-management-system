<?php

namespace App\Repositories\ProductCategory;

use App\Repositories\AbstractRepository;
use App\Repositories\ProductCategory\ProductCategoryRepositoryInterface;
use App\Models\ProductCategory;
use DB;

class ProductCategoryRepository extends AbstractRepository implements ProductCategoryRepositoryInterface
{
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
