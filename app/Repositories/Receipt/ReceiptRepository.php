<?php

namespace App\Repositories\Receipt;

use App\Repositories\AbstractRepository;
use Carbon\Carbon;
use App\Models\Receipt;

class ReceiptRepository extends AbstractRepository implements ReceiptRepositoryInterface
{
    protected $model;

    function __construct(Receipt $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $receipt = $this->model->create($request);
        return $receipt;
    }

    public function update(int $id, array $request)
    {
        $receipt = $this->findById($id);
        $receipt->update($request);
    }

    public function delete(int $id)
    {
        $receipt = $this->findById($id);
        $receipt->delete();
    }

    public function finalize(int $id)
    {
        $receipt = $this->findById($id);
        $receipt->finalized_at = Carbon::now()->toDateTimeString();
        $receipt->save();

        foreach ($receipt->products as $received_product) {
            $received_product->product->stock += $received_product->stock;
            $received_product->product->stock_defective += $received_product->stock_defective;
            $received_product->product->save();
        }
    }
}
