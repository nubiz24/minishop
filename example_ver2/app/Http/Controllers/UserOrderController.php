<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserOrderController extends Controller
{
    public function __construct(private OrderRepositoryInterface $orders) {}

    public function index(Request $request): Response
    {
        return Inertia::render('Orders/Index', [
            'orders' => $this->orders
                ->paginateForUser($request->user(), 10)
                ->through(fn (Order $order): array => [
                    'id' => $order->id,
                    'status' => $order->status,
                    'status_label' => Order::statuses()[$order->status] ?? $order->status,
                    'total_amount' => (float) $order->total_amount,
                    'created_at' => $order->created_at?->format('d/m/Y H:i'),
                    'items_count' => (int) ($order->getAttribute('items_quantity_sum') ?? 0),
                ]),
        ]);
    }

    public function show(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order = $this->orders->loadItems($order);

        return Inertia::render('Orders/Show', [
            'order' => [
                'id' => $order->id,
                'customer_name' => $order->customer_name,
                'customer_email' => $order->customer_email,
                'customer_phone' => $order->customer_phone,
                'shipping_address' => $order->shipping_address,
                'note' => $order->note,
                'status' => $order->status,
                'status_label' => Order::statuses()[$order->status] ?? $order->status,
                'total_amount' => (float) $order->total_amount,
                'created_at' => $order->created_at?->format('d/m/Y H:i'),
                'items' => $order->items->map(fn ($item): array => [
                    'id' => $item->id,
                    'product_name' => $item->product_name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]),
            ],
        ]);
    }
}
