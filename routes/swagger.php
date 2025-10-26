<?php

use Illuminate\Support\Facades\Route;

// Ruta para la documentación Swagger
Route::get('/api/documentation', function () {
    return view('swagger.index');
})->name('swagger.docs');

// Ruta para servir el JSON de Swagger
Route::get('/api/swagger.json', function () {
    $swagger = \OpenApi\Generator::scan([app_path('Http/Controllers/Api')]);
    return response()->json($swagger->toArray());
})->name('swagger.json');