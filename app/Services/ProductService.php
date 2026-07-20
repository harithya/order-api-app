<?php

namespace App\Services;

use App\Models\Product;

class ProductService
{
    public function all()
    {
        return Product::all();
    }

    public function store(array $product)
    {
        return Product::create($product);
    }

    public function find(string $id)
    {
        return Product::findOrFail($id);
    }

    public function update(array $product, string $id)
    {
        $find =  $this->find($id);
        $find->update($product);

        return $find;
    }

    public function delete(string $id)
    {
        return $this->find($id)->delete();
    }
}
