<?php

namespace App\Http\Controllers;

use App\Models\Receipt;
use App\Models\Provider;
use App\Models\Product;

use Carbon\Carbon;
use App\Models\ReceivedProduct;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function index()
    {
        $receipts = Receipt::paginate(25);

        return view('inventory.receipts.index', compact('receipts'));
    }

    public function create()
    {
        $providers = Provider::all();

        return view('inventory.receipts.create', compact('providers'));
    }

    public function store(Request $request, Receipt $receipt)
    {
        $receipt = $receipt->create($request->all());

        return redirect()
            ->route('receipts.show', $receipt)
            ->withStatus('Receipt registered successfully, you can start adding the products belonging to it.');
    }

    public function show(Receipt $receipt)
    {
        return view('inventory.receipts.show', compact('receipt'));
    }

    public function destroy(Receipt $receipt)
    {
        $receipt->delete();

        return redirect()
            ->route('receipts.index')
            ->withStatus('Receipt successfully removed.');
    }

    public function finalize(Receipt $receipt)
    {
        $receipt->finalized_at = Carbon::now()->toDateTimeString();
        $receipt->save();

        foreach($receipt->products as $receivedproduct) {
            $receivedproduct->product->stock += $receivedproduct->stock;
            $receivedproduct->product->stock_defective += $receivedproduct->stock_defective;
            $receivedproduct->product->save();
        }

        return back()->withStatus('Receipt successfully completed.');
    }

    public function addProduct(Receipt $receipt)
    {
        $products = Product::all();

        return view('inventory.receipts.addproduct', compact('receipt', 'products'));
    }

    public function storeProduct(Request $request, Receipt $receipt, ReceivedProduct $receivedproduct)
    {
        $receivedproduct->create($request->all());

        return redirect()
            ->route('receipts.show', $receipt)
            ->withStatus('Product added successfully.');
    }

    public function editProduct(Receipt $receipt, ReceivedProduct $receivedproduct)
    {
        $products = Product::all();

        return view('inventory.receipts.editproduct', compact('receipt', 'receivedproduct', 'products'));
    }

    public function updateProduct(Request $request, Receipt $receipt, ReceivedProduct $receivedproduct)
    {
        $receivedproduct->update($request->all());
        return redirect()->route('receipts.show', $receipt)->withStatus('Product edited successfully.');
    }

    public function destroyProduct(Receipt $receipt, ReceivedProduct $receivedproduct)
    {
        $receivedproduct->delete();

        return redirect()
            ->route('receipts.show', $receipt)
            ->withStatus('Product removed successfully.');
    }
}
