<script setup lang="ts">
import type { CustomerOrderSummary, Paginated } from '@/types';
import { Head, Link } from '@inertiajs/vue3';

defineProps<{
    orders: Paginated<CustomerOrderSummary>;
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
    <Head title="Don hang cua toi" />

    <div class="mb-6">
        <h1 class="text-3xl font-bold">Don hang cua toi</h1>
        <p class="mt-2 text-zinc-600">Theo doi lich su mua hang va tinh trang xu ly don.</p>
    </div>

    <div v-if="!orders.data.length" class="rounded-lg border border-zinc-200 bg-white p-8 text-center">
        <p class="text-zinc-700">Ban chua co don hang nao.</p>
        <Link href="/products" class="mt-4 inline-flex rounded-md bg-zinc-950 px-4 py-2 text-sm font-semibold text-white">
            Mua san pham
        </Link>
    </div>

    <div v-else class="overflow-hidden rounded-lg border border-zinc-200 bg-white">
        <table class="w-full text-sm">
            <thead class="bg-zinc-100 text-left">
                <tr>
                    <th class="p-3">Ma don</th>
                    <th class="p-3">Ngay dat</th>
                    <th class="p-3">So luong</th>
                    <th class="p-3">Tong tien</th>
                    <th class="p-3">Trang thai</th>
                    <th class="p-3 text-right">Chi tiet</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="order in orders.data" :key="order.id" class="border-t border-zinc-200">
                    <td class="p-3 font-semibold">#{{ order.id }}</td>
                    <td class="p-3">{{ order.created_at }}</td>
                    <td class="p-3">{{ order.items_count }}</td>
                    <td class="p-3 font-medium">{{ formatMoney(order.total_amount) }}</td>
                    <td class="p-3">
                        <span :class="['rounded-full border px-2.5 py-1 text-xs font-medium', statusClass(order.status)]">
                            {{ order.status_label }}
                        </span>
                    </td>
                    <td class="p-3 text-right">
                        <Link :href="`/orders/${order.id}`" class="rounded-md border border-zinc-300 px-3 py-1.5 hover:bg-zinc-100">
                            Xem
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <nav v-if="orders.last_page > 1" class="mt-6 flex flex-wrap gap-2">
        <Link
            v-for="link in orders.links"
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
