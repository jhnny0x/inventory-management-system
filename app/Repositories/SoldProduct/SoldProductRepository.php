<?php

namespace App\Repositories\SoldProduct;

use App\Repositories\AbstractRepository;
use App\Repositories\SoldProduct\SoldProductRepositoryInterface;
use App\Models\SoldProduct;
use DB;

class SoldProductRepository extends AbstractRepository implements SoldProductRepositoryInterface
{
    protected $model;

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
            ->get()
            ->keyBy('month');

        $monthly_product_quantities = [];
        foreach (MONTHS_IN_A_YEAR as $month)
            $monthly_product_quantities[] = $products[$month]['total_quantities'] ?? 0;
        return $monthly_product_quantities;
    }

    public function getSoldProducts()
    {
        $sold_products = $this->model->select([
            'product_id',
            DB::raw('SUM(qty) as total_quantities'),
            DB::raw('SUM(total_amount) as incomes'),
            DB::raw('AVG(price) as avg_price')
        ])
            ->thisYear();

        return function (int $limit = 0) use ($sold_products) {
            if ($limit)
                $sold_products = $sold_products->limit($limit);
            return function (string $order_by, string $order_dir = 'asc') use ($sold_products) {
                return $sold_products->groupBy('product_id')
                    ->orderBy($order_by, $order_dir)
                    ->get();
            };
        };
    }
}
