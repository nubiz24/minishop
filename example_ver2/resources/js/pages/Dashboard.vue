<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Dashboard',
                href: '/dashboard',
            },
        ],
    },
});

const page = usePage();
const user = computed(() => page.props.auth.user);
</script>

<template>
    <Head title="Dashboard" />

    <div class="p-4">
        <section class="rounded-lg border bg-card p-6">
            <h1 class="text-2xl font-semibold">Xin chao {{ user?.name }}</h1>
            <p class="mt-2 text-muted-foreground">
                Day la khu vuc tai khoan. Admin co the quan ly user, san pham va don hang tu menu ben trai.
            </p>
            <div class="mt-5 flex flex-wrap gap-3">
                <Link href="/" class="rounded-md border px-4 py-2 text-sm font-semibold hover:bg-muted">
                    Ve trang chu
                </Link>
                <Link v-if="user?.role === 'admin'" href="/admin/products" class="rounded-md bg-primary px-4 py-2 text-sm font-semibold text-primary-foreground">
                    Quan ly san pham
                </Link>
            </div>
        </section>
    </div>
</template>
