<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderService
{
    public function all()
    {
        return Order::with('items.product')->get();
    }

    public function store(array $data)
    {
        return DB::transaction(function () use ($data) {
            $productIds = collect($data['items'])->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);
                if (!$product || $product->stock < $item['quantity']) {
                    abort(422, "Product out of stock");
                }
            }

            $data['reference'] = 'ORD-' . Str::upper(Str::random(8));

            $totalAmount = 0;
            $items = [];

            foreach ($data['items'] as $item) {
                $product = $products->get($item['product_id']);
                $subtotal = $product->price * $item['quantity'];
                $totalAmount += $subtotal;

                $items[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $product->price,
                    'subtotal' => $subtotal,
                ];

                $product->decrement('stock', $item['quantity']);
            }

            $data['total_amount'] = $totalAmount;

            $order = Order::create($data);
            $order->items()->saveMany($items);

            return $order->load('items.product');
        });
    }

    public function find(string $id)
    {
        return Order::with('items.product')->findOrFail($id);
    }
}
