<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Response;
use Inertia\Inertia;
use App\Http\Support\Cart;
use Illuminate\Validation\ValidationException;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function create(Request $request): RedirectResponse|Response
    {
        if (Cart::count() === 0) {
            return back()->with('error', 'Gio hang cua ban dang trong');
        }

        $user = $request->user();

        return Inertia::render('Checkout/Create', [
            'lines' => Cart::lines(),
            'total' => Cart::total(),
            'customer' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_address' => ['required', 'string', 'max:255'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $lines = Cart::productlines();

        if ($lines->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Gio hang cua ban dang trong');
        }

        $order = DB::transaction(function () use ($request, $validated, $lines): Order {
            $total = 0;
            $prepareLines = [];

            foreach ($lines as $line) {
                $product = Product::query()
                    ->whereKey($line['product']->id)
                    ->lockforUpdate()
                    ->firstOrFail();

                if (! $product->is_active || $product->stock < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "San pham {$product->name} da het hoac khong kha dung",
                    ]);
                }

                $subtotal = (float) $product->price * $line['quantity'];
                $total += $subtotal;

                $prepareLines[] = [
                    'product' => $product,
                    'quantity' => $line['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            $order = Order::query()->create([
                'user_id' => $request->user()->id,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_address' => $validated['customer_address'],
                'shipping_address' => $validated['shipping_address'],
                'note' => $validated['note'] ?? null,
                'status' => Order::STATUS_PENDING,
                'total_amount' => $total,
            ]);

            foreach ($prepareLines as $line) {
                $order->items()->create([
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['subtotal'],
                ]);

                $line['product']->decrement('stock', $line['quantity']);
            }

            return $order;
        });

        Cart::clear();

        return redirect()->route('orders.show', $order)->with('success', 'Don hang cua ban da duoc tao thanh cong');
    }

    public function success(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load('items');

        return Inertia::render('Checkout/Success', [
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'total_amount' => $order->total_amount,
                'items' => $order->items->map(fn($item): array => [
                    'product_name' => $item->product_name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]),
            ],
        ]);
    }
}
