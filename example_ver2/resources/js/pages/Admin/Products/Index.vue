<script setup lang="ts">
import type { Paginated, Product } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';

defineProps<{
    products: Paginated<Product>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'San pham', href: '/admin/products' },
        ],
    },
});

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);

const deleteProduct = (product: Product) => {
    if (confirm(`Xoa san pham "${product.name}"?`)) {
        router.delete(`/admin/products/${product.id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Quan ly san pham" />

    <div class="p-4">
        <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-semibold">Quan ly san pham</h1>
                <p class="text-sm text-muted-foreground">Them, sua, xoa san pham hien thi tren website.</p>
            </div>
            <Link href="/admin/products/create" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">
                Them san pham
            </Link>
        </div>

        <div class="overflow-hidden rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead class="bg-muted text-left">
                    <tr>
                        <th class="p-3">San pham</th>
                        <th class="p-3">Gia</th>
                        <th class="p-3">Ton kho</th>
                        <th class="p-3">Trang thai</th>
                        <th class="p-3 text-right">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="product in products.data" :key="product.id" class="border-t">
                        <td class="p-3">
                            <div class="font-medium">{{ product.name }}</div>
                            <div class="text-xs text-muted-foreground">{{ product.slug }}</div>
                        </td>
                        <td class="p-3">{{ formatMoney(product.price) }}</td>
                        <td class="p-3">{{ product.stock }}</td>
                        <td class="p-3">{{ product.is_active ? 'Dang ban' : 'An' }}</td>
                        <td class="p-3">
                            <div class="flex justify-end gap-2">
                                <Link :href="`/admin/products/${product.id}/edit`" class="rounded-md border px-3 py-1.5 hover:bg-muted">
                                    Sua
                                </Link>
                                <button class="rounded-md border px-3 py-1.5 text-red-700 hover:bg-red-50" @click="deleteProduct(product)">
                                    Xoa
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <nav v-if="products.last_page > 1" class="mt-5 flex flex-wrap gap-2">
            <Link
                v-for="link in products.links"
                :key="link.label"
                :href="link.url || '#'"
                :class="[
                    'rounded-md border px-3 py-2 text-sm',
                    link.active ? 'bg-primary text-primary-foreground' : 'bg-card',
                    !link.url ? 'pointer-events-none opacity-50' : 'hover:bg-muted',
                ]"
                v-html="link.label"
            />
        </nav>
    </div>
</template>
