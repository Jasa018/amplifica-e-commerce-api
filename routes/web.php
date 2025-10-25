<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('products', ProductController::class);
"\nRoute::get('/test-pedidos', function() { return App\Models\Pedido::count(); });" 
