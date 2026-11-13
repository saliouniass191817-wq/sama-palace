<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\WhatsAppOrderController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route(auth()->check() ? 'dashboard' : 'login');
})->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredUserController::class, 'store']);
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store'])->middleware('throttle:5,1');
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {

    Route::get('/dashboard', [ShopController::class, 'dashboard'])->name('dashboard');
    Route::get('/shops', fn () => redirect()->route('dashboard'))->name('shops.index');
    Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::get('/shops/edit', [ShopController::class, 'edit'])->name('shops.edit');
    Route::put('/shops', [ShopController::class, 'update'])->name('shops.update');

    Route::resource('products', ProductController::class)->except(['show']);
});

Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shops.public');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/products/{product}', [CartController::class, 'store'])->name('cart.products.store');
Route::patch('/cart/products/{product}', [CartController::class, 'update'])->name('cart.products.update');
Route::delete('/cart/products/{product}', [CartController::class, 'destroy'])->name('cart.products.destroy');
Route::post('/cart/checkout', [CartController::class, 'checkout'])
    ->middleware('throttle:30,1')
    ->name('cart.checkout');
Route::get('/order/{product}/whatsapp', WhatsAppOrderController::class)
    ->middleware('throttle:30,1')
    ->name('orders.whatsapp');
