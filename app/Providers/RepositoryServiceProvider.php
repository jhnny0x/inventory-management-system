<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(
            \App\Http\Repositories\Client\ClientRepositoryInterface::class,
            \App\Http\Repositories\Client\ClientRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\PaymentMethod\PaymentMethodRepositoryInterface::class,
            \App\Http\Repositories\PaymentMethod\PaymentMethodRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\Product\ProductRepositoryInterface::class,
            \App\Http\Repositories\Product\ProductRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\ProductCategory\ProductCategoryRepositoryInterface::class,
            \App\Http\Repositories\ProductCategory\ProductCategoryRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\Provider\ProviderRepositoryInterface::class,
            \App\Http\Repositories\Provider\ProviderRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\Receipt\ReceiptRepositoryInterface::class,
            \App\Http\Repositories\Receipt\ReceiptRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\ReceivedProduct\ReceivedProductRepositoryInterface::class,
            \App\Http\Repositories\ReceivedProduct\ReceivedProductRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\Sale\SaleRepositoryInterface::class,
            \App\Http\Repositories\Sale\SaleRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\SoldProduct\SoldProductRepositoryInterface::class,
            \App\Http\Repositories\SoldProduct\SoldProductRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\Transaction\TransactionRepositoryInterface::class,
            \App\Http\Repositories\Transaction\TransactionRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\TransactionType\TransactionTypeRepositoryInterface::class,
            \App\Http\Repositories\TransactionType\TransactionTypeRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\Transfer\TransferRepositoryInterface::class,
            \App\Http\Repositories\Transfer\TransferRepository::class
        );
        $this->app->bind(
            \App\Http\Repositories\User\UserRepositoryInterface::class,
            \App\Http\Repositories\User\UserRepository::class
        );
    }
}
