<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Repositories\Sale\SaleRepositoryInterface as Sale;
use App\Repositories\Client\ClientRepositoryInterface as Client;
use App\Repositories\Provider\ProviderRepositoryInterface as Provider;
use App\Repositories\SoldProduct\SoldProductRepositoryInterface as SoldProduct;
use App\Repositories\Transaction\TransactionRepositoryInterface as Transaction;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface as PaymentMethod;

class TransactionController extends Controller
{
    function __construct(Sale $sale, Client $client, Provider $provider, SoldProduct $sold_product, Transaction $transaction, PaymentMethod $payment_method)
    {
        $this->sale = $sale;
        $this->client = $client;
        $this->provider = $provider;
        $this->sold_product = $sold_product;
        $this->transaction = $transaction;
        $this->payment_method = $payment_method;
    }

    public function index()
    {
        $data['transactionname'] = get_transaction_names();
        $data['transactions'] = $this->transaction->latest()->paginate(25);

        return view('transactions.index', $data);
    }

    public function statistics()
    {
        $data['salesperiods'] = $this->sale->getSalePeriods();
        $data['transactionsperiods'] = $this->transaction->getTransactionPeriods();
        $data['date'] = Carbon::now();
        $data['methods'] = $this->payment_method->all();
        $data['clients'] = $this->client->where('balance', '!=', '0.00')->get();

        return view('transactions.stats', $data);
    }

    public function type($type)
    {
        $data['transactions'] = $this->transaction->where('type', $type)
            ->latest()
            ->paginate(25);

        return view("transactions.$type.index", $data);
    }

    public function create($type)
    {
        $data['payment_methods'] = $this->payment_method->all();
        $data['providers'] = $this->provider->all();

        return view("transactions.$type.create", $data);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $return_type = $this->transaction->create($input);

        switch ($return_type) {
            case 1:
                return redirect()
                    ->route('clients.show', $input['client_id'])
                    ->withStatus('Successfully registered transaction.');
            case 2:
                return redirect()
                    ->route('transactions.type', ['type' => 'expense'])
                    ->withStatus('Expense recorded successfully.');
            case 3:
                return redirect()
                    ->route('transactions.type', ['type' => 'payment'])
                    ->withStatus('Payment registered successfully.');
            case 4:
                return redirect()
                    ->route('transactions.type', ['type' => 'income'])
                    ->withStatus('Login successfully registered.');
            default:
                return redirect()
                    ->route('transactions.index')
                    ->withStatus('Successfully registered transaction.');
        }
    }

    public function edit(int $id)
    {
        $data['transaction'] = $transaction = $this->transaction->find($id);
        $data['payment_methods'] = $this->payment_method->all();
        $data['providers'] = $this->provider->all();

        return view("transactions.{$transaction->type}.edit", $data);
    }

    public function update(Request $request, int $id)
    {
        $input = $request->all();
        $this->transaction->update($id, $input);

        switch ($input['type']) {
            case 'expense':
                return redirect()
                    ->route('transactions.type', ['type' => 'expense'])
                    ->withStatus('Expense updated sucessfully.');
            case 'payment':
                return redirect()
                    ->route('transactions.type', ['type' => 'payment'])
                    ->withStatus('Payment updated satisfactorily.');
            case 'income':
                return redirect()
                    ->route('transactions.type', ['type' => 'income'])
                    ->withStatus('Login successfully updated.');
            default:
                return redirect()
                    ->route('transactions.index')
                    ->withStatus('Transaction updated successfully.');
        }
    }

    public function destroy(int $id)
    {
        $transaction = $this->transaction->find($id);
        if ($transaction->transfer) {
            return back()->withStatus('You cannot remove a transaction from a transfer. You must delete the transfer to delete its records.');
        }

        $transaction_type = $transaction->type;
        $transaction->delete();

        $message_status = 'Transaction deleted successfully.';
        switch ($transaction_type) {
            case 'expense':
                $message_status = 'Expenditure successfully removed.';
                break;
            case 'payment':
                $message_status = 'Payment successfully removed.';
                break;
            case 'income':
                $message_status = 'Entry successfully removed.';
                break;
        }

        return back()->withStatus($message_status);
    }
}
