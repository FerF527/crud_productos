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
        Storage::disk(self::$disk)->put(self::$file,json_encode($products));
    }

}
