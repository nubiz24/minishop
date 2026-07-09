<script setup lang="ts">
import ProductCard from '@/components/ProductCard.vue';
import type { Paginated, Product } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    products: Paginated<Product>;
}>();
</script>

<template>
    <Head title="San pham" />

    <div class="mb-6">
        <h1 class="text-3xl font-bold">Danh sach san pham</h1>
        <p class="mt-2 text-zinc-600">Chon san pham, xem chi tiet va them vao gio hang.</p>
    </div>

    <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
        <ProductCard v-for="product in products.data" :key="product.id" :product="product" />
    </div>

    <nav v-if="products.last_page > 1" class="mt-8 flex flex-wrap gap-2">
        <Link
            v-for="link in products.links"
            :key="link.label"
            :href="link.url || '#'"
            :class="[
                'rounded-md border px-3 py-2 text-sm',
                link.active ? 'border-zinc-950 bg-zinc-950 text-white' : 'border-zinc-300 bg-white text-zinc-700',
                !link.url ? 'pointer-events-none opacity-50' : 'hover:bg-zinc-100',
            ]"
            v-html="link.label"
        />
    </nav>
</template>
