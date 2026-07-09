<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Inertia\Inertia;
use Inertia\Response;

class ShopController extends Controller
{
    public function __construct(private ProductRepositoryInterface $products) {}

    public function home(): Response
    {
        return Inertia::render('Shop/Home', [
            'featuredProducts' => $this->products
                ->latestActive(4)
                ->map(fn (Product $product): array => $this->productData($product)),
        ]);
    }

    public function index(): Response
    {
        return Inertia::render('Shop/Products', [
            'products' => $this->products
                ->paginateActiveLatest(9)
                ->through(fn (Product $product): array => $this->productData($product)),
        ]);
    }

    public function show(Product $product): Response
    {
        abort_unless($product->is_active, 404);

        return Inertia::render('Shop/ProductDetail', [
            'product' => $this->productData($product),
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function productData(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'price' => (float) $product->price,
            'stock' => $product->stock,
            'image_url' => $product->image_url,
            'is_active' => $product->is_active,
        ];
    }
}
