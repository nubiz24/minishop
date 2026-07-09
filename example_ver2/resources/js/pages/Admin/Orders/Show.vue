<script setup lang="ts">
import type { OrderDetail } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{
    order: OrderDetail;
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

const updateStatus = (status: string) => {
    router.put(`/admin/orders/${props.order.id}`, { status }, { preserveScroll: true });
};
</script>

<template>
    <Head :title="`Don hang #${order.id}`" />

    <div class="p-4">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Don hang #{{ order.id }}</h1>
                <p class="text-sm text-muted-foreground">Tao luc {{ order.created_at }}</p>
            </div>
            <Link href="/admin/orders" class="rounded-md border px-4 py-2 text-sm font-semibold hover:bg-muted">
                Quay lai
            </Link>
        </div>

        <div class="grid gap-5 lg:grid-cols-[1fr_320px]">
            <section class="rounded-lg border bg-card p-5">
                <h2 class="text-lg font-semibold">San pham</h2>
                <div class="mt-4 divide-y">
                    <div v-for="item in order.items" :key="item.id || item.product_name" class="flex justify-between gap-4 py-3 text-sm">
                        <div>
                            <div class="font-medium">{{ item.product_name }}</div>
                            <div class="text-muted-foreground">{{ formatMoney(item.price) }} x {{ item.quantity }}</div>
                        </div>
                        <div class="font-medium">{{ formatMoney(item.subtotal) }}</div>
                    </div>
                </div>
                <div class="mt-4 flex justify-between border-t pt-4 text-base font-semibold">
                    <span>Tong tien</span>
                    <span>{{ formatMoney(order.total_amount) }}</span>
                </div>
            </section>

            <aside class="space-y-5">
                <section class="rounded-lg border bg-card p-5">
                    <h2 class="text-lg font-semibold">Trang thai</h2>
                    <select :value="order.status" class="mt-3 w-full rounded-md border bg-background px-3 py-2" @change="updateStatus(($event.target as HTMLSelectElement).value)">
                        <option v-for="(label, value) in statuses" :key="value" :value="value">{{ label }}</option>
                    </select>
                </section>

                <section class="rounded-lg border bg-card p-5">
                    <h2 class="text-lg font-semibold">Khach hang</h2>
                    <div class="mt-3 space-y-2 text-sm">
                        <p><span class="text-muted-foreground">Ten:</span> {{ order.customer_name }}</p>
                        <p><span class="text-muted-foreground">Email:</span> {{ order.customer_email }}</p>
                        <p><span class="text-muted-foreground">SDT:</span> {{ order.customer_phone || '-' }}</p>
                        <p><span class="text-muted-foreground">Dia chi:</span> {{ order.shipping_address }}</p>
                        <p><span class="text-muted-foreground">Ghi chu:</span> {{ order.note || '-' }}</p>
                    </div>
                </section>
            </aside>
        </div>
    </div>
</template>
