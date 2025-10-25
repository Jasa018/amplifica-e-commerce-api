<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Producto</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 600px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #33533a; /* Color 1 */
            border-bottom: 2px solid #456547;
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        .product-detail {
            margin-bottom: 1rem;
        }
        .product-detail strong {
            color: #456547; /* Color 2 */
            display: inline-block;
            width: 100px;
        }
        .btn-back {
            display: inline-block;
            margin-top: 1.5rem;
            background-color: #65865d; /* Color 3 */
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }
        .btn-back:hover {
            background-color: #456547; /* Color 2 */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>{{ $product->name }}</h1>

        <div class="product-detail"><strong>ID:</strong> {{ $product->id }}</div>
        <div class="product-detail"><strong>Precio:</strong> ${{ number_format($product->price, 2) }}</div>
        <div class="product-detail"><strong>Stock:</strong> {{ $product->stock }}</div>
        <div class="product-detail"><strong>Peso:</strong> {{ $product->weight }}</div>
        <div class="product-detail"><strong>Ancho:</strong> {{ $product->width }}</div>
        <div class="product-detail"><strong>Alto:</strong> {{ $product->height }}</div>
        <div class="product-detail"><strong>Largo:</strong> {{ $product->length }}</div>
        <div class="product-detail"><strong>Creado:</strong> {{ $product->created_at->format('d/m/Y H:i') }}</div>
        <div class="product-detail"><strong>Actualizado:</strong> {{ $product->updated_at->format('d/m/Y H:i') }}</div>

        <a href="{{ route('products.index') }}" class="btn-back">Volver a la lista</a>
    </div>
</body>
</html>
