<?php

namespace App\Repositories\Sale;

use App\Repositories\AbstractRepository;
use App\Repositories\Sale\SaleRepositoryInterface;
use App\Models\Sale;
use Carbon\Carbon;
use DB;

class SaleRepository extends AbstractRepository implements SaleRepositoryInterface
{
    protected $model;

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

    public function finalize(int $id): array
    {
        $sale = $this->findById($id);
        $sale->total_amount = $sale->products->sum('total_amount');

        foreach ($sale->products as $sold_product) {
            $product_name = $sold_product->product->name;
            $product_stock = $sold_product->product->stock;
            if ($sold_product->qty > $product_stock) {
                return [ $product_name, $product_stock ];
            }
        }

        foreach ($sale->products as $sold_product) {
            $sold_product->product->stock -= $sold_product->qty;
            $sold_product->product->save();
        }

        $sale->finalized_at = Carbon::now()->toDateTimeString();
        $sale->client->balance -= $sale->total_amount;
        $sale->save();
        $sale->client->save();

        return [];
    }

    public function getSalePeriods()
    {
        $sales = $this->model->all();
        $callback = function (array $date_range) {
            [ $start_date, $end_date ] = $date_range;
            $start_date = Carbon::parse($start_date);
            $end_date = Carbon::parse($end_date);

            return function ($sale) use ($start_date, $end_date) {
                $created_at = Carbon::parse($sale->created_at);
                return $created_at->gte($start_date) && $created_at->lte($end_date);
            };
        };

        return [
            'Day' => $sales->filter($callback(get_date_range('today'))),
            'Yesterday' => $sales->filter($callback(get_date_range('yesterday'))),
            'Week' => $sales->filter($callback(get_date_range('week'))),
            'Monthly' => $sales->filter($callback(get_date_range('month'))),
            'Trimester' => $sales->filter($callback(get_date_range('trimester'))),
            'Year' => $sales->filter($callback(get_date_range('year'))),
        ];
    }
}
