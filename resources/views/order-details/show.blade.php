<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Orden #{{ $orderDetail->id }}</title>
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
            color: #33533a;
        }
        .detail-group {
            margin-bottom: 1rem;
        }
        .detail-group strong {
            display: block;
            margin-bottom: 0.5rem;
            color: #456547;
        }
        .btn-back {
            display: inline-block;
            background-color: #6c757d;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            margin-top: 1rem;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Detalle de Orden #{{ $orderDetail->id }}</h1>

        <div class="detail-group">
            <strong>Orden ID:</strong>
            <span>#{{ $orderDetail->order_id }}</span>
        </div>
        <div class="detail-group">
            <strong>Producto:</strong>
            <span>{{ $orderDetail->product->name }}</span>
        </div>
        <div class="detail-group">
            <strong>Cantidad:</strong>
            <span>{{ $orderDetail->quantity }}</span>
        </div>
        <div class="detail-group">
            <strong>Precio Unitario:</strong>
            <span>${{ number_format($orderDetail->unit_price, 2) }}</span>
        </div>
        <div class="detail-group">
            <strong>Subtotal:</strong>
            <span>${{ number_format($orderDetail->quantity * $orderDetail->unit_price, 2) }}</span>
        </div>

        <a href="{{ route('order-details.index') }}" class="btn-back">Volver a la Lista</a>
    </div>
</body>
</html>
