<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CotizacionController;
use App\Http\Controllers\Api\HistorialCotizacionController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\OrderDetailController;
use App\Http\Controllers\Api\UserController;

// Rutas públicas de autenticación
Route::post('/auth/login', [AuthController::class, 'login'])->name('api.auth.login');

// Rutas protegidas
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout'])->name('api.auth.logout');
    Route::get('/auth/user', [AuthController::class, 'user'])->name('api.auth.user');

    // Nombrar las rutas API con prefijo `api.` para evitar colisiones con las web
    Route::name('api.')->group(function () {
        Route::apiResource('products', ProductController::class);
        Route::apiResource('orders', OrderController::class);
        Route::apiResource('order-details', OrderDetailController::class);
        Route::apiResource('users', UserController::class);
        Route::apiResource('historial-cotizaciones', HistorialCotizacionController::class, ['only' => ['index', 'show', 'destroy']]);
    });
    
    // Cotizaciones
    Route::post('/cotizar-envio', [CotizacionController::class, 'cotizar']);
});

