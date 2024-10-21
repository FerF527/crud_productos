<?php

namespace Tests;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Support\Facades\Storage;

class TestModel extends TestCase
{
    /**
     * Prueba para crear un producto.
     */
    public function test_create_product()
    {
        // Simular almacenamiento
        Storage::fake('local');

        // Datos de prueba
        $data = [
            'title' => 'Producto de prueba',
            'price' => 150,
        ];

        // Llamar a la función de crear producto
        $product = Product::create($data);

        // Verificar que el producto se haya guardado en el archivo
        Storage::disk('local')->assertExists('product.json');

        // Verificar los datos del producto
        $this->assertEquals('Producto de prueba', $product['title']);
        $this->assertEquals(150, $product['price']);
    }

    /**
     * Prueba para editar un producto.
     */
    public function test_edit_product()
    {
        // Simular almacenamiento
        Storage::fake('local');
        $products = [
            ['id' => 1, 'title' => 'Producto 1', 'price' => 100],
            ['id' => 2, 'title' => 'Producto 2', 'price' => 200],
        ];
        Storage::disk('local')->put('product.json', json_encode($products));

        // Datos de prueba
        $productData = [
            'title' => 'Producto de prueba',
            'price' => 300,
        ];

        // Llamar a la función de editar producto
        Product::update(2,$productData);

        // Buscar el producto
        $product = Product::find(2);

        // Verificar que el producto se actualizo correctamente
        $this->assertNotNull($product);
        $this->assertEquals(2, $product['id']);
        $this->assertEquals('Producto de prueba', $product['title']);
        $this->assertEquals(300, $product['price']);

    }

    /**
     * Prueba para eliminar un producto.
     */
    public function test_delete_product()
    {
        // Simular almacenamiento con un producto
        Storage::fake('local');
        $products = [
            ['id' => 1, 'title' => 'Producto 1', 'price' => 100],
            ['id' => 2, 'title' => 'Producto 2', 'price' => 200],
        ];
        Storage::disk('local')->put('product.json', json_encode($products));

        // Ejecutar la eliminación
        Product::delete(2);

        // Verificar que el archivo fue actualizado sin el producto eliminado
        $newProducts = json_decode(Storage::disk('local')->get('product.json'), true);
        $this->assertCount(1, $newProducts);
        $this->assertEquals(1, $newProducts[0]['id']);
    }

    /**
     * Prueba para obtener un producto por ID.
     */
    public function test_find_product_by_id()
    {
        // Simular almacenamiento con un producto
        Storage::fake('local');
        $products = [
            ['id' => 1, 'title' => 'Producto 1', 'price' => 100],
            ['id' => 2, 'title' => 'Producto 2', 'price' => 200],
        ];
        Storage::disk('local')->put('product.json', json_encode($products));

        // Buscar el producto
        $product = Product::find(2);

        // Verificar que el producto encontrado sea el correcto
        $this->assertNotNull($product);
        $this->assertEquals(2, $product['id']);
        $this->assertEquals('Producto 2', $product['title']);
    }

    /**
     * Prueba para obtener todos los productos.
     */
    public function test_products()
    {
        // Simular almacenamiento con un producto
        Storage::fake('local');
        $products = [
            ['id' => 1, 'title' => 'Producto 1', 'price' => 300],
            ['id' => 2, 'title' => 'Producto 2', 'price' => 500],
        ];
        Storage::disk('local')->put('product.json', json_encode($products));

        // Buscar los productos
        $list_product = Product::all();

        // Verificar que el listado de productos no sea nulo.
        $this->assertNotNull($list_product);

        //producto 1
        $this->assertEquals(1, $list_product[0]['id']);
        $this->assertEquals('Producto 1', $list_product[0]['title']);
        $this->assertEquals(300, $list_product[0]['price']);
        //producto 2
        $this->assertEquals(2, $list_product[1]['id']);
        $this->assertEquals('Producto 2', $list_product[1]['title']);
        $this->assertEquals(500, $list_product[1]['price']);
    }
}
