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

    <script src="/js/sweetalert/swal2.js"></script>
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
                                    <button onclick="editProduct(${product.id}, '${product.title}', ${product.price})">Editar</button>
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
                Swal.fire("Por favor, complete el campo Nombre y Precio.");
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

        //Editar producto

        function editProduct(id, title, price) {
            Swal.fire({
                title: 'Editar Producto',
                html: `
                        <label for="editTitle">Nombre</label>
                        <input type="text" id="editTitle" class="swal2-input" value="${title}">

                        <label for="editPrice">Precio</label>
                        <input type="number" id="editPrice" class="swal2-input" value="${price}">
                `,
                focusConfirm: false,
                showCancelButton: true,
                confirmButtonText: 'Guardar',
                preConfirm: () => {
                    const newTitle = document.getElementById('editTitle').value;
                    const newPrice = document.getElementById('editPrice').value;

                    if (!newTitle || !newPrice) {
                        Swal.showValidationMessage('Por favor, complete el campo Nombre y Precio.');
                    } else {
                        return {
                            title: newTitle,
                            price: newPrice
                        };
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Realizar la petición PUT para actualizar el producto
                    fetch(`/product/${id}`, {
                            method: 'PUT',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify({
                                title: result.value.title,
                                price: result.value.price
                            })
                        })
                        .then(response => response.json())
                        .then(() => {
                            Swal.fire('Actualizado', 'El producto ha sido actualizado', 'success');
                            getProducts();
                        });
                }
            });
        }
        getProducts();
    </script>

</body>

</html>
