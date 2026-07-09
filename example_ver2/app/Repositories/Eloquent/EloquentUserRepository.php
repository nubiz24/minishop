<?php

namespace App\Repositories\Eloquent;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{
    public function paginateLatestWithOrdersCount(int $perPage): LengthAwarePaginator
    {
        return User::query()
            ->withCount('orders')
            ->latest()
            ->paginate($perPage);
    }

    public function update(User $user, array $data): void
    {
        $user->update($data);
    }

    public function delete(User $user): void
    {
        $user->delete();
    }
}
