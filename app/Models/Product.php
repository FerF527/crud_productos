<?php

namespace App\Models;
use Illuminate\Support\Facades\Storage;

class Product
{
    private static $disk = "local";
    private static $file = "product.json";

    public static function all()
    {
        if (!Storage::disk(self::$disk)->exists(self::$file)) {
            Storage::disk(self::$disk)->put(self::$file,"[]");
        }
        return Storage::disk(self::$disk)->json(self::$file);
    }

    public static function delete($id)
    {
        $products = self::all();
        $products = array_filter($products, function ($product) use ($id) {
            return $product['id'] != $id;
        });
        Storage::disk(self::$disk)->put(self::$file,json_encode($products, JSON_PRETTY_PRINT));
    }

    public static function create($data)
    {
        $products = self::all();
        $data['id'] = end($products)['id'] + 1;
        $data['created_at'] = now()->format('Y-m-d H:i');
        $products[] = $data;
        Storage::disk(self::$disk)->put(self::$file,json_encode($products, JSON_PRETTY_PRINT));
        return $data;
    }

    public static function find($id)
    {
        $products = self::all();
        return collect($products)->firstWhere('id', $id);
    }

    public static function update($id, $data)
    {
        $products = self::all();
        foreach ($products as &$product) {
            if ($product['id'] == $id) {
                $product = array_merge($product, $data);
            }
        }
        Storage::disk(self::$disk)->put(self::$file,json_encode($products, JSON_PRETTY_PRINT));
    }

}
