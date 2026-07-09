<?php

namespace App\Repositories\Contracts;

use App\Models\Order;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface OrderRepositoryInterface
{
    public function paginateLatestWithUser(int $perPage): LengthAwarePaginator;

    public function paginateForUser(User $user, int $perPage): LengthAwarePaginator;

    /**
     * @param  array<string, mixed>  $data
     */
    public function create(array $data): Order;

    /**
     * @param  array<string, mixed>  $data
     */
    public function createItem(Order $order, array $data): void;

    public function loadItems(Order $order): Order;

    public function loadForAdmin(Order $order): Order;

    public function updateStatus(Order $order, string $status): void;
}
