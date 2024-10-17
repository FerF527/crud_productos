<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Listado de productos.
     */
    public function index(Request $request)
    {
        // Obtener filtros y paginación
        $title = $request->query('title', '');
        $price = $request->query('price', '');
        $date = $request->query('date', '');
        $page = $request->query('page', 1);
        $perPage = $request->query('per_page', 5);

        // Filtrar los productos
        $products = Product::all();
        if (!empty($title)) {
            $products = array_filter($products, function ($product) use ($title) {
                return stripos($product['title'], $title) !== false;
            });
        }
        if (!empty($price)) {
            $products = array_filter($products, function ($product) use ($price) {
                return $product['price'] == $price;
            });
        }
        if (!empty($date)) {
            $products = array_filter($products, function ($product) use ($date) {
                return strpos($product['created_at'], $date) !== false;
            });
        }

        // Paginación
        $total = count($products);
        $totalPages = ceil($total / $perPage);
        $products = array_slice($products, ($page - 1) * $perPage, $perPage);

        return response()->json([
            'products' => $products,
            'totalPages' => $totalPages,
        ]);
    }

    /**
     * Crear producto.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'price' => 'required|numeric|min:0',
            ]);
            // Crear el producto
            $product = Product::create($validated);

            return response()->json($product, 201); // Devuelve el producto creado con código 201 (creado)
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al procesar la solicitud' . $e->getMessage()], 500);
        }
    }

    /**
     * Actualizar producto.
     */
    public function update(Request $request, string $id)
    {
        try {
            // Validar los datos de entrada
            $validated = $request->validate([
                'title' => 'required|string|max:100',
                'price' => 'required|numeric|min:0',
            ]);

            // Buscar el producto por ID
            $product = Product::find($id);

            if (!$product) {
                return response()->json(['message' => 'Producto no encontrado'], 404);
            }

            // Actualizar el producto
            Product::update($id, $validated);

            return response()->json(['message' => 'Producto actualizado'], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Capturar errores de validación
            return response()->json(['message' => 'Error de validación', 'errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Capturar cualquier otro error
            return response()->json(['message' => 'Error al procesar la solicitud: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Eliminar producto.
     */
    public function destroy(string $id)
    {
        Product::delete($id);
        return response()->json(['message' => 'Producto eliminado']);
    }
}
