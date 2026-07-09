<script setup lang="ts">
import type { CartLine } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    lines: CartLine[];
    total: number;
}>();

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);

const updateQuantity = (line: CartLine, quantity: number) => {
    router.put(`/cart/${line.product.id}`, { quantity }, { preserveScroll: true });
};

const removeLine = (line: CartLine) => {
    router.delete(`/cart/${line.product.id}`, { preserveScroll: true });
};

const clearCart = () => {
    router.delete('/cart', { preserveScroll: true });
};
</script>

<template>
    <Head title="Gio hang" />

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold">Gio hang</h1>
            <p class="mt-2 text-zinc-600">Kiem tra san pham truoc khi dat hang.</p>
        </div>
        <button v-if="lines.length" class="rounded-md border border-zinc-300 px-3 py-2 text-sm hover:bg-white" @click="clearCart">
            Xoa gio hang
        </button>
    </div>

    <div v-if="!lines.length" class="rounded-lg border border-zinc-200 bg-white p-8 text-center">
        <p class="text-zinc-700">Gio hang dang trong.</p>
        <Link href="/products" class="mt-4 inline-flex rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">
            Mua san pham
        </Link>
    </div>

    <div v-else class="grid gap-6 lg:grid-cols-[1fr_320px]">
        <div class="space-y-3">
            <article v-for="line in lines" :key="line.product.id" class="grid gap-4 rounded-lg border border-zinc-200 bg-white p-4 sm:grid-cols-[96px_1fr_auto]">
                <img :src="line.product.image_url || ''" :alt="line.product.name" class="h-24 w-24 rounded-md object-cover" />
                <div>
                    <Link :href="`/products/${line.product.slug}`" class="font-semibold hover:text-emerald-700">{{ line.product.name }}</Link>
                    <p class="mt-1 text-sm text-zinc-600">{{ formatMoney(line.product.price) }}</p>
                    <div class="mt-3 flex items-center gap-2">
                        <label class="text-sm text-zinc-600" :for="`quantity-${line.product.id}`">So luong</label>
                        <input
                            :id="`quantity-${line.product.id}`"
                            type="number"
                            min="0"
                            :max="line.product.stock"
                            :value="line.quantity"
                            class="w-20 rounded-md border border-zinc-300 px-2 py-1"
                            @change="updateQuantity(line, Number(($event.target as HTMLInputElement).value))"
                        />
                    </div>
                </div>
                <div class="flex flex-col items-start justify-between gap-3 sm:items-end">
                    <p class="font-semibold">{{ formatMoney(line.subtotal) }}</p>
                    <button class="text-sm font-medium text-red-700 hover:text-red-800" @click="removeLine(line)">Xoa</button>
                </div>
            </article>
        </div>

        <aside class="h-fit rounded-lg border border-zinc-200 bg-white p-5">
            <div class="flex items-center justify-between text-lg font-semibold">
                <span>Tong tien</span>
                <span>{{ formatMoney(total) }}</span>
            </div>
            <Link href="/checkout" class="mt-5 block rounded-md bg-zinc-950 px-4 py-2.5 text-center text-sm font-semibold text-white hover:bg-zinc-800">
                Dat hang
            </Link>
        </aside>
    </div>
</template>
