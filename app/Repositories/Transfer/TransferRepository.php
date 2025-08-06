<?php

namespace App\Repositories\Transfer;

use App\Repositories\AbstractRepository;
use App\Models\Transfer;

class TransferRepository extends AbstractRepository implements TransferRepositoryInterface
{
    protected $model;

    function __construct(Transfer $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $transfer = $this->model->create($request);
        return $transfer;
    }

    public function update(int $id, array $request)
    {
        $transfer = $this->findById($id);
        $transfer->update($request);
    }

    public function delete(int $id)
    {
        $transfer = $this->findById($id);
        $transfer->delete();
    }
}
