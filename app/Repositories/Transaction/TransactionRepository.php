<?php

namespace App\Repositories\Transaction;

use App\Repositories\AbstractRepository;
use App\Models\Transaction;
use App\Models\Client;
use Carbon\Carbon;
use DB;

class TransactionRepository extends AbstractRepository implements TransactionRepositoryInterface
{
    protected $model;

    function __construct(Transaction $model, Client $client)
    {
        $this->model = $model;
        $this->client = $client;
    }

    public function create(array $request)
    {
        [
            'client_id' => $client_id,
            'amount' => $amount,
            'type' => $type,
        ]
            = $request;

        $return_type = 0;
        if ($client_id) {
            switch ($type) {
                case 'income':
                    $request['title'] = "Payment Received from Customer ID: $client_id";
                    break;
                case 'expense':
                    $request['title'] = "Customer ID Return Payment: $client_id";
                    if ($amount)
                        $request['amount'] = (float) $amount * (-1);
                    break;
            }

            $this->model->create($request);
            $client = $this->client->find($client_id);
            $client->balance += $amount;
            $client->save();
            return $return_type = 1;
        }

        switch ($type) {
            case 'expense':
                if ($amount)
                    $request['amount'] = (float) $amount * (-1);
                $this->model->create($request);
                $return_type = 2;
            case 'payment':
                if ($amount)
                    $request['amount'] = (float) $amount * (-1);
                $this->model->create($request);
                $return_type = 3;
                break;
            case 'income':
                $this->model->create($request);
                $return_type = 4;
                break;
        }

        return $return_type;
    }

    public function update(int $id, array $request)
    {
        $transaction = $this->findById($id);
        $transaction->update($request);
    }

    public function delete(int $id)
    {
        $transaction = $this->findById($id);
        $transaction->delete();
    }

    public function getMethodBalance()
    {
        $transactions = $this->model->select([
            'payment_methods.name as payment_method_name',
            DB::raw('SUM(transactions.amount) as total_amount'),
            DB::raw('SUM(COALESCE(transactions.amount, 0)) as total_amount'),
        ])
            ->rightJoin('payment_methods', function ($join) {
                $join->on('payment_methods.id', '=', 'transactions.payment_method_id')
                    ->where(DB::raw('MONTH(transactions.created_at)'), Carbon::now()->month);
            })
            ->groupBy('payment_method_name')
            ->get();

        $calculate_balance = function ($total_balance, $transaction) {
            return $total_balance + $transaction->total_amount;
        };

        $monthly_balance_per_method = $transactions->pluck('total_amount', 'payment_method_name');
        $entire_balance_this_month = $transactions->reduce($calculate_balance, 0.0);

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

    public function getTransactionBalance()
    {
        $transaction_amount = $this->model->select(['created_at', 'amount'])->get();
        $callback = function (array $date_range) {
            [ $start_date, $end_date ] = $date_range;
            $start_date = Carbon::parse($start_date);
            $end_date = Carbon::parse($end_date);

            return function ($transaction) use ($start_date, $end_date) {
                $created_at = Carbon::parse($transaction->created_at);
                return $created_at->gte($start_date) && $created_at->lte($end_date);
            };
        };

        $transaction_balance = [
            'daily' => $transaction_amount->filter($callback(get_date_range('daily')))->sum('amount'),
            'weekly' => $transaction_amount->filter($callback(get_date_range('weekly')))->sum('amount'),
            'quarter' => $transaction_amount->filter($callback(get_date_range('quarterly')))->sum('amount'),
            'monthly' => $transaction_amount->filter($callback(get_date_range('monthly')))->sum('amount'),
            'annual' => $transaction_amount->filter($callback(get_date_range('annually')))->sum('amount'),
        ];

        return function ($period = 'all') use ($transaction_balance) {
            return $period == 'all' ? $transaction_balance : ($transaction_balance[$period] ?? []);
        };
    }

    public function getTransactionPeriods()
    {
        $transactions = $this->model->all();
        $callback = function (array $date_range) {
            [ $start_date, $end_date ] = $date_range;
            $start_date = Carbon::parse($start_date);
            $end_date = Carbon::parse($end_date);

            return function ($transaction) use ($start_date, $end_date) {
                $created_at = Carbon::parse($transaction->created_at);
                return $created_at->gte($start_date) && $created_at->lte($end_date);
            };
        };

        return [
            'Day' => $transactions->filter($callback(get_date_range('today'))),
            'Yesterday' => $transactions->filter($callback(get_date_range('yesterday'))),
            'Week' => $transactions->filter($callback(get_date_range('week'))),
            'Monthly' => $transactions->filter($callback(get_date_range('month'))),
            'Trimester' => $transactions->filter($callback(get_date_range('trimester'))),
            'Year' => $transactions->filter($callback(get_date_range('year'))),
        ];
    }
}
