<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Product
{
    private static $disk = "local";
    private static $file = "product.json";
    /**
     * Listado de productos.
     */
    public static function all()
    {
        if (!Storage::disk(self::$disk)->exists(self::$file)) {
            Storage::disk(self::$disk)->put(self::$file,"[]");
        }
        return Storage::disk(self::$disk)->json(self::$file);
    }

    /**
     * Eliminar producto.
     */
    public static function delete($id)
    {
        $products = self::all();
        $deleted_product = [];
        
        foreach ($products as $index => $product) {
            if ($product['id'] == $id) {
                $deleted_product = $product;
                unset($products[$index]);
                break;
            }
        }

        if (!$deleted_product) {
            throw new \Exception("Producto no encontrado");
        }
    
        Storage::disk(self::$disk)->put(self::$file,json_encode($products, JSON_PRETTY_PRINT));
        // Registrar la acción en los logs
        Log::channel('product_logs')->info('Producto eliminado', [
            'id' => $deleted_product['id'],
            'title' => $deleted_product['title'],
            'price' => $deleted_product['price'],
            'deleted_at' => Carbon::now()->toDateTimeString()
        ]);
    }

    /**
     * Crear producto.
     */
    public static function create($data)
    {
        $products = self::all();
        
        //validar si existe un producto en el archivo
        if ($products == null) {
            $data['id'] = 1;
        }else {
            $data['id'] = end($products)['id'] + 1;
        }
        $data['created_at'] = now()->format('Y-m-d H:i');
        $products[] = $data;
        Storage::disk(self::$disk)->put(self::$file,json_encode($products, JSON_PRETTY_PRINT));
        // Registrar la acción en los logs
        Log::channel('product_logs')->info('Producto agregado', [
            'id' => $data['id'],
            'title' => $data['title'],
            'price' => $data['price'],
            'created_at' => $data['created_at']
        ]);
        return $data;
    }

    /**
     * Producto por id.
     */
    public static function find($id)
    {
        $products = self::all();
        return collect($products)->firstWhere('id', $id);
    }

    /**
     * Actualizar producto.
     */
    public static function update($id, $data)
    {
        $products = self::all();
        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product = array_merge($product, $data);
            }
        }
        Storage::disk(self::$disk)->put(self::$file,json_encode($products, JSON_PRETTY_PRINT));
        // Registrar la acción en los logs
        Log::channel('product_logs')->info('Producto actualizado', [
            'id' => intval($id),
            'title' => $data['title'],
            'price' => $data['price'],
            'updated_at'=> Carbon::now()->toDateTimeString()
        ]);
    }

}
