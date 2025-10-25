<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Producto</title>
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
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #456547; /* Color 2 */
        }
        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .btn {
            display: inline-block;
            background-color: #65865d; /* Color 3 */
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 1rem;
        }
        .btn:hover {
            background-color: #456547; /* Color 2 */
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Crear Nuevo Producto</h1>

        <form action="{{ route('products.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="name">Nombre</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="price">Precio</label>
                <input type="number" id="price" name="price" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="weight">Peso</label>
                <input type="number" id="weight" name="weight" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="width">Ancho</label>
                <input type="number" id="width" name="width" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="height">Alto</label>
                <input type="number" id="height" name="height" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="length">Largo</label>
                <input type="number" id="length" name="length" step="0.01" required>
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" required>
            </div>
            <button type="submit" class="btn">Guardar Producto</button>
        </form>
    </div>
</body>
</html>
