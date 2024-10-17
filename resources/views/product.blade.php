<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Productos</title>
    <link rel="stylesheet" href="/css/product.css">
</head>

<body>
    <div class="container">
        <h1>Productos</h1>

        <div class="form-group">
            <h3>Agregar Producto</h3>
            <div class="form-box">
                <input type="text" id="title" placeholder="Nombre del producto">
                <input type="number" id="price" placeholder="Precio del producto">
                <button onclick="addProduct()">Agregar Producto</button>
            </div>
        </div>

        <div class="form-group">
            <h3>Filtros</h3>
            <div class="form-box">
                <label for="filterTitle">Nombre:</label>
                <input type="text" id="filterTitle" placeholder="Nombre del producto">

                <label for="filterPrice">Precio:</label>
                <input type="number" id="filterPrice" placeholder="Precio del producto">

                <label for="filterDate">Fecha:</label>
                <input type="date" id="filterDate" placeholder="Fecha del producto">
            </div>
            <button onclick="applyFilters()">Buscar</button>
        </div>

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

        <div class="pagination">
            <button id="prevPage" onclick="prevPage()" disabled>Anterior</button>
            <button id="nextPage" onclick="nextPage()">Siguiente</button>
            <span>Página: <span id="currentPage">1</span></span>
        </div>
    </div>

    <script src="/js/sweetalert/swal2.js"></script>
    <script src="/js/jquery/jquery.js"></script>
    <script>
        let currentPage = 1;
        const productsPerPage = 5;

        //aplicar filtros
        function applyFilters() {
            currentPage = 1;
            getProducts();
        }
        //Obtener todos los productos
        async function getProducts() {
            const filterTitle = $('#filterTitle').val();
            const filterPrice = $('#filterPrice').val();
            const filterDate = $('#filterDate').val();

            const queryParams = new URLSearchParams({
                page: currentPage,
                per_page: productsPerPage,
                title: filterTitle || '',
                price: filterPrice || '',
                date: filterDate || ''
            });

            try {
                const response = await fetch(`/product?${queryParams.toString()}`);
                const data = await response.json();
                const products = data.products;
                const totalPages = data.totalPages;

                const tbody = $('#productTable tbody');
                tbody.empty(); // Limpiar la tabla

                products.forEach(product => {
                    const formattedDate = formatDate(product.created_at);
                    const row = `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.title}</td>
                    <td>${product.price}</td>
                    <td>${formattedDate}</td>
                    <td>
                        <button onclick="editProduct(${product.id}, '${product.title}',${product.price})">Editar</button>
                        <button onclick="deleteProduct(${product.id})">Borrar</button>
                    </td>
                </tr>`;
                    tbody.append(row);
                });

                // Control de paginación
                $('#currentPage').text(currentPage);
                $('#prevPage').prop('disabled', currentPage === 1);
                $('#nextPage').prop('disabled', currentPage >= totalPages);
            } catch (error) {
                Swal.fire('Error', 'Error al cargar los productos', 'error');
            }
        }

        function nextPage() {
            currentPage++;
            getProducts();
        }

        function prevPage() {
            if (currentPage > 1) {
                currentPage--;
                getProducts();
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

                const responseData = await response.json();

                if (!response.ok) {
                    // Recolectar los errores de validación en un solo mensaje
                    let errorMessages = '';
                    if (responseData.errors) {
                        for (const field in responseData.errors) {
                            if (responseData.errors.hasOwnProperty(field)) {
                                responseData.errors[field].forEach(error => {
                                    errorMessages += `<li style="color: red">${error}</li>`;
                                });
                            }
                        }
                    }

                    // Mostrar los errores
                    Swal.fire({
                        icon: 'error',
                        title: responseData.message || 'Error al agregar el producto',
                        html: `<ul>${errorMessages}</ul>`,
                    });

                    return;
                }

                // Si no hay errores, mostrar el éxito
                Swal.fire('Producto agregado correctamente', `El producto ${responseData.title} fue agregado.`,
                    'success');
                getProducts();

                // Limpiar los campos del formulario
                $('#title').val('');
                $('#price').val('');
            } catch (error) {
                Swal.fire('Error', error.message, 'error');
            }
        }

        // Editar producto
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

                    const responseData = await response.json();

                    if (!response.ok) {
                        // Recolectar los errores de validación en un solo mensaje
                        let errorMessages = '';
                        if (responseData.errors) {
                            for (const field in responseData.errors) {
                                if (responseData.errors.hasOwnProperty(field)) {
                                    responseData.errors[field].forEach(error => {
                                        errorMessages += `<li style="color: red">${error}</li>`;
                                    });
                                }
                            }
                        }
                        console.log("responseData",responseData);
                        // Mostrar los errores
                        Swal.fire({
                            icon: 'error',
                            title: responseData.message || 'Error al actualizar el producto',
                            html: `<ul>${errorMessages}</ul>`,
                        });

                        return; 
                    }

                    // Si no hay errores, mostrar el éxito
                    Swal.fire('Actualizado', `El producto ha sido actualizado correctamente.`, 'success');
                    getProducts(); // Actualizar la lista de productos

                } catch (error) {
                    Swal.fire('Error', JSON.stringify(error.message), 'error');
                }
            }
        }

        // formato de fecha

        function formatDate(dateString) {
            const date = new Date(dateString);
            const day = String(date.getDate()).padStart(2, '0');
            const month = String(date.getMonth() + 1).padStart(2, '0'); // Los meses comienzan desde 0
            const year = date.getFullYear();

            return `${day}-${month}-${year}`;
        }

        getProducts();
    </script>

</body>

</html>
