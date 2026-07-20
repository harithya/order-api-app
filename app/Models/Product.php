<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
    ];


    // set the slug attribute automatically when creating a product
    protected static function booted(): void
    {
        static::creating(fn(Product $product) => self::generateSlug($product));
        static::updating(fn(Product $product) => self::generateSlug($product));
    }

    protected static function generateSlug(Product $product): void
    {
        $product->slug = Str::slug($product->name) . '-' . Str::random(5);
    }
}
