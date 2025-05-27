<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Transaction;
use App\Models\PaymentMethod;
use Illuminate\Http\Request;

class MethodController extends Controller
{
    public function index()
    {
        return view('methods.index', [
            'methods' => PaymentMethod::paginate(15),
            'month' => Carbon::now()->month
        ]);
    }

    public function create()
    {
        return view('methods.create');
    }

    public function store(Request $request, PaymentMethod $method)
    {
        $method->create($request->all());

        return redirect()
            ->route('methods.index')
            ->withStatus('Payment method successfully created.');
    }

    public function show(PaymentMethod $method)
    {
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $transactionname = [
            'income' => 'Income',
            'payment' => 'Payment',
            'expense' => 'Expense',
            'transfer' => 'Transfer'
        ];

        $balances = [
            'daily' => Transaction::whereBetween('created_at', [Carbon::now()->startOfDay(), Carbon::now()->endOfDay()])->sum('amount'),
            'weekly' => Transaction::whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('amount'),
            'quarter' => Transaction::whereBetween('created_at', [Carbon::now()->startOfQuarter(), Carbon::now()->endOfQuarter()])->sum('amount'),
            'monthly' => Transaction::whereBetween('created_at', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])->sum('amount'),
            'annual' => Transaction::whereBetween('created_at', [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()])->sum('amount'),
        ];

        return view('methods.show', [
            'method' => $method,
            'transactions' => Transaction::where('payment_method_id', $method->id)->latest()->paginate(25),
            'balances' => $balances,
            'transactionname' => $transactionname
        ]);
    }

    public function edit(PaymentMethod $method)
    {
        return view('methods.edit', compact('method'));
    }

    public function update(Request $request, PaymentMethod $method)
    {
        $method->update($request->all());

        return redirect()
            ->route('methods.index')
            ->withStatus('Payment method updated satisfactorily.');
    }

    public function destroy(PaymentMethod $method)
    {
        $method->delete();

        return back()->withStatus('Payment method successfully removed.');
    }
}
