<?php

namespace App\Repositories\Transaction;

use App\Repositories\AbstractRepository;
use App\Models\Transaction;
use Carbon\Carbon;
use DB;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
    protected $model;

    function __construct(Transaction $model)
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

    public function getMethodBalance()
    {
        $transactions = $this->model->select([
            'payment_methods.name as payment_method_name',
            DB::raw('SUM(transactions.amount) as total_amount'),
            DB::raw('SUM(COALESCE(transactions.amount, 0)) as total_amount'),
        ])
            ->rightJoin('payment_methods', 'payment_methods.id', '=', 'transactions.payment_method_id')
            ->groupBy('payment_method_name')
            ->get();

        $calculate_balance = function ($total_balance, $transaction) {
            return $total_balance + $transaction->total_amount;
        };

        $monthly_balance_per_method = $transactions->pluck('total_amount', 'payment_method_name');
        $entire_balance_this_month = $transactions->filter(function ($transaction) {
            return Carbon::parse($transaction->created_at)->month == Carbon::now()->month;
        })
            ->reduce($calculate_balance, 0.0);

        return [
            'entire_balance_this_month' => $entire_balance_this_month,
            'monthly_balance_per_method' => $monthly_balance_per_method
        ];
    }

    public function getMonthlyTransactions()
    {
        $latest_income = [];
        $semestral_income = 0;
        $incomes = $this->model->select([
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(amount) as total_income')
        ])
            ->income()
            ->thisYear()
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $latest_expenses = [];
        $semestral_expenses = 0;
        $expenses = $this->model->select([
            DB::raw('MONTH(created_at) as month'),
            DB::raw('SUM(ABS(amount)) as total_expenses')
        ])
            ->expense()
            ->thisYear()
            ->groupBy('month')
            ->get()
            ->keyBy('month');

        $previous_months = [];
        $current_date = Carbon::now();
        foreach (MONTHS_IN_HALF_YEAR as $month) {
            $total_income = $incomes[$month]['total_income'] ?? 0;
            $semestral_income += $total_income;
            $latest_income[] = round($total_income);

            $total_expenses = $expenses[$month]['total_expenses'] ?? 0;
            $semestral_expenses += $total_expenses;
            $latest_expenses[] = round($total_expenses);

            array_push($previous_months, $current_date->shortMonthName);
            $current_date->subMonth(1);
        }

        return [
            'latest_income' => $latest_income,
            'latest_expenses' => $latest_expenses,
            'semestral_income' => $semestral_income,
            'semestral_expenses' => $semestral_expenses,
            'previous_months' => array_reverse($previous_months),
        ];
    }
}
