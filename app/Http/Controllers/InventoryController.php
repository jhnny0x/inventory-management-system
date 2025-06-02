<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Repositories\SoldProduct\SoldProductRepositoryInterface as SoldProduct;
use App\Repositories\ProductCategory\ProductCategoryRepositoryInterface as ProductCategory;
use App\Repositories\Product\ProductRepositoryInterface as Product;

class InventoryController extends Controller
{
    private $sold_product;
    private $product_category;
    private $product;

    function __construct(SoldProduct $sold_product, ProductCategory $product_category, Product $product)
    {
        $this->sold_product = $sold_product;
        $this->product_category = $product_category;
        $this->product = $product;
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
            'categories' => $this->product_category->all(),
            'products' => $this->product->all(),
            'soldproductsbystock' => $sold_products('total_qty', 'desc'),
            'soldproductsbyincomes' => $sold_products('incomes', 'desc'),
            'soldproductsbyavgprice' => $sold_products('avg_price', 'desc')
        ];
    }
}
