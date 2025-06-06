<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\Transfer\TransferRepository as Transfer;
use App\Repositories\Transaction\TransactionRepository as Transaction;
use App\Repositories\PaymentMethod\PaymentMethodRepository as PaymentMethod;

class TransferController extends Controller
{
    function __construct(Transfer $transfer, PaymentMethod $payment_method, Transaction $transaction)
    {
        $this->transfer = $transfer;
        $this->payment_method = $payment_method;
        $this->transaction = $transaction;
    }

    public function index(Transfer $transfer)
    {
        $data['transfers'] = $this->transfer->latest()->paginate(25);
        return view('transfers.index', $data);
    }

    public function create()
    {
        $data['methods'] = $this->payment_method->all();
        return view('transfers.create', $data);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $transfer = $this->transfer->create($input);

        $this->transaction->create([
            'type' => 'expense',
            'title' => "TransferID: {$transfer->id}",
            'transfer_id' => $transfer->id,
            'payment_method_id' => $transfer->sender_method_id,
            'amount' => ((float) abs($transfer->sended_amount) * (-1)),
            'user_id' => Auth::id(),
            'reference' => $transfer->reference
        ]);

        $this->transaction->create([
            'type' => "income",
            'title' => "TransferID: {$transfer->id}",
            'transfer_id' => $transfer->id,
            'payment_method_id' => $transfer->receiver_method_id,
            'amount' => abs($transfer->received_amount),
            'user_id' => Auth::id(),
            'reference' => $transfer->reference
        ]);

        return redirect()
            ->route('transfer.index')
            ->withStatus('Transaction registered successfully.');
    }

    public function destroy(int $id)
    {
        $this->transfer->delete($id);
        return back()->withStatus('The transfer and its associated transactions have been successfully removed.');
    }
}
