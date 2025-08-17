<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class RepositoryServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->app->bind(
            \App\Repositories\Client\ClientRepositoryInterface::class,
            \App\Repositories\Client\ClientRepository::class
        );
        $this->app->bind(
            \App\Repositories\PaymentMethod\PaymentMethodRepositoryInterface::class,
            \App\Repositories\PaymentMethod\PaymentMethodRepository::class
        );
        $this->app->bind(
            \App\Repositories\Product\ProductRepositoryInterface::class,
            \App\Repositories\Product\ProductRepository::class
        );
        $this->app->bind(
            \App\Repositories\ProductCategory\ProductCategoryRepositoryInterface::class,
            \App\Repositories\ProductCategory\ProductCategoryRepository::class
        );
        $this->app->bind(
            \App\Repositories\Provider\ProviderRepositoryInterface::class,
            \App\Repositories\Provider\ProviderRepository::class
        );
        $this->app->bind(
            \App\Repositories\Receipt\ReceiptRepositoryInterface::class,
            \App\Repositories\Receipt\ReceiptRepository::class
        );
        $this->app->bind(
            \App\Repositories\ReceivedProduct\ReceivedProductRepositoryInterface::class,
            \App\Repositories\ReceivedProduct\ReceivedProductRepository::class
        );
        $this->app->bind(
            \App\Repositories\Sale\SaleRepositoryInterface::class,
            \App\Repositories\Sale\SaleRepository::class
        );
        $this->app->bind(
            \App\Repositories\SoldProduct\SoldProductRepositoryInterface::class,
            \App\Repositories\SoldProduct\SoldProductRepository::class
        );
        $this->app->bind(
            \App\Repositories\Transaction\TransactionRepositoryInterface::class,
            \App\Repositories\Transaction\TransactionRepository::class
        );
        $this->app->bind(
            \App\Repositories\TransactionType\TransactionTypeRepositoryInterface::class,
            \App\Repositories\TransactionType\TransactionTypeRepository::class
        );
        $this->app->bind(
            \App\Repositories\Transfer\TransferRepositoryInterface::class,
            \App\Repositories\Transfer\TransferRepository::class
        );
        $this->app->bind(
            \App\Repositories\User\UserRepositoryInterface::class,
            \App\Repositories\User\UserRepository::class
        );
    }
}
