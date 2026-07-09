<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import type { Product } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps<{
    product: Product | null;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'San pham', href: '/admin/products' },
        ],
    },
});

const isEditing = computed(() => Boolean(props.product));

const form = useForm({
    name: props.product?.name ?? '',
    description: props.product?.description ?? '',
    price: props.product?.price ?? 0,
    stock: props.product?.stock ?? 0,
    image_url: props.product?.image_url ?? '',
    is_active: props.product?.is_active ?? true,
});

const submit = () => {
    if (props.product) {
        form.put(`/admin/products/${props.product.id}`);
        return;
    }

    form.post('/admin/products');
};
</script>

<template>
    <Head :title="isEditing ? 'Sua san pham' : 'Them san pham'" />

    <div class="max-w-3xl p-4">
        <div class="mb-5">
            <h1 class="text-2xl font-semibold">{{ isEditing ? 'Sua san pham' : 'Them san pham' }}</h1>
            <p class="text-sm text-muted-foreground">Slug duoc tao tu ten san pham o backend.</p>
        </div>

        <form class="space-y-4 rounded-lg border bg-card p-5" @submit.prevent="submit">
            <div>
                <label class="block text-sm font-medium" for="name">Ten san pham</label>
                <input id="name" v-model="form.name" class="mt-1 w-full rounded-md border bg-background px-3 py-2" />
                <InputError :message="form.errors.name" />
            </div>
            <div>
                <label class="block text-sm font-medium" for="description">Mo ta</label>
                <textarea id="description" v-model="form.description" rows="4" class="mt-1 w-full rounded-md border bg-background px-3 py-2" />
                <InputError :message="form.errors.description" />
            </div>
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium" for="price">Gia</label>
                    <input id="price" v-model.number="form.price" type="number" min="0" class="mt-1 w-full rounded-md border bg-background px-3 py-2" />
                    <InputError :message="form.errors.price" />
                </div>
                <div>
                    <label class="block text-sm font-medium" for="stock">Ton kho</label>
                    <input id="stock" v-model.number="form.stock" type="number" min="0" class="mt-1 w-full rounded-md border bg-background px-3 py-2" />
                    <InputError :message="form.errors.stock" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium" for="image_url">Anh san pham URL</label>
                <input id="image_url" v-model="form.image_url" class="mt-1 w-full rounded-md border bg-background px-3 py-2" />
                <InputError :message="form.errors.image_url" />
            </div>
            <label class="flex items-center gap-2 text-sm">
                <input v-model="form.is_active" type="checkbox" class="rounded border" />
                Dang ban
            </label>
            <InputError :message="form.errors.is_active" />

            <div class="flex gap-3">
                <button type="submit" :disabled="form.processing" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground disabled:opacity-50">
                    Luu
                </button>
                <Link href="/admin/products" class="rounded-md border px-4 py-2 text-sm font-semibold hover:bg-muted">
                    Huy
                </Link>
            </div>
        </form>
    </div>
</template>
