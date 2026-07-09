<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import type { Product } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    featuredProducts: Product[];
}>();
</script>

<template>
    <Head title="Trang chu" />

    <section class="grid gap-8 lg:grid-cols-[1.1fr_0.9fr] lg:items-center">
        <div class="space-y-5">
            <p class="text-sm font-medium uppercase tracking-wide text-emerald-700">Laravel + Vue/Inertia + PostgreSQL</p>
            <h1 class="max-w-2xl text-4xl font-bold tracking-tight text-zinc-950 sm:text-5xl">
                Mini Shop
            </h1>
            <p class="max-w-2xl text-lg text-zinc-650">
                Website ban hang nho voi dang ky, dang nhap, danh sach san pham, gio hang, dat hang va trang quan tri.
            </p>
            <div class="flex flex-wrap gap-3">
                <Link href="/products" class="rounded-md bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800">
                    Xem san pham
                </Link>
                <Link href="/cart" class="rounded-md border border-zinc-300 px-4 py-2.5 text-sm font-semibold text-zinc-800 hover:bg-white">
                    Gio hang
                </Link>
            </div>
        </div>

        <img
            src="https://images.unsplash.com/photo-1556742502-ec7c0e9f34b1?auto=format&fit=crop&w=1200&q=80"
            alt="Cua hang truc tuyen"
            class="h-80 w-full rounded-lg object-cover shadow-sm"
        />
    </section>

    <section class="mt-12">
        <div class="mb-5 flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-semibold">San pham moi</h2>
                <p class="text-sm text-zinc-600">Du lieu mau duoc tao tu seeder.</p>
            </div>
            <Link href="/products" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">Xem tat ca</Link>
        </div>

        <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <ProductCard v-for="product in featuredProducts" :key="product.id" :product="product" />
        </div>
    </section>
</template>
