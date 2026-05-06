<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredUserController;
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
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', [ShopController::class, 'dashboard'])->name('dashboard');
    Route::get('/shops/create', [ShopController::class, 'create'])->name('shops.create');
    Route::post('/shops', [ShopController::class, 'store'])->name('shops.store');
    Route::get('/shops/edit', [ShopController::class, 'edit'])->name('shops.edit');
    Route::put('/shops', [ShopController::class, 'update'])->name('shops.update');

    Route::resource('products', ProductController::class)->except(['show']);
});

Route::get('/shop/{slug}', [ShopController::class, 'show'])->name('shops.public');
Route::get('/order/{product}/whatsapp', WhatsAppOrderController::class)->name('orders.whatsapp');
