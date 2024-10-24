<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Illuminate\Support\Facades\Session;

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

    /**
     * Prueba para crear un producto.
     */
    public function test_store()
    {
        // Simular almacenamiento
        Storage::fake('local');
        Storage::disk('local')->put('product.json', json_encode([]));

        // Iniciar una sesión para generar un token CSRF
        Session::start();

        // Datos del producto a crear
        $data = [
            '_token' => csrf_token(), // Añadir el token CSRF
            'title' => 'Nuevo Producto',
            'price' => 900,
        ];

        // Hacer una solicitud POST al endpoint de creación
        $response = $this->post('/product', $data);

        // Verificar que el producto fue creado correctamente
        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Nuevo Producto',
                'price' => 900,
            ]);

        // Verificar que se guardó en el archivo
        $products = json_decode(Storage::disk('local')->get('product.json'), true);
        $this->assertCount(1, $products);
        $this->assertEquals('Nuevo Producto', $products[0]['title']);
        $this->assertEquals(900, $products[0]['price']);
    }

    /**
     * Prueba para actualizar un producto.
     */
    public function test_update()
    {
        // Simular almacenamiento
        Storage::fake('local');
        $products = [
            ['id' => 1, 'title' => 'Producto de prueba', 'price' => 1150, 'created_at' => now()],
        ];
        Storage::disk('local')->put('product.json', json_encode($products));

        // Iniciar una sesión para generar un token CSRF
        Session::start();

        // Datos de actualización
        $data = [
            '_token' => csrf_token(), // Añadir el token CSRF
            'title' => 'Producto Actualizado',
            'price' => 200,
        ];

        // Hacer una solicitud PUT al endpoint de actualización
        $response = $this->put('/product/1', $data);

        // Verificar que el producto fue actualizado correctamente
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Producto actualizado',
            ]);

        // Verificar que se guardó en el archivo
        $updatedProducts = json_decode(Storage::disk('local')->get('product.json'), true);
        $this->assertEquals('Producto Actualizado', $updatedProducts[0]['title']);
        $this->assertEquals(200, $updatedProducts[0]['price']);
    }

    /**
     * Prueba para eliminar un producto.
     */
    public function test_destroy()
    {
        // Simular almacenamiento
        Storage::fake('local');
        $products = [
            ['id' => 1, 'title' => 'Producto de prueba', 'price' => 100, 'created_at' => now()],
        ];
        Storage::disk('local')->put('product.json', json_encode($products));

        // Iniciar una sesión para generar un token CSRF
        Session::start();

        // Añadir el token CSRF como parte del encabezado
        $response = $this->delete('/product/1', [
            '_token' => csrf_token(),
        ]);

        // Verificar que el producto fue eliminado correctamente
        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Producto eliminado',
            ]);

        // Verificar que el producto fue removido del archivo
        $remainingProducts = json_decode(Storage::disk('local')->get('product.json'), true);
        $this->assertEmpty($remainingProducts);
    }
}
