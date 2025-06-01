<?php

namespace App\Repositories\Sale;

use App\Repositories\AbstractRepository;
use App\Repositories\Sale\SaleRepositoryInterface;
use App\Models\Sale;
use DB;

class SaleRepository extends AbstractRepository implements SaleRepositoryInterface
{
    function __construct(Sale $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $sale = $this->model->create($request);
        return $sale;
    }

    public function update(int $id, array $request)
    {
        $sale = $this->findById($id);
        $sale->update($request);
    }

    public function delete(int $id)
    {
        $sale = $this->findById($id);
        $sale->delete();
    }

    public function getMonthlySalesCount()
    {
        $number_of_sales = [];
        $number_of_clients = [];
        $sales = $this->model->select([
            DB::raw("COUNT(id) as number_of_sales"),
            DB::raw("MONTH(created_at) as month"),
            DB::raw('COUNT(DISTINCT client_id) as number_of_clients'),
        ])
            ->thisYear()
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        foreach(MONTHS_IN_A_YEAR as $month) {
            $number_of_sales[] = $sales[$month]['number_of_sales'] ?? 0;
            $number_of_clients[] = $sales[$month]['number_of_clients'] ?? 0;
        }

        return [
            'number_of_sales' => $number_of_sales,
            'number_of_clients' => $number_of_clients
        ];
    }
}
