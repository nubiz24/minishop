<?php

namespace App\Http\Support;

use App\Models\Product;
use Illuminate\Support\Collection;

class Cart
{
    /**
     * @return Collection<int, array{product: Product, quantity: int, subtotal: int}>
     */

    public static function productLines(): Collection
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        return collect($cart)
            ->map(function (int $quantity, int|string $productId) use ($products): ?array {
                $product = $products->get((int) $productId);

                if (!$product) {
                    return [];
                }

                return [
                    'product' => $product,
                    'quantity' => $quantity,
                    'subtotal' => (float) $product->price * $quantity,
                ];
            })
            ->filter()
            ->values();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public static function lines(): Collection
    {
        return self::productLines()->map(fn(array $line): array => [
            'product' => [
                'id' => $line['product']->id,
                'name' => $line['product']->name,
                'slug' => $line['product']->slug,
                'price' => (float) $line['product']->price,
                'stock' => $line['product']->stock,
                'image_url' => $line['product']->image_url,
            ],
            'quantity' => $line['quantity'],
            'subtotal' => $line['subtotal'],
        ]);
    }

    public static function total(): float
    {
        return self::productLines()->sum('subtotal');
    }

    public static function count(): int
    {
        return collect(session('cart', []))->sum();
    }

    public static function add(Product $product, int $quantity): void
    {
        $cart = session('cart', []);
        $currentQuantity = (int) $cart[$product->id] ?? 0;

        $cart[$product->id] = min($product->stock, $currentQuantity + $quantity);

        session(['cart' => $cart]);
    }

    public static function update(Product $product, int $quantity): void
    {
        $cart = session('cart', []);

        if ($quantity < 1) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($product->stock, $quantity);
        }

        session(['cart' => $cart]);
    }

    public static function remove(Product $product): void
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);
        session(['cart' => $cart]);
    }

    public static function clear(): void
    {
        session()->forget('cart');
    }
}
