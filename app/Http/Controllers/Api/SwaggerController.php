<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

/**
 * @OA\Info(
 *     title="Amplifica E-commerce API",
 *     version="1.0.0",
 *     description="API para gestión de productos, pedidos y cotizaciones de envío",
 *     @OA\Contact(
 *         email="admin@amplifica.com"
 *     )
 * )
 * @OA\Server(
 *     url="http://localhost/api",
 *     description="Servidor de desarrollo"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class SwaggerController extends Controller
{
    //
}