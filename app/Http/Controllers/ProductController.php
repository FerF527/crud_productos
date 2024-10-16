<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Listado de productos.
     */
    public function index()
    {
        return response()->json(Product::all());
    }

    /**
     * Crear producto.
     */
    public function store(Request $request)
    {
        try {
            // Validar los datos de entrada
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'price' => 'required|numeric|min:0'
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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        Product::update($id, $validated);

        return response()->json(['message' => 'Producto actualizado']);
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
