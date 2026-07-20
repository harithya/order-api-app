<?php

use App\Models\Order;
use App\Models\Product;

test('can retrieve all orders', function () {
    Order::factory()
        ->hasItems(2)
        ->count(3)
        ->create();

    $response = $this->get('/api/order');

    $response->assertStatus(200)
        ->assertJsonCount(3, 'data');
});

test('can retrieve a single order', function () {
    $order = Order::factory()
        ->hasItems(2)
        ->create();

    $response = $this->get("/api/order/{$order->id}");

    $response->assertStatus(200)
        ->assertJson([
            'data' => [
                'id' => $order->id,
            ],
        ]);
});

test('can create an order', function () {
    $product = Product::factory()->create(['price' => 10000, 'stock' => 10]);

    $orderData = [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '089777188828',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 2],
        ],
    ];

    $response = $this->postJson('/api/order', $orderData);

    $response->assertStatus(201)
        ->assertJson([
            'data' => [
                'customer_name' => 'John Doe',
                'total_amount' => 20000,
            ],
        ]);

    $this->assertDatabaseHas('orders', [
        'customer_email' => 'john@example.com',
        'total_amount' => 20000,
    ]);

    $this->assertDatabaseHas('order_items', [
        'product_id' => $product->id,
        'quantity' => 2,
        'price' => 10000,
        'subtotal' => 20000,
    ]);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 8,
    ]);
});

test('validation errors when creating order with invalid data', function () {
    $response = $this->postJson('/api/order', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['customer_name', 'customer_email', 'customer_phone', 'items']);
});

test('returns 422 when product out of stock', function () {
    $product = Product::factory()->create(['stock' => 1]);

    $orderData = [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '089777188828',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 5],
        ],
    ];

    $response = $this->postJson('/api/order', $orderData);

    $response->assertStatus(422)
        ->assertJson(['message' => 'Product out of stock']);
});

test('returns 404 when order not found', function () {
    $response = $this->getJson('/api/order/999');

    $response->assertStatus(404)
        ->assertJson(['message' => 'Data not found']);
});

test('stock cannot go negative when two orders compete for the last item', function () {
    $product = Product::factory()->create(['stock' => 1]);

    // First order - succeeds, takes the last stock
    $response1 = $this->postJson('/api/order', [
        'customer_name' => 'John Doe',
        'customer_email' => 'john@example.com',
        'customer_phone' => '089777188828',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ]);

    $response1->assertStatus(201);

    // Second order - stock already gone, should fail
    $response2 = $this->postJson('/api/order', [
        'customer_name' => 'Jane Doe',
        'customer_email' => 'jane@example.com',
        'customer_phone' => '089777188829',
        'items' => [
            ['product_id' => $product->id, 'quantity' => 1],
        ],
    ]);

    $response2->assertStatus(422)
        ->assertJson(['message' => 'Product out of stock']);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'stock' => 0,
    ]);

    $this->assertDatabaseCount('orders', 1);
});
