<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Support\Cart;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function __construct(private Cart $cart) {}

    public function show(): Response
    {
        return Inertia::render('Cart/Show', [
            'lines' => $this->cart->lines(),
            'total' => $this->cart->total(),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        if (! $product->is_active || $product->stock < 1) {
            return back()->with('error', 'San pham nay hien khong the mua.');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:'.$product->stock],
        ]);

        $this->cart->add($product, $validated['quantity']);

        return back()->with('success', 'Da them san pham vao gio hang.');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:'.$product->stock],
        ]);

        $this->cart->update($product, $validated['quantity']);

        return back()->with('success', 'Gio hang da duoc cap nhat.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->cart->remove($product);

        return back()->with('success', 'Da xoa san pham khoi gio hang.');
    }

    public function clear(): RedirectResponse
    {
        $this->cart->clear();

        return back()->with('success', 'Da xoa tat ca san pham trong gio hang.');
    }
}
