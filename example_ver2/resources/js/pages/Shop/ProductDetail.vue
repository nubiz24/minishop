<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import type { Product } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    product: Product;
}>();

const form = useForm({
    quantity: 1,
});

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);

const addToCart = () => {
    form.post(`/cart/${props.product.id}`, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="product.name" />

    <div class="grid gap-8 lg:grid-cols-2">
        <img
            :src="product.image_url || 'https://images.unsplash.com/photo-1556742049-0cfed4f6a45d?auto=format&fit=crop&w=900&q=80'"
            :alt="product.name"
            class="h-[420px] w-full rounded-lg object-cover"
        />

        <section class="space-y-5">
            <Link href="/products" class="text-sm font-medium text-emerald-700 hover:text-emerald-800">Quay lai danh sach</Link>
            <div>
                <h1 class="text-3xl font-bold">{{ product.name }}</h1>
                <p class="mt-3 text-2xl font-semibold text-emerald-700">{{ formatMoney(product.price) }}</p>
            </div>
            <p class="text-zinc-700">{{ product.description }}</p>
            <p class="text-sm text-zinc-600">Ton kho: {{ product.stock }}</p>

            <form class="max-w-xs space-y-3" @submit.prevent="addToCart">
                <label class="block text-sm font-medium" for="quantity">So luong</label>
                <input
                    id="quantity"
                    v-model.number="form.quantity"
                    type="number"
                    min="1"
                    :max="product.stock"
                    class="w-full rounded-md border border-zinc-300 px-3 py-2"
                />
                <InputError :message="form.errors.quantity" />
                <button
                    type="submit"
                    :disabled="form.processing || product.stock < 1"
                    class="w-full rounded-md bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    Them vao gio hang
                </button>
            </form>
        </section>
    </div>
</template>
