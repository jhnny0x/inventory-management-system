<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Repositories\SoldProduct\SoldProductRepositoryInterface as SoldProduct;

class InventoryController extends Controller
{
    private $sold_product;

    function __construct(SoldProduct $sold_product)
    {
        $this->sold_product = $sold_product;
    }

    public function statistics()
    {
        $data = $this->getStatisticsData();
        return view('inventory.stats', $data);
    }

    private function getStatisticsData()
    {
        $sold_products = $this->sold_product->getSoldProducts()(15);

        return [
            'sold_products_by_stock' => $sold_products('total_quantities', 'desc'),
            'sold_products_by_incomes' => $sold_products('incomes', 'desc'),
            'sold_products_by_average_price' => $sold_products('avg_price', 'desc')
        ];
    }
}
