<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pedidos</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f9;
            color: #333;
            margin: 0;
            padding: 2rem;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            color: #33533a; /* Color 1 */
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
        }
        th, td {
            padding: 0.75rem;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        thead {
            background-color: #456547; /* Color 2 */
            color: #deffb4; /* Color 5 */
        }
        tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }
        tbody tr:hover {
            background-color: #98ba80; /* Color 4 */
            color: white;
        }
        .alert-success {
            padding: 1rem;
            margin-bottom: 1rem;
            background-color: #deffb4; /* Color 5 */
            color: #33533a; /* Color 1 */
            border: 1px solid #98ba80; /* Color 4 */
            border-radius: 4px;
        }
        .actions a {
            color: #33533a;
            text-decoration: none;
            margin-right: 10px;
        }
        .actions a:hover {
            text-decoration: underline;
        }
        .btn-delete {
            background: none;
            border: none;
            color: #c0392b;
            cursor: pointer;
            text-decoration: none;
            font-family: sans-serif;
            font-size: 1em;
            padding: 0;
            margin: 0;
        }
        .btn-delete:hover {
            text-decoration: underline;
        }
        .btn-create {
            display: inline-block;
            background-color: #65865d; /* Color 3 */
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
            margin-bottom: 1rem;
        }
        .btn-create:hover {
            background-color: #456547; /* Color 2 */
        }
    </style>
</head>
<body>
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <h1>Lista de Pedidos</h1>
        <a href="{{ route('orders.create') }}" class="btn-create">Crear Nuevo Pedido</a>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cliente</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($orders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->cliente_nombre }}</td>
                        <td>{{ $order->fecha }}</td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td class="actions">
                            <a href="{{ route('orders.show', $order->id) }}">Ver</a>
                            <a href="{{ route('orders.edit', $order->id) }}">Editar</a>
                            <form action="{{ route('orders.destroy', $order->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete" onclick="return confirm('¿Estás seguro de que quieres eliminar este pedido?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</body>
</html>
