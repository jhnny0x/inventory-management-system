<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Repositories\Sale\SaleRepositoryInterface as Sale;
use App\Repositories\SoldProduct\SoldProductRepositoryInterface as SoldProduct;
use App\Repositories\Transaction\TransactionRepositoryInterface as Transaction;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface as PaymentMethod;
use DB;

class HomeController extends Controller
{
    private $sale;
    private $sold_product;
    private $transaction;
    private $payment_method;

    function __construct(Sale $sale, SoldProduct $sold_product, Transaction $transaction, PaymentMethod $payment_method)
    {
        $this->sale = $sale;
        $this->sold_product = $sold_product;
        $this->transaction = $transaction;
        $this->payment_method = $payment_method;
    }

    public function index()
    {
        $data = $this->getDashboardData();
        return view('dashboard', $data);
    }

    private function getDashboardData()
    {
        [
            'entire_balance_this_month' => $entire_balance_this_month,
            'monthly_balance_per_method' => $monthly_balance_per_method
        ]
            = $this->transaction->getMethodBalance();

        [
            'latest_income' => $latest_income,
            'latest_expenses' => $latest_expenses,
            'previous_months' => $previous_months,
            'semestral_income' => $semestral_income,
            'semestral_expenses' => $semestral_expenses
        ]
            = $this->transaction->getMonthlyTransactions();

        $latest_transactions = $this->transaction->latest()->limit(20)->get();


        [
            'number_of_sales' => $number_of_sales,
            'number_of_clients' => $number_of_clients
        ]
            = $this->sale->getMonthlySalesCount();

        $unfinished_sales = $this->sale->whereNull('finalized_at')->get();
        $monthly_product_quantities = $this->sold_product->getMonthlyProductQuantities();

        return [
            'entire_balance_this_month' => $entire_balance_this_month,
            'monthly_balance_per_method'=> $monthly_balance_per_method,
            'latest_transactions' => $latest_transactions,
            'unfinished_sales' => $unfinished_sales,
            'number_of_sales' => json_encode($number_of_sales, JSON_NUMERIC_CHECK),
            'number_of_clients' => json_encode($number_of_clients, JSON_NUMERIC_CHECK),
            'monthly_product_quantities' => json_encode($monthly_product_quantities, JSON_NUMERIC_CHECK),
            'previous_months' => $previous_months,
            'latest_income' => json_encode($latest_income, JSON_NUMERIC_CHECK),
            'latest_expenses' => json_encode($latest_expenses, JSON_NUMERIC_CHECK),
            'semestral_expenses' => $semestral_expenses,
            'semestral_income' => $semestral_income
        ];
    }
}
