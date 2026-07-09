<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Repositories\Contracts\OrderRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function __construct(
        private Cart $cart,
        private ProductRepositoryInterface $products,
        private OrderRepositoryInterface $orders,
    ) {}

    public function create(Request $request): RedirectResponse|Response
    {
        if ($this->cart->count() === 0) {
            return redirect()->route('products.index')->with('error', 'Gio hang dang trong.');
        }

        $user = $request->user();

        return Inertia::render('Checkout/Create', [
            'lines' => $this->cart->lines(),
            'total' => $this->cart->total(),
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
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'shipping_address' => ['required', 'string', 'max:1000'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $lines = $this->cart->productLines();

        if ($lines->isEmpty()) {
            return redirect()->route('products.index')->with('error', 'Gio hang dang trong.');
        }

        $order = DB::transaction(function () use ($request, $validated, $lines): Order {
            $total = 0;
            $preparedLines = [];

            foreach ($lines as $line) {
                $product = $this->products->findForUpdate($line['product']->id);

                if (! $product->is_active || $product->stock < $line['quantity']) {
                    throw ValidationException::withMessages([
                        'cart' => "San pham {$product->name} khong du so luong ton kho.",
                    ]);
                }

                $subtotal = (float) $product->price * $line['quantity'];
                $total += $subtotal;

                $preparedLines[] = [
                    'product' => $product,
                    'quantity' => $line['quantity'],
                    'subtotal' => $subtotal,
                ];
            }

            $order = $this->orders->create([
                'user_id' => $request->user()->id,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'note' => $validated['note'] ?? null,
                'status' => Order::STATUS_PENDING,
                'total_amount' => $total,
            ]);

            foreach ($preparedLines as $line) {
                $this->orders->createItem($order, [
                    'product_id' => $line['product']->id,
                    'product_name' => $line['product']->name,
                    'price' => $line['product']->price,
                    'quantity' => $line['quantity'],
                    'subtotal' => $line['subtotal'],
                ]);

                $this->products->decrementStock($line['product'], $line['quantity']);
            }

            return $order;
        });

        $this->cart->clear();

        return redirect()->route('checkout.success', $order)->with('success', 'Dat hang thanh cong.');
    }

    public function success(Request $request, Order $order): Response
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order = $this->orders->loadItems($order);

        return Inertia::render('Checkout/Success', [
            'order' => [
                'id' => $order->id,
                'status' => $order->status,
                'total_amount' => (float) $order->total_amount,
                'items' => $order->items->map(fn ($item): array => [
                    'product_name' => $item->product_name,
                    'price' => (float) $item->price,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ]),
            ],
        ]);
    }
}
