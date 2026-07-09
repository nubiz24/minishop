<script setup lang="ts">
import type { OrderItem } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    order: {
        id: number;
        status: string;
        total_amount: number;
        items: OrderItem[];
    };
}>();

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);
</script>

<template>
    <Head title="Dat hang thanh cong" />

    <section class="mx-auto max-w-2xl rounded-lg border border-zinc-200 bg-white p-6">
        <h1 class="text-2xl font-bold">Dat hang thanh cong</h1>
        <p class="mt-2 text-zinc-600">Ma don hang: #{{ order.id }}</p>

        <div class="mt-5 space-y-3">
            <div v-for="item in order.items" :key="item.product_name" class="flex justify-between gap-4 text-sm">
                <span>{{ item.product_name }} x {{ item.quantity }}</span>
                <span>{{ formatMoney(item.subtotal) }}</span>
            </div>
        </div>

        <div class="mt-5 flex justify-between border-t border-zinc-200 pt-4 font-semibold">
            <span>Tong tien</span>
            <span>{{ formatMoney(order.total_amount) }}</span>
        </div>

        <Link href="/products" class="mt-6 inline-flex rounded-md bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white">
            Tiep tuc mua hang
        </Link>
    </section>
</template>
