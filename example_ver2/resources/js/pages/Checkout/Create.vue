<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import type { CartLine } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    lines: CartLine[];
    total: number;
    customer: {
        name: string;
        email: string;
    };
}>();

const form = useForm<{
    customer_name: string;
    customer_email: string;
    customer_phone: string;
    shipping_address: string;
    note: string;
    cart?: string;
}>({
    customer_name: props.customer.name,
    customer_email: props.customer.email,
    customer_phone: '',
    shipping_address: '',
    note: '',
});

const formatMoney = (value: number) =>
    new Intl.NumberFormat('vi-VN', {
        style: 'currency',
        currency: 'VND',
    }).format(value);

const submit = () => {
    form.post('/checkout');
};
</script>

<template>
    <Head title="Thanh toan" />

    <div class="mb-6">
        <h1 class="text-3xl font-bold">Thong tin dat hang</h1>
        <p class="mt-2 text-zinc-600">Don hang se duoc tao voi trang thai cho xu ly.</p>
    </div>

    <div class="grid gap-6 lg:grid-cols-[1fr_360px]">
        <form class="space-y-4 rounded-lg border border-zinc-200 bg-white p-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium" for="customer_name">Ho ten</label>
                <input id="customer_name" v-model="form.customer_name" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2" />
                <InputError :message="form.errors.customer_name" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="customer_email">Email</label>
                <input id="customer_email" v-model="form.customer_email" type="email" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2" />
                <InputError :message="form.errors.customer_email" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="customer_phone">So dien thoai</label>
                <input id="customer_phone" v-model="form.customer_phone" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2" />
                <InputError :message="form.errors.customer_phone" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="shipping_address">Dia chi giao hang</label>
                <textarea id="shipping_address" v-model="form.shipping_address" rows="4" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2" />
                <InputError :message="form.errors.shipping_address" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="note">Ghi chu</label>
                <textarea id="note" v-model="form.note" rows="3" class="mt-1 w-full rounded-md border border-zinc-300 px-3 py-2" />
                <InputError :message="form.errors.note" />
            </div>
            <InputError :message="form.errors.cart" />
            <button
                type="submit"
                :disabled="form.processing"
                class="rounded-md bg-zinc-950 px-4 py-2.5 text-sm font-semibold text-white hover:bg-zinc-800 disabled:opacity-50"
            >
                Xac nhan dat hang
            </button>
        </form>

        <aside class="h-fit rounded-lg border border-zinc-200 bg-white p-5">
            <h2 class="text-lg font-semibold">Don hang</h2>
            <div class="mt-4 space-y-3">
                <div v-for="line in lines" :key="line.product.id" class="flex justify-between gap-4 text-sm">
                    <span>{{ line.product.name }} x {{ line.quantity }}</span>
                    <span>{{ formatMoney(line.subtotal) }}</span>
                </div>
            </div>
            <div class="mt-5 flex justify-between border-t border-zinc-200 pt-4 font-semibold">
                <span>Tong tien</span>
                <span>{{ formatMoney(total) }}</span>
            </div>
            <Link href="/cart" class="mt-4 block text-sm font-medium text-emerald-700 hover:text-emerald-800">Sua gio hang</Link>
        </aside>
    </div>
</template>
