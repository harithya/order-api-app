<?php

use App\Models\Product;

test('can retrieve all products', function () {

    Product::factory()->count(3)->create();

    $response = $this->get('/api/product');
    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('can retrieve a single product', function () {

    $product = Product::factory()->create();

    $response = $this->get("/api/product/{$product->id}");
    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $product->id,
            ],
        ]);
});

test('can create a product', function () {

    $productData = Product::factory()->raw();

    $response = $this->post('/api/product', $productData);
    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'name' => $productData['name'],
                'description' => $productData['description'],
            ],
        ]);
});

test('validation errors when invalid data', function () {

    $invalidData = Product::factory()->raw();
    unset($invalidData['name']);

    $response = $this->post('/api/product', $invalidData);
    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name']);
});


test('can update a product', function () {

    $product = Product::factory()->create();
    $updatedData = [
        ...$product->toArray(),
        'name' => 'Updated Product Name',
    ];

    $response = $this->put("/api/product/{$product->id}", $updatedData);
    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $product->id,
                'name' => 'Updated Product Name',
            ],
        ]);
});


test('can delete a product', function () {

    $product = Product::factory()->create();

    $response = $this->delete("/api/product/{$product->id}");
    $response->assertStatus(200);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});
