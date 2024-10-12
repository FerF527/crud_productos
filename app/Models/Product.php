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

}
