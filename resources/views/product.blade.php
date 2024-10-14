<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Productos</title>
</head>

<body>
    <h1>Productos</h1>
    <h3>Agregar Producto</h3>
    <input type="text" id="title" placeholder="Nombre del producto">
    <input type="number" id="price" placeholder="Precio del producto">
    <button onclick="addProduct()">Agregar Producto</button>
    <table id="productTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Precio</th>
                <th>Fecha</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        //Obtener todos los productos
        function getProducts() {
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
                                <td>${product.created_at}</td>
                                <td>
                                    <button>Editar</button>
                                    <button onclick="deleteProduct(${product.id})">Borrar</button>
                                </td>
                            </tr>`;
                        tbody.innerHTML += row;
                    });
                });
        }

        //Eliminar un producto
        function deleteProduct(id) {
            fetch(`/product/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            }).then(() => getProducts());
        }

        // Agregar producto
        function addProduct() {
            const title = document.getElementById('title').value;
            const price = document.getElementById('price').value;

            if (!title || !price) {
                alert("Por favor, completa ambos campos.");
                return;
            }

            fetch('/product', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title,
                        price
                    })
                }).then(response => response.json())
                .then(product => {
                    getProducts();
                });

            // Limpiar los campos del formulario
            document.getElementById('title').value = '';
            document.getElementById('price').value = '';
        }

        getProducts();
    </script>
</body>

</html>
