<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Admin/Users/Index', [
            'users' => User::query()
                ->withCount('orders')
                ->latest()
                ->paginate(10)
                ->through(fn(User $user): array => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'orders_count' => $user->orders_count,
                    'created_at' => $user->created_at?->format('d/m/Y'),
                ]),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,customer'],
        ]);

        $user->update($validated);

        return back()->with('success', 'Thong tin nguoi dung da duoc cap nhat thanh cong');
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($request->user()->is($user)) {
            return back()->with('error', 'Ban khong the xoa chinh minh');
        }

        $user->delete();

        return back()->with('success', 'Nguoi dung da duoc xoa thanh cong');
    }
}
