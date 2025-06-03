<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;

use App\Repositories\Client\ClientRepositoryInterface as Client;
use App\Repositories\Sale\SaleRepositoryInterface as Sale;
use App\Repositories\Product\ProductRepositoryInterface as Product;
use App\Repositories\SoldProduct\SoldProductRepositoryInterface as SoldProduct;
use App\Repositories\Transaction\TransactionRepositoryInterface as Transaction;
use App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface as PaymentMethod;

class SaleController extends Controller
{
    private $client;
    private $sale;
    private $product;
    private $sold_product;
    private $transaction;
    private $payment_method;

    function __construct(Client $client, Sale $sale, Product $product, SoldProduct $sold_product, Transaction $transaction, PaymentMethod $payment_method)
    {
        $this->client = $client;
        $this->sale = $sale;
        $this->product = $product;
        $this->sold_product = $sold_product;
        $this->transaction = $transaction;
        $this->payment_method = $payment_method;
    }

    public function index()
    {
        $data['sales'] = $this->sale->select([
                'sales.*',
                'clients.name as client_name',
                'clients.document_id as client_document_id',
                'clients.document_type as client_document_type',
                'clients.deleted_at as client_deleted_at',
            ])
            ->leftJoin('clients', 'clients.id', '=', 'sales.client_id')
            ->latest()
            ->paginate(25);

        return view('sales.index', $data);
    }

    public function create()
    {
        $data['clients'] = $this->client->all();
        return view('sales.create', $data);
    }

    public function store(Request $request)
    {
        ['client_id' => $client_id] = $input = $request->all();
        $sale = $this->sale->where('client_id', $client_id)
            ->whereNull('finalized_at')
            ->get();

        if ($sale->count()) {
            return back()->withError('There is already an unfinished sale belonging to this customer. <a href="'.route('sales.show', ['sale' => $sale->first()->id]).'">Click here to go to it</a>');
        }

        $sale = $this->sale->create($input);
        return redirect()->route('sales.show', ['sale' => $sale->id])->withStatus('Sale registered successfully, you can start registering products and transactions.');
    }

    public function show(int $id)
    {
        $data['sale'] = $this->sale->with(['client'])->find($id);
        return view('sales.show', $data);
    }

    public function destroy(int $id)
    {
        $this->sale->delete($id);
        return redirect()->route('sales.index')->withStatus('The sale record has been successfully deleted.');
    }

    public function finalize(int $id)
    {
        $response = $this->sale->finalize($id);
        if (count($response)) {
            [ $product_name, $product_stock ] = $response;
            return back()->withError("The product '$product_name' does not have enough stock. Only has $product_stock units.");
        }

        return back()->withStatus('The sale has been successfully completed.');
    }

    public function addProduct(int $id)
    {
        $data['sale'] = $this->sale->find($id);
        $data['products'] = $this->product->all();

        return view('sales.addproduct', $data);
    }

    public function storeProduct(Request $request, int $id)
    {
        ['price' => $price, 'qty' => $quantity] = $input = $request->all();
        $input['total_amount'] = $price * $quantity;
        $sale = $this->sale->find($id);
        $this->sold_product->create($input);

        return redirect()->route('sales.show', ['sale' => $sale->id])->withStatus('Product successfully registered.');
    }

    public function editProduct(int $sale_id, int $sold_product_id)
    {
        $data['products'] = $this->product->all();
        $data['sale'] = $this->sale->find($sale_id);
        $data['soldproduct'] = $this->sale->find($sold_product_id);

        return view('sales.editproduct', $data);
    }

    public function updateProduct(Request $request, int $sale_id, int $sold_product_id)
    {
        ['price' => $price, 'qty' => $quantity] = $input = $request->all();

        $input['total_amount'] = $price * $quantity;
        $this->sold_product->update($sold_product_id, $input);

        return redirect()->route('sales.show', ['sale' => $sale_id])->withStatus('Product successfully modified.');
    }

    public function destroyProduct(int $id)
    {
        $this->sold_product->delete($id);
        return back()->withStatus('The product has been disposed of successfully.');
    }
}
