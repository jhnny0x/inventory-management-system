<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/', ['as' => 'home', 'uses' => 'HomeController@index']);

    Route::resources([
        'users' => 'UserController',
        'providers' => 'ProviderController',
        'inventory/products' => 'ProductController',
        'clients' => 'ClientController',
        'inventory/categories' => 'ProductCategoryController',
        'transactions/transfer' => 'TransferController',
        'methods' => 'MethodController',
    ]);

    Route::resource('transactions', 'TransactionController')->except(['create', 'show']);
    Route::group(['prefix' => 'transactions'], function () {
        Route::get('statistics/{year?}/{month?}/{day?}', ['as' => 'transactions.statistics', 'uses' => 'TransactionController@statistics']);
        Route::get('{type}', ['as' => 'transactions.type', 'uses' => 'TransactionController@type']);
        Route::get('{type}/create', ['as' => 'transactions.create', 'uses' => 'TransactionController@create']);
        Route::get('{transaction}/edit', ['as' => 'transactions.edit', 'uses' => 'TransactionController@edit']);
    });

    Route::group(['prefix' => 'inventories'], function () {
        Route::get('statistics/{year?}/{month?}/{day?}', ['as' => 'inventories.statistics', 'uses' => 'InventoryController@statistics']);
        Route::resource('receipts', 'ReceiptController')->except(['edit', 'update']);
        Route::group(['prefix' => 'receipts/{receipt}'], function () {
            Route::get('finalize', ['as' => 'receipts.finalize', 'uses' => 'ReceiptController@finalize']);
            Route::get('product/add', ['as' => 'receipts.product.add-product', 'uses' => 'ReceiptController@addProduct']);
            Route::get('product/{receivedproduct}/edit', ['as' => 'receipts.product.edit-product', 'uses' => 'ReceiptController@editProduct']);
            Route::post('product', ['as' => 'receipts.product.store', 'uses' => 'ReceiptController@storeProduct']);
            Route::match(['put', 'patch'], 'product/{receivedproduct}', ['as' => 'receipts.product.update-product', 'uses' => 'ReceiptController@updateProduct']);
            Route::delete('product/{receivedproduct}', ['as' => 'receipts.product.destroy-product', 'uses' => 'ReceiptController@destroyProduct']);
        });
    });

    Route::resource('sales', 'SaleController')->except(['edit', 'update']);
    Route::group(['prefix' => 'sales/{sale}'], function () {
        Route::get('finalize', ['as' => 'sales.finalize', 'uses' => 'SaleController@finalize']);
        Route::get('product/add', ['as' => 'sales.product.add', 'uses' => 'SaleController@addProduct']);
        Route::get('product/{soldproduct}/edit', ['as' => 'sales.product.edit', 'uses' => 'SaleController@editProduct']);
        Route::post('product', ['as' => 'sales.product.store', 'uses' => 'SaleController@storeProduct']);
        Route::match(['put', 'patch'], 'product/{soldproduct}', ['as' => 'sales.product.update', 'uses' => 'SaleController@updateProduct']);
        Route::delete('product/{soldproduct}', ['as' => 'sales.product.destroy', 'uses' => 'SaleController@destroyProduct']);
    });

    Route::group(['prefix' => 'profile'], function () {
        Route::get('/', ['as' => 'profile.edit', 'uses' => 'ProfileController@edit']);
        Route::match(['put', 'patch'], '/', ['as' => 'profile.update', 'uses' => 'ProfileController@update']);
        Route::match(['put', 'patch'], 'password', ['as' => 'profile.password', 'uses' => 'ProfileController@password']);
    });

    Route::get('clients/{id}/transactions/add', ['as' => 'clients.transactions.add-transaction', 'uses' => 'ClientController@addTransaction']);
    Route::get('icons', ['as' => 'pages.icons', 'uses' => 'PageController@icons']);
    Route::get('notifications', ['as' => 'pages.notifications', 'uses' => 'PageController@notifications']);
    Route::get('tables', ['as' => 'pages.tables', 'uses' => 'PageController@tables']);
    Route::get('typography', ['as' => 'pages.typography', 'uses' => 'PageController@typography']);
});
