<?php

namespace App\Support;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Support\Collection;

class Cart
{
    public function __construct(private ProductRepositoryInterface $products) {}

    /**
     * @return Collection<int, array{product: Product, quantity: int, subtotal: float}>
     */
    public function productLines(): Collection
    {
        $cart = session('cart', []);

        if ($cart === []) {
            return collect();
        }

        $products = $this->products
            ->findManyByIds(array_keys($cart))
            ->keyBy('id');

        return collect($cart)
            ->map(function (int $quantity, int|string $productId) use ($products): ?array {
                $product = $products->get((int) $productId);

                if (! $product) {
                    return null;
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
    public function lines(): Collection
    {
        return $this->productLines()->map(fn (array $line): array => [
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

    public function total(): float
    {
        return $this->productLines()->sum('subtotal');
    }

    public function count(): int
    {
        return (int) collect(session('cart', []))->sum();
    }

    public function add(Product $product, int $quantity): void
    {
        $cart = session('cart', []);
        $currentQuantity = (int) ($cart[$product->id] ?? 0);

        $cart[$product->id] = min($product->stock, $currentQuantity + $quantity);

        session(['cart' => $cart]);
    }

    public function update(Product $product, int $quantity): void
    {
        $cart = session('cart', []);

        if ($quantity < 1) {
            unset($cart[$product->id]);
        } else {
            $cart[$product->id] = min($product->stock, $quantity);
        }

        session(['cart' => $cart]);
    }

    public function remove(Product $product): void
    {
        $cart = session('cart', []);
        unset($cart[$product->id]);

        session(['cart' => $cart]);
    }

    public function clear(): void
    {
        session()->forget('cart');
    }
}
