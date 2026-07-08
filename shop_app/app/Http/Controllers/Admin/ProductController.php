<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Products/Index', [
            'products' => Product::query()
                ->latest()
                ->paginate(10)
                ->through(fn(Product $product): array => $this->productData($product)),
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

        Product::query()->create($validated);

        return redirect()->route('admin.products.index')->with('success', 'San pham da duoc tao thanh cong');
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
            : $this->uniqueSlug($validated['name']);
        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'San pham da duoc cap nhat thanh cong');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

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

        while (Product::query()
            ->where('slug', $slug)
            ->when($ignoreProductId, fn($query) => $query->whereKeyNot($ignoreProductId))
            ->exists()
        ) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
