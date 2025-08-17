<?php

namespace App\Repositories\PaymentMethod;

use App\Repositories\AbstractRepository;
use App\Models\PaymentMethod;

class PaymentMethodRepository extends AbstractRepository implements PaymentMethodRepositoryInterface
{
    function __construct(PaymentMethod $model)
    {
        $this->model = $model;
    }

    public function create(array $request)
    {
        $payment_method = $this->model->create($request);
        return $payment_method;
    }

    public function update(int $id, array $request)
    {
        $payment_method = $this->findById($id);
        $payment_method->update($request);
    }

    public function delete(int $id)
    {
        $payment_method = $this->findById($id);
        $payment_method->delete();
    }
}
