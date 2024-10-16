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
    <script src="/js/jquery/jquery.js"></script>
    <script>
        //Obtener todos los productos
        async function getProducts() {
            try {
                const response = await fetch('/product');
                const products = await response.json();

                const tbody = $('#productTable tbody');
                tbody.empty(); // Limpiar la tabla

                products.forEach(product => {
                    const row = `
                        <tr>
                            <td>${product.id}</td>
                            <td>${product.title}</td>
                            <td>${product.price}</td>
                            <td>${product.created_at}</td>
                            <td>
                                <button onclick="editProduct(${product.id}, '${product.title}',${product.price})">Editar</button>
                                <button onclick="deleteProduct(${product.id})">Borrar</button>
                            </td>
                        </tr>`;
                    tbody.append(row);
                });
            } catch (error) {
                Swal.fire('Error', 'Error al cargar los productos', 'error');
            }
        }

        //Eliminar un producto
        async function deleteProduct(id) {
            const confirmDelete = await Swal.fire({
                title: '¿Estás seguro?',
                text: "No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, eliminarlo!',
                cancelButtonText: 'Cancelar'
            });

            if (!confirmDelete.isConfirmed) return;

            try {
                const response = await fetch(`/product/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    }
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al eliminar el producto');
                }

                await Swal.fire('OK', 'El producto ha sido eliminado.', 'success');
                getProducts(); // Actualizar la tabla con los nuevos datos
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        }

        // Agregar producto
        async function addProduct() {
            const title = $('#title').val();
            const price = $('#price').val();

            if (!title || !price) {
                Swal.fire("Por favor, complete el campo Nombre y Precio.");
                return;
            }

            try {
                const response = await fetch('/product', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        title,
                        price
                    })
                });

                if (!response.ok) {
                    const errorData = await response.json();
                    throw new Error(errorData.message || 'Error al agregar el producto');
                }

                const product = await response.json();
                Swal.fire('Producto agregado correctamente', `El producto ${product.title} fue agregado.`, 'success');
                getProducts(); // Actualizar la tabla con los nuevos datos

                // Limpiar los campos del formulario
                $('#title').val('');
                $('#price').val('');
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        }

        //Editar producto

        async function editProduct(id, title, price) {
            const {
                value: formValues
            } = await Swal.fire({
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
                    const newTitle = $('#editTitle').val();
                    const newPrice = $('#editPrice').val();

                    if (!newTitle || !newPrice) {
                        Swal.showValidationMessage('Por favor, complete ambos campos.');
                        return null;
                    }

                    return {
                        title: newTitle,
                        price: newPrice
                    };
                }
            });

            if (formValues) {
                try {
                    const response = await fetch(`/product/${id}`, {
                        method: 'PUT',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(formValues)
                    });

                    if (!response.ok) {
                        throw new Error('Error al actualizar el producto');
                    }

                    const result = await response.json();
                    Swal.fire('Actualizado', 'El producto ha sido actualizado correctamente.', 'success');
                    getProducts(); // Actualizar la lista de productos
                } catch (error) {
                    Swal.fire('Error', error.message, 'error');
                }
            }
        }
        getProducts();
    </script>

</body>

</html>
