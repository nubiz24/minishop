<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface ProductRepositoryInterface
{
    /**
     * @return Collection<int, Product>
     */
    public function latestActive(int $limit): Collection;

    public function paginateActiveLatest(int $perPage): LengthAwarePaginator;

    public function paginateLatest(int $perPage): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Product;

    /**
     * @param  array<string, mixed>  $data
     */
    public function update(Product $product, array $data): void;

    public function delete(Product $product): void;

    public function slugExists(string $slug, ?int $ignoreProductId = null): bool;

    /**
     * @param  array<int, int|string>  $ids
     * @return Collection<int, Product>
     */
    public function findManyByIds(array $ids): Collection;

    public function findForUpdate(int $id): Product;

    public function decrementStock(Product $product, int $quantity): void;
}
