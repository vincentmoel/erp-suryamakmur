<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ConfigController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
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
Route::group(['middleware' => ['revalidate']], function () {
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');

    Route::group(['middleware' => ['guest']], function () {
        Route::get('/login', function () {
            return redirect('/');
        });
        Route::get('/', [AuthController::class, 'login'])->name('login');
    });


    Route::group(['middleware' => ['auth', 'refresh.permission']], function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('check.permission:Dashboard,read');

        // =============== USER =============== \\ 
        $module = "User";
        Route::group(['prefix' => 'users', 'as' => 'users.', 'controller' => UserController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
        });

        // =============== ROLE =============== \\ 
        $module = "Role";
        Route::group(['prefix' => 'roles', 'as' => 'roles.', 'controller' => RoleController::class], function () use ($module) {
            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
            Route::delete('/{encryptedRoleId}/{encryptedUserId}/delete-user-role', 'deleteUserRole')->name('delete-user-role')->middleware("check.permission:$module,delete");
        });

        // =============== CONFIG =============== \\ 
        $module = "Config";
        Route::group(['prefix' => 'configs', 'as' => 'configs.', 'controller' => ConfigController::class], function () use ($module) {
            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
        });

        // =============== CATEGORY =============== \\
        $module = "Category";
        Route::group(['prefix' => 'categories', 'as' => 'categories.', 'controller' => CategoryController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
        });

        // =============== UNIT =============== \\
        $module = "Unit";
        Route::group(['prefix' => 'units', 'as' => 'units.', 'controller' => UnitController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
        });

        // =============== CUSTOMER =============== \\
        $module = "Customer";
        Route::group(['prefix' => 'customers', 'as' => 'customers.', 'controller' => CustomerController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
            Route::patch('/{encryptedId}/toggle-active', 'toggleActive')->name('toggleActive')->middleware("check.permission:$module,update");
        });

        // =============== PRODUCT =============== \\
        $module = "Product";
        Route::group(['prefix' => 'products', 'as' => 'products.', 'controller' => ProductController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
            Route::patch('/{encryptedId}/toggle-active', 'toggleActive')->name('toggleActive')->middleware("check.permission:$module,update");
        });

        // =============== INVOICE =============== \\
        $module = "Invoice";
        Route::group(['prefix' => 'invoices', 'as' => 'invoices.', 'controller' => InvoiceController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/cancel', 'cancel')->name('cancel')->middleware("check.permission:$module,update");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
        });

        // =============== RECEIPT =============== \\
        $module = "Receipt";
        Route::group(['prefix' => 'receipts', 'as' => 'receipts.', 'controller' => ReceiptController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
        });

        // =============== VENDOR =============== \\
        $module = "Vendor";
        Route::group(['prefix' => 'vendors', 'as' => 'vendors.', 'controller' => VendorController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
            Route::patch('/{encryptedId}/toggle-active', 'toggleActive')->name('toggleActive')->middleware("check.permission:$module,update");
        });

        // =============== BILL =============== \\
        $module = "Bill";
        Route::group(['prefix' => 'bills', 'as' => 'bills.', 'controller' => BillController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/receive', 'receive')->name('receive')->middleware("check.permission:$module,update");
            Route::patch('/{encryptedId}/cancel', 'cancel')->name('cancel')->middleware("check.permission:$module,update");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::patch('/{encryptedId}', 'update')->name('update')->middleware("check.permission:$module,update");
            Route::get('/{encryptedId}/edit', 'edit')->name('edit')->middleware("check.permission:$module,update");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
        });

        // =============== SALES RETURN =============== \\
        $module = "SalesReturn";
        Route::group(['prefix' => 'sales-returns', 'as' => 'sales-returns.', 'controller' => SalesReturnController::class], function () use ($module) {
            Route::get('/trashed', 'trashed')->name('trashed')->middleware("check.permission:$module,restore");
            Route::patch('/{encryptedId}/restore', 'restore')->name('restore')->middleware("check.permission:$module,restore");

            Route::get('/', 'index')->name('index')->middleware("check.permission:$module,read");
            Route::get('/create', 'create')->name('create')->middleware("check.permission:$module,create");
            Route::post('/', 'store')->name('store')->middleware("check.permission:$module,create");
            Route::get('/{encryptedId}', 'show')->name('show')->middleware("check.permission:$module,read");
            Route::delete('/{encryptedId}/destroy', 'destroy')->name('destroy')->middleware("check.permission:$module,delete");
        });

        Route::patch('/language/{lang}', [LanguageController::class, 'update'])->name('language.update')->whereIn('lang', ['id', 'en']);

        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });
});

require_once "ajax.php";