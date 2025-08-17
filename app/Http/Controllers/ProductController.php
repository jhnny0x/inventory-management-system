<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Repositories\Product\ProductRepositoryInterface as Product;
use App\Repositories\ProductCategory\ProductCategoryRepositoryInterface as ProductCategory;

class ProductController extends Controller
{
    private $product;
    private $product_category;

    function __construct(Product $product, ProductCategory $product_category)
    {
        $this->product_category = $product_category;
        $this->product = $product;
    }

    public function index()
    {
        $data['products'] = $this->product->paginate(25);
        return view('inventory.products.index', $data);
    }

    public function create()
    {
        $data['categories'] = $this->product_category->all();
        return view('inventory.products.create', $data);
    }

    public function store(ProductRequest $request)
    {
        $input = $request->all();
        $this->product->create($input);

        return redirect()->route('products.index')->withStatus('Product successfully registered.');
    }

    public function show(int $id)
    {
        $data['product'] = $product = $this->product->find($id);
        $data['solds'] = $product->solds()->latest()->limit(25)->get();
        $data['receiveds'] = $product->receiveds()->latest()->limit(25)->get();

        return view('inventory.products.show', $data);
    }

    public function edit(int $id)
    {
        $data['product'] = $this->product->find($id);
        $data['categories'] = $this->product_category->all();

        return view('inventory.products.edit', $data);
    }

    public function update(ProductRequest $request, int $id)
    {
        $input = $request->all();
        $this->product->update($id, $input);

        return redirect()->route('products.index')->withStatus('Product updated successfully.');
    }

    public function destroy(int $id)
    {
        $this->product->delete($id);
        return redirect()->route('products.index')->withStatus('Product removed successfully.');
    }
}
