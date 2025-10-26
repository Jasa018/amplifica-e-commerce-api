<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Detalle de Orden</title>
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
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #456547;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            background-color: #65865d;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }
        .btn:hover {
            background-color: #456547;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Detalle de Orden</h1>

        <form action="{{ route('order-details.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="order_id">Orden</label>
                <select id="order_id" name="order_id" required>
                    @foreach ($orders as $order)
                        <option value="{{ $order->id }}">#{{ $order->id }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="product_id">Producto</label>
                <select id="product_id" name="product_id" required>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="quantity">Cantidad</label>
                <input type="number" id="quantity" name="quantity" required min="1">
            </div>
            <div class="form-group">
                <label for="unit_price">Precio Unitario</label>
                <input type="number" id="unit_price" name="unit_price" step="0.01" required min="0">
            </div>
            <button type="submit" class="btn">Guardar Detalle</button>
        </form>
    </div>
</body>
</html>
