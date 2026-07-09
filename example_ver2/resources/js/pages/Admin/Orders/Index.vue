<script setup lang="ts">
import type { OrderSummary, Paginated } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    orders: Paginated<OrderSummary>;
    statuses: Record<string, string>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Don hang', href: '/admin/orders' },
        ],
    },
});

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);

const updateStatus = (order: OrderSummary, status: string) => {
    router.put(`/admin/orders/${order.id}`, { status }, { preserveScroll: true });
};
</script>

<template>
    <Head title="Quan ly don hang" />

    <div class="p-4">
        <div class="mb-5">
            <h1 class="text-2xl font-semibold">Quan ly don hang</h1>
            <p class="text-sm text-muted-foreground">Theo doi thong tin khach va cap nhat trang thai don.</p>
        </div>

        <div class="overflow-hidden rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead class="bg-muted text-left">
                    <tr>
                        <th class="p-3">Ma don</th>
                        <th class="p-3">Khach hang</th>
                        <th class="p-3">Tong tien</th>
                        <th class="p-3">Trang thai</th>
                        <th class="p-3 text-right">Chi tiet</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="order in orders.data" :key="order.id" class="border-t">
                        <td class="p-3">#{{ order.id }}</td>
                        <td class="p-3">
                            <div class="font-medium">{{ order.customer_name }}</div>
                            <div class="text-xs text-muted-foreground">{{ order.customer_email }}</div>
                        </td>
                        <td class="p-3">{{ formatMoney(order.total_amount) }}</td>
                        <td class="p-3">
                            <select :value="order.status" class="rounded-md border bg-background px-2 py-1.5" @change="updateStatus(order, ($event.target as HTMLSelectElement).value)">
                                <option v-for="(label, value) in statuses" :key="value" :value="value">{{ label }}</option>
                            </select>
                        </td>
                        <td class="p-3 text-right">
                            <Link :href="`/admin/orders/${order.id}`" class="rounded-md border px-3 py-1.5 hover:bg-muted">Xem</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
