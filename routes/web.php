<?php

use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\BarangController;

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
    ->name('dashboard.index');

Route::resource('categories', CategoryController::class);
Route::resource('orders', OrderController::class);
Route::resource('users', UserController::class);
Route::resource('barangs', BarangController::class);

