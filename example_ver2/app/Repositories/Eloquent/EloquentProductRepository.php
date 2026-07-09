<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function latestActive(int $limit): Collection
    {
        return Product::query()
            ->where('is_active', true)
            ->latest()
            ->limit($limit)
            ->get();
    }

    public function paginateActiveLatest(int $perPage): LengthAwarePaginator
    {
        return Product::query()
            ->where('is_active', true)
            ->latest()
            ->paginate($perPage);
    }

    public function paginateLatest(int $perPage): LengthAwarePaginator
    {
        return Product::query()
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Product
    {
        return Product::query()->create($data);
    }

    public function update(Product $product, array $data): void
    {
        $product->update($data);
    }

    public function delete(Product $product): void
    {
        $product->delete();
    }

    public function slugExists(string $slug, ?int $ignoreProductId = null): bool
    {
        return Product::query()
            ->where('slug', $slug)
            ->when($ignoreProductId, fn ($query) => $query->whereKeyNot($ignoreProductId))
            ->exists();
    }

    public function findManyByIds(array $ids): Collection
    {
        return Product::query()
            ->whereIn('id', $ids)
            ->get();
    }

    public function findForUpdate(int $id): Product
    {
        return Product::query()
            ->whereKey($id)
            ->lockForUpdate()
            ->firstOrFail();
    }

    public function decrementStock(Product $product, int $quantity): void
    {
        $product->decrement('stock', $quantity);
    }
}
