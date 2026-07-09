<script setup lang="ts">
import type { Product } from '@/types';
import { Link } from '@inertiajs/vue3';

defineProps<{
    product: Product;
}>();

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);
</script>

<template>
    <article class="overflow-hidden rounded-lg border border-zinc-200 bg-white shadow-sm">
        <img
            :src="product.image_url || 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=900&q=80'"
            :alt="product.name"
            class="h-44 w-full object-cover"
        />
        <div class="space-y-3 p-4">
            <div>
                <h3 class="line-clamp-2 min-h-12 text-base font-semibold text-zinc-950">{{ product.name }}</h3>
                <p class="mt-1 text-sm text-zinc-600">Con {{ product.stock }} san pham</p>
            </div>
            <div class="flex items-center justify-between gap-3">
                <p class="font-semibold text-emerald-700">{{ formatMoney(product.price) }}</p>
                <Link
                    :href="`/products/${product.slug}`"
                    class="rounded-md bg-zinc-950 px-3 py-2 text-sm font-medium text-white hover:bg-zinc-800"
                >
                    Chi tiet
                </Link>
            </div>
        </div>
    </article>
</template>
