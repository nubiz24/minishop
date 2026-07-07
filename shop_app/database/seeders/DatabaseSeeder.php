<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::query()->UpdateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        $user = User::query()->updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User',
                'password' => Hash::make('password'),
                'role' => 'user',
                'email_verified_at' => now(),
            ],
        );

        $products = collect([
            [
                'name' => 'Binh giu nhiet mini',
                'description' => 'Binh nho gon cho hoc tap va lam viec hang ngay.',
                'price' => 159000,
                'stock' => 25,
                'image_url' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'So tay ke hoach',
                'description' => 'So bia cung, giay day, phu hop ghi chep cong viec.',
                'price' => 89000,
                'stock' => 40,
                'image_url' => 'https://images.unsplash.com/photo-1517842645767-c639042777db?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Den ban led',
                'description' => 'Den hoc tap anh sang diu, co the gap gon.',
                'price' => 329000,
                'stock' => 12,
                'image_url' => 'https://images.unsplash.com/photo-1507473885765-e6ed057f782c?auto=format&fit=crop&w=900&q=80',
            ],
            [
                'name' => 'Tai nghe bluetooth',
                'description' => 'Tai nghe khong day don gian cho cuoc goi va am nhac.',
                'price' => 499000,
                'stock' => 18,
                'image_url' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?auto=format&fit=crop&w=900&q=80',
            ],
        ])->map(function (array $data): Product {
            return Product::query()->updateOrCreate(
                ['slug' => Str::slug($data['name'])],
                $data + ['is_active' => true]
            );
        });

        $firstProduct = $products->first();

        if ($firstProduct && ! Order::query()->where('customer-email', $user->email)->exists()) {
            $order = Order::query()->create([
                'user_id' => $user->id,
                'customer_name' => $user->name,
                'customer_email' => $user->email,
                'customer_phone' => '0123456789',
                'shipping_address' => '123 Main St, City, Country',
                'note' => 'Please deliver between 9 AM and 5 PM.',
                'status' => Order::STATUS_PENDING,
                'total_amount' => $firstProduct->price,
            ]);

            $order->items()->create([
                'product_id' => $firstProduct->id,
                'product_name' => $firstProduct->name,
                'price' => $firstProduct->price,
                'quantity' => 1,
                'subtotal' => $firstProduct->price,
            ]);
        }
    }
}
