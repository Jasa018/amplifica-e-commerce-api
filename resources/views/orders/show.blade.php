<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Pedido</title>
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
        }
        .details {
            margin-top: 1rem;
        }
        .details p {
            font-size: 1.1rem;
            line-height: 1.6;
        }
        .details strong {
            color: #456547; /* Color 2 */
        }
        .btn-back {
            display: inline-block;
            margin-top: 2rem;
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
        <h1>Detalles del Pedido</h1>

        <div class="details">
            <p><strong>ID:</strong> {{ $order->id }}</p>
            <p><strong>Cliente:</strong> {{ $order->cliente_nombre }}</p>
            <p><strong>Fecha:</strong> {{ $order->fecha }}</p>
            <p><strong>Total:</strong> ${{ number_format($order->total, 2) }}</p>
        </div>

        <a href="{{ route('orders.index') }}" class="btn-back">Volver a la Lista</a>
    </div>
</body>
</html>
