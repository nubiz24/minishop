<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function __construct(private ProductRepositoryInterface $products) {}

    public function index(): Response
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => $this->products
                ->paginateLatest(10)
                ->through(fn (Product $product): array => $this->productData($product)),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $validated['slug'] = $this->uniqueSlug($validated['name']);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $this->products->create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Da them san pham.');
    }

    public function edit(Product $product): Response
    {
        return Inertia::render('Admin/Products/Form', [
            'product' => $this->productData($product),
        ]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $this->validateProduct($request);
        $validated['slug'] = $product->name === $validated['name']
            ? $product->slug
            : $this->uniqueSlug($validated['name'], $product->id);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $this->products->update($product, $validated);

        return redirect()->route('admin.products.index')->with('success', 'Da cap nhat san pham.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->products->delete($product);

        return back()->with('success', 'Da xoa san pham.');
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
            'created_at' => $product->created_at?->format('d/m/Y'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image_url' => ['nullable', 'url', 'max:1000'],
            'is_active' => ['boolean'],
        ]);
    }

    private function uniqueSlug(string $name, ?int $ignoreProductId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $count = 2;

        while ($this->products->slugExists($slug, $ignoreProductId)) {
            $slug = $baseSlug.'-'.$count;
            $count++;
        }

        return $slug;
    }
}
