<?php

use App\Http\Controllers\TableController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'login'])->name('login');
Route::post('/login', [AuthController::class, 'authenticate']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


// Pelanggan
Route::get('/', [UserController::class, 'index'])->name('home');
Route::get('/product', [UserController::class, 'product'])->name('product');
Route::get('/cart', [UserController::class, 'cart'])->name('cart');
Route::get('/cart/remove/{key}', [UserController::class, 'removeFromCart'])->name('cart.remove');
Route::get('/cart/add/{product}', [UserController::class, 'addToCart'])->name('cart.add');
Route::post('/cart/update/{id}', [UserController::class, 'updateCart'])->name('cart.update');

Route::get('/checkout', [UserController::class, 'checkout'])->name('checkout');
Route::post('/checkout/submit', [UserController::class, 'submitCheckout'])->name('checkout.submit');
Route::get('/order/{code}', [UserController::class, 'orderDetail'])->name('order.detail');
Route::get('/order-success', [UserController::class, 'orderSuccess'])->name('orderSuccess');



// Admin

Route::middleware(['auth', IsAdmin::class])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('products', ProductController::class);

    Route::post('products/save', [ProductController::class, 'save'])->name('products.save');
    
    Route::post('transactions/save', [TransactionController::class, 'save'])->name('transactions.save');

    Route::get('transactions/export', [TransactionController::class, 'export'])->name('transactions.export');

    Route::resource('transactions', TransactionController::class);

    Route::resource('categories', CategoryController::class);

    Route::post('categories/save', [CategoryController::class, 'save'])->name('categories.save');

    Route::resource('tables', TableController::class);

    Route::post('tables/save', [TableController::class, 'save'])->name('tables.save');

    Route::post('tables/available-selected', [TableController::class, 'setSelectedAvailable'])->name('tables.setSelectedAvailable');

    Route::get('tables/qrcode/{table}', [TableController::class, 'qrcode'])->name('tables.qrcode');

});

