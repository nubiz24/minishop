<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use App\Http\Support\Cart;
use App\Models\Product;

class CartController extends Controller
{
    public function show(): Response
    {
        return Inertia::render('Cart/Show', [
            'lines' => Cart::lines(),
            'total' => Cart::total(),
        ]);
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        if (! $product->is_active || $product->stock <= 0) {
            return back()->with('error', 'Khong du so luong san pham trong kho');
        }

        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1', 'max:' . $product->stock],
        ]);

        Cart::add($product, $validated['quantity']);
        return back()->with('success', 'San pham da duoc them vao gio hang');
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:' . $product->stock],
        ]);

        Cart::update($product, $validated['quantity']);

        return back()->with('success', 'So luong san pham da duoc cap nhat');
    }

    public function destroy(Product $product): RedirectResponse
    {
        Cart::remove($product);

        return back()->with('success', 'San pham da duoc xoa khoi gio hang');
    }

    public function clear(): RedirectResponse
    {
        Cart::clear();

        return back()->with('success', 'Gio hang da duoc lam rong');
    }
}
