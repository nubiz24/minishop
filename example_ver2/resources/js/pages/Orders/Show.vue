<script setup lang="ts">
import type { CustomerOrderDetail } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    order: CustomerOrderDetail;
}>();

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);

const statusClass = (status: string) => {
    const classes: Record<string, string> = {
        pending: 'border-amber-200 bg-amber-50 text-amber-800',
        processing: 'border-blue-200 bg-blue-50 text-blue-800',
        completed: 'border-emerald-200 bg-emerald-50 text-emerald-800',
        cancelled: 'border-red-200 bg-red-50 text-red-800',
    };

    return classes[status] ?? 'border-zinc-200 bg-zinc-50 text-zinc-700';
};
</script>

<template>
    <Head :title="`Don hang #${order.id}`" />

    <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl font-bold">Don hang #{{ order.id }}</h1>
            <p class="mt-2 text-zinc-600">Dat luc {{ order.created_at }}</p>
        </div>
        <Link href="/orders" class="rounded-md border border-zinc-300 px-4 py-2 text-sm font-semibold hover:bg-white">
            Quay lai
        </Link>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_340px]">
        <section class="rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="text-lg font-semibold">San pham trong don</h2>
            <div class="mt-4 divide-y divide-zinc-200">
                <div v-for="item in order.items" :key="item.id || item.product_name" class="flex justify-between gap-4 py-3 text-sm">
                    <div>
                        <div class="font-medium">{{ item.product_name }}</div>
                        <div class="text-zinc-600">{{ formatMoney(item.price) }} x {{ item.quantity }}</div>
                    </div>
                    <div class="font-semibold">{{ formatMoney(item.subtotal) }}</div>
                </div>
            </div>
            <div class="mt-4 flex justify-between border-t border-zinc-200 pt-4 text-base font-semibold">
                <span>Tong tien</span>
                <span>{{ formatMoney(order.total_amount) }}</span>
            </div>
        </section>

        <aside class="space-y-5">
            <section class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Tinh trang</h2>
                <span :class="['mt-3 inline-flex rounded-full border px-3 py-1 text-sm font-medium', statusClass(order.status)]">
                    {{ order.status_label }}
                </span>
            </section>

            <section class="rounded-lg border border-zinc-200 bg-white p-5">
                <h2 class="text-lg font-semibold">Thong tin giao hang</h2>
                <div class="mt-3 space-y-2 text-sm text-zinc-700">
                    <p><span class="font-medium text-zinc-950">Ten:</span> {{ order.customer_name }}</p>
                    <p><span class="font-medium text-zinc-950">Email:</span> {{ order.customer_email }}</p>
                    <p><span class="font-medium text-zinc-950">SDT:</span> {{ order.customer_phone || '-' }}</p>
                    <p><span class="font-medium text-zinc-950">Dia chi:</span> {{ order.shipping_address }}</p>
                    <p><span class="font-medium text-zinc-950">Ghi chu:</span> {{ order.note || '-' }}</p>
                </div>
            </section>
        </aside>
    </div>
</template>
