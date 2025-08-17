<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface as PaymentMethod;
use App\Repositories\Transaction\TransactionRepositoryInterface as Transaction;

class MethodController extends Controller
{
    private $payment_method;
    private $transaction;

    function __construct(PaymentMethod $payment_method, Transaction $transaction)
    {
        $this->payment_method = $payment_method;
        $this->transaction = $transaction;
    }

    public function index()
    {
        $data['methods'] = $this->payment_method->paginate(15);
        $data['month'] = Carbon::now()->month;

        return view('methods.index', $data);
    }

    public function create()
    {
        return view('methods.create');
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $this->payment_method->create($input);

        return redirect()->route('methods.index')->withStatus('Payment method successfully created.');
    }

    public function show(int $id)
    {
        $transaction_names = get_transaction_names();
        $transaction_balance = $this->transaction->getTransactionBalance();

        $data['method'] = $payment_method = $this->payment_method->find($id);
        $data['balances'] = $transaction_balance();
        $data['transactions'] = $this->transaction->where('payment_method_id', $payment_method->id)->latest()->paginate(25);
        $data['transactionname'] = $transaction_names;

        return view('methods.show', $data);
    }

    public function edit($id)
    {
        $data['method'] = $this->payment_method->find($id);
        return view('methods.edit', $data);
    }

    public function update(Request $request, int $id)
    {
        $input = $request->all();
        $this->payment_method->update($id, $input);
        return redirect()->route('methods.index')->withStatus('Payment method updated satisfactorily.');
    }

    public function destroy(int $id)
    {
        $this->payment_method->delete($id);
        return back()->withStatus('Payment method successfully removed.');
    }
}
