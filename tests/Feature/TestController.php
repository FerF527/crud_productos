<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TestController extends TestCase
{
    /**
     * Prueba para el listado de productos.
     */
    public function test_index()
    {
        // Simular almacenamiento
        Storage::fake('local');
        $products = [
            ['id' => 1, 'title' => 'Producto 1', 'price' => 200, 'created_at' => now()],
            ['id' => 2, 'title' => 'Producto 2', 'price' => 400, 'created_at' => now()],
        ];
        Storage::disk('local')->put('product.json', json_encode($products));

        // Hacer una solicitud GET al endpoint de index
        $response = $this->get('/product');
        
        // Verificar la respuesta
        $response->assertStatus(200)
            ->assertJson([
                'products' => [
                    ['id' => 1, 'title' => 'Producto 1', 'price' => 200],
                    ['id' => 2, 'title' => 'Producto 2', 'price' => 400],
                ],
                'totalPages' => 1,
            ]);
    }
}
