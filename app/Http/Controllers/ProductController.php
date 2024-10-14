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
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric|min:0'
        ]);

        return response()->json(Product::create($validated));
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
