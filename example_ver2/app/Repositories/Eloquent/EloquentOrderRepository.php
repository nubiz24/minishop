<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Models\User;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentOrderRepository implements OrderRepositoryInterface
{
    public function paginateLatestWithUser(int $perPage): LengthAwarePaginator
    {
        return Order::query()
            ->with('user')
            ->latest()
            ->paginate($perPage);
    }

    public function paginateForUser(User $user, int $perPage): LengthAwarePaginator
    {
        return Order::query()
            ->whereBelongsTo($user)
            ->withSum('items as items_quantity_sum', 'quantity')
            ->latest()
            ->paginate($perPage);
    }

    public function create(array $data): Order
    {
        return Order::query()->create($data);
    }

    public function createItem(Order $order, array $data): void
    {
        $order->items()->create($data);
    }

    public function loadItems(Order $order): Order
    {
        return $order->load('items');
    }

    public function loadForAdmin(Order $order): Order
    {
        return $order->load(['items', 'user']);
    }

    public function updateStatus(Order $order, string $status): void
    {
        $order->update(['status' => $status]);
    }
}
