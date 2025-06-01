<?php

namespace App\Repositories\SoldProduct;

use App\Repositories\AbstractRepository;
use App\Repositories\SoldProduct\SoldProductRepositoryInterface;
use App\Models\SoldProduct;
use DB;

class SoldProductRepository extends AbstractRepository implements SoldProductRepositoryInterface
{
    function __construct(SoldProduct $model)
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

    public function getMonthlyProductQuantities()
    {
        $products = $this->model->select([
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(qty) as total_quantities')
        ])
            ->thisYear()
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total_quantities')
            ->toArray();

        return $products;
    }
}
