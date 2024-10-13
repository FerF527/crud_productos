<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Productos</title>
</head>

<body>
    <h1>Productos</h1>
    <table id="productTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        function fetchProducts() {
            fetch('/product')
                .then(response => response.json())
                .then(products => {
                    const tbody = document.querySelector('#productTable tbody');
                    tbody.innerHTML = '';
                    products.forEach(product => {
                        const row = `<tr>
                                <td>${product.id}</td>
                                <td>${product.title}</td>
                                <td>${product.price}</td>
                                <td>
                                    <button>Editar</button>
                                    <button onclick="deleteProduct(${product.id})">Borrar</button>
                                </td>
                            </tr>`;
                        tbody.innerHTML += row;
                    });
                });
        }

        function deleteProduct(id) {
            fetch(`/product/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => fetchProducts());
        }
        
        fetchProducts();
    </script>
</body>

</html>
