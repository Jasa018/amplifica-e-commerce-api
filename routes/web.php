<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\ProductController;
use App\Http\Controllers\Web\OrderController;
use App\Http\Controllers\Web\OrderDetailController;
use App\Http\Controllers\Web\CotizacionController;

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
    
    // Rutas de cotización
    Route::get('/cotizaciones', [CotizacionController::class, 'index'])->name('cotizaciones.index');
    Route::post('/cotizar-envio', [CotizacionController::class, 'cotizar'])->name('cotizar-envio');
});

// Rutas de documentación Swagger (públicas)
Route::get('/api/documentation', function () {
    return view('swagger.index');
})->name('swagger.docs');

Route::get('/api/swagger.json', function () {
    $swagger = [
        'openapi' => '3.0.0',
        'info' => [
            'title' => 'Amplifica E-commerce API',
            'version' => '1.0.0',
            'description' => 'API para gestión de productos, pedidos y cotizaciones de envío'
        ],
        'servers' => [
            ['url' => url('/api'), 'description' => 'Servidor de desarrollo']
        ],
        'components' => [
            'securitySchemes' => [
                'bearerAuth' => [
                    'type' => 'http',
                    'scheme' => 'bearer',
                    'bearerFormat' => 'JWT'
                ]
            ]
        ],
        'paths' => [
            '/auth/login' => [
                'post' => [
                    'tags' => ['Autenticación'],
                    'summary' => 'Iniciar sesión',
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['email', 'password'],
                                    'properties' => [
                                        'email' => ['type' => 'string', 'format' => 'email'],
                                        'password' => ['type' => 'string']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Login exitoso'],
                        '422' => ['description' => 'Credenciales inválidas']
                    ]
                ]
            ],
            '/products' => [
                'get' => [
                    'tags' => ['Productos'],
                    'summary' => 'Listar productos',
                    'security' => [['bearerAuth' => []]],
                    'responses' => [
                        '200' => ['description' => 'Lista de productos']
                    ]
                ],
                'post' => [
                    'tags' => ['Productos'],
                    'summary' => 'Crear producto',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['name', 'price', 'weight', 'stock'],
                                    'properties' => [
                                        'name' => ['type' => 'string'],
                                        'price' => ['type' => 'number'],
                                        'weight' => ['type' => 'number'],
                                        'width' => ['type' => 'number'],
                                        'height' => ['type' => 'number'],
                                        'length' => ['type' => 'number'],
                                        'stock' => ['type' => 'integer']
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '201' => ['description' => 'Producto creado'],
                        '422' => ['description' => 'Error de validación']
                    ]
                ]
            ],
            '/products/{id}' => [
                'get' => [
                    'tags' => ['Productos'],
                    'summary' => 'Obtener producto',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Producto encontrado'],
                        '404' => ['description' => 'Producto no encontrado']
                    ]
                ],
                'put' => [
                    'tags' => ['Productos'],
                    'summary' => 'Actualizar producto',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Producto actualizado']
                    ]
                ],
                'delete' => [
                    'tags' => ['Productos'],
                    'summary' => 'Eliminar producto',
                    'security' => [['bearerAuth' => []]],
                    'parameters' => [
                        [
                            'name' => 'id',
                            'in' => 'path',
                            'required' => true,
                            'schema' => ['type' => 'integer']
                        ]
                    ],
                    'responses' => [
                        '200' => ['description' => 'Producto eliminado']
                    ]
                ]
            ],
            '/cotizar' => [
                'post' => [
                    'tags' => ['Cotizaciones'],
                    'summary' => 'Cotizar envío',
                    'security' => [['bearerAuth' => []]],
                    'requestBody' => [
                        'required' => true,
                        'content' => [
                            'application/json' => [
                                'schema' => [
                                    'type' => 'object',
                                    'required' => ['comuna', 'productos'],
                                    'properties' => [
                                        'comuna' => ['type' => 'string', 'example' => 'Providencia'],
                                        'productos' => [
                                            'type' => 'array',
                                            'items' => [
                                                'type' => 'object',
                                                'properties' => [
                                                    'weight' => ['type' => 'number', 'example' => 1.5],
                                                    'quantity' => ['type' => 'integer', 'example' => 2]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'responses' => [
                        '200' => [
                            'description' => 'Tarifas de envío',
                            'content' => [
                                'application/json' => [
                                    'schema' => [
                                        'type' => 'array',
                                        'items' => [
                                            'type' => 'object',
                                            'properties' => [
                                                'name' => ['type' => 'string', 'example' => 'Tarifa Express'],
                                                'code' => ['type' => 'string', 'example' => 'EXP'],
                                                'price' => ['type' => 'number', 'example' => 4990],
                                                'transitDays' => ['type' => 'integer', 'example' => 0]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        '422' => ['description' => 'Error de validación'],
                        '503' => ['description' => 'Error de conexión']
                    ]
                ]
            ],
            '/orders' => [
                'get' => [
                    'tags' => ['Pedidos'],
                    'summary' => 'Listar pedidos',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['200' => ['description' => 'Lista de pedidos']]
                ],
                'post' => [
                    'tags' => ['Pedidos'],
                    'summary' => 'Crear pedido',
                    'security' => [['bearerAuth' => []]],
                    'responses' => ['201' => ['description' => 'Pedido creado']]
                ]
            ]
        ]
    ];
    
    return response()->json($swagger);
})->name('swagger.json');