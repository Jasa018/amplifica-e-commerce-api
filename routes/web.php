<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\OrderDetailController;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [AuthController::class, 'login'])->middleware('guest');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Rutas protegidas
Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Rutas web (nombres sin prefijo) — se usan en las vistas y formularios
    Route::resource('products', ProductController::class);
    Route::resource('orders', OrderController::class);
    Route::resource('order-details', OrderDetailController::class);
});