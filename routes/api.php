<?php

use Illuminate\Support\Facades\Route;
use MiladSarli\CartSystem\Http\Controllers\CartController;

Route::middleware(config('cart.middleware', ['auth:sanctum']))->prefix(config('cart.route_prefix', 'api/v1'))->group(function () {
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::post('cart/checkout', [CartController::class, 'checkout'])->name('cart.checkout');
});
