<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;

use App\Repositories\ReceivedProduct\ReceivedProductRepositoryInterface as ReceivedProduct;
use App\Repositories\Product\ProductRepositoryInterface as Product;
use App\Repositories\Provider\ProviderRepositoryInterface as Provider;
use App\Repositories\Receipt\ReceiptRepositoryInterface as Receipt;

class ReceiptController extends Controller
{
    private $received_product;
    private $product;
    private $provider;
    private $receipt;

    function __construct(ReceivedProduct $received_product, Product $product, Provider $provider, Receipt $receipt)
    {
        $this->received_product = $received_product;
        $this->product = $product;
        $this->provider = $provider;
        $this->receipt = $receipt;
    }

    public function index()
    {
        $data['receipts'] = $this->receipt->paginate(25);
        return view('inventory.receipts.index', $data);
    }

    public function create()
    {
        $data['providers'] = $this->provider->all();
        return view('inventory.receipts.create', $data);
    }

    public function store(Request $request)
    {
        $input = $request->all();
        $receipt = $this->receipt->create($input);

        return redirect()->route('receipts.show', $receipt)->withStatus('Receipt registered successfully, you can start adding the products belonging to it.');
    }

    public function show(int $id)
    {
        $data['receipt'] = $this->receipt->with(['user', 'provider', 'products'])->find($id);
        return view('inventory.receipts.show', $data);
    }

    public function destroy(int $id)
    {
        $this->receipt->delete($id);
        return redirect()->route('receipts.index')->withStatus('Receipt successfully removed.');
    }

    public function finalize(int $id)
    {
        $this->receipt->finalize($id);
        return back()->withStatus('Receipt successfully completed.');
    }

    public function addProduct(int $id)
    {
        $data['products'] = $this->product->all();
        $data['receipt'] = $this->receipt->find($id);

        return view('inventory.receipts.addproduct', $data);
    }

    public function storeProduct(Request $request, int $id)
    {
        $input = $request->all();
        $receipt = $this->receipt->find($id);
        $this->received_product->create($input);

        return redirect()->route('receipts.show', $receipt)->withStatus('Product added successfully.');
    }

    public function editProduct(int $receipt_id, int $received_product_id)
    {
        $data['products'] = $this->product->all();
        $data['received_product'] = $this->received_product->find($received_product_id);
        $data['receipt'] = $this->receipt->find($receipt_id);

        return view('inventory.receipts.editproduct', $data);
    }

    public function updateProduct(Request $request, int $receipt_id, int $received_product_id)
    {
        $input = $request->all();
        $receipt = $this->receipt->find($receipt_id);
        $this->received_product->update($id, $input);

        return redirect()->route('receipts.show', $receipt)->withStatus('Product edited successfully.');
    }

    public function destroyProduct(int $id, ReceivedProduct $receivedproduct)
    {
        $receipt = $this->receipt->find($id);
        $this->received_product->delete($id);

        return redirect()->route('receipts.show', $receipt)->withStatus('Product removed successfully.');
    }
}
