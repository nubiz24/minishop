<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Orders/Index', [
            'orders' => Order::query()
                ->with('user')
                ->latest()
                ->paginate(10)
                ->through(fn(Order $order): array => [
                    'id' => $order->id,
                    'customer_name' => $order->customer_name,
                    'customer_email' => $order->customer_email,
                    'status' => $order->status,
                    'total_amount' => (float) $order->total_amount,
                    'created_at' => $order->created_at?->format('d/m/Y H:i'),
                    'user' => $order->user ? [
                        'id' => $order->user->id,
                        'name' => $order->user->name,
                    ] : null,
                ]),
            'statuses' => Order::statuses(),
        ]);
    }

    public function show(Order $order): Response
    {
        $order->load(['items', 'user']);

        return Inertia::render('Admin/Orders/Show', [
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'note' => $order->note,
                'status' => $order->status,
                'total_amount' => (float) $order->total_amount,
                'created_at' => $order->created_at?->format('d/m/Y H:i'),
                'user' => $order->user ? [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ] : null,
                'items' => $order->items->map(fn($item): array => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]),
            ],
            'statuses' => Order::statuses(),
        ]);
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(array_keys(Order::statuses()))],
        ]);

        $order->update($validated);

        return back()->with('success', 'Da cap nhat trang thai don hang.');
    }
}
