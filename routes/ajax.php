<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(['middleware' => ['auth'], 'prefix' => 'ajax', 'as' => 'ajax.'], function () {
    Route::get('/products/{id}', [\App\Http\Controllers\ProductController::class, 'ajaxInfo'])->name('products.info');
    Route::get('/products/{id}/stock', [\App\Http\Controllers\ProductController::class, 'ajaxStock'])->name('products.stock');
    Route::get('/customers/{id}/invoices', [\App\Http\Controllers\ReceiptController::class, 'ajaxCustomerInvoices'])->name('customers.invoices');
    Route::get('/invoices/{encryptedId}/returnable-batches', [\App\Http\Controllers\SalesReturnController::class, 'ajaxReturnableBatches'])->name('invoices.returnable-batches');
    Route::get('/invoices/{encryptedId}/preview', [\App\Http\Controllers\InvoiceController::class, 'ajaxPreview'])->name('invoices.preview');
    Route::post('/configs/save', [\App\Http\Controllers\ConfigController::class, 'ajaxSave'])->name('configs.save');
    Route::get('/configs/preview-code', [\App\Http\Controllers\ConfigController::class, 'previewCode'])->name('configs.preview-code');
});