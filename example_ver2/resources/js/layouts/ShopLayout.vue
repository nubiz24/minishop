<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { LayoutDashboard, LogIn, ReceiptText, ShoppingCart, Store, UserPlus } from '@lucide/vue';

const page = usePage();

const user = computed(() => page.props.auth.user);
const cartCount = computed(() => Number(page.props.cartCount ?? 0));
</script>

<template>
    <div class="min-h-screen bg-zinc-50 text-zinc-950">
        <header class="border-b border-zinc-200 bg-white">
            <div class="mx-auto flex max-w-6xl flex-col gap-4 px-4 py-4 sm:flex-row sm:items-center sm:justify-between">
                <Link href="/" class="flex items-center gap-2 text-lg font-semibold">
                    <Store class="h-5 w-5 text-emerald-700" />
                    Mini Shop
                </Link>

                <nav class="flex flex-wrap items-center gap-2 text-sm">
                    <Link href="/" class="rounded-md px-3 py-2 text-zinc-700 hover:bg-zinc-100">
                        Trang chu
                    </Link>
                    <Link href="/products" class="rounded-md px-3 py-2 text-zinc-700 hover:bg-zinc-100">
                        San pham
                    </Link>
                    <Link href="/cart" class="inline-flex items-center gap-2 rounded-md border border-zinc-300 px-3 py-2 hover:bg-zinc-100">
                        <ShoppingCart class="h-4 w-4" />
                        Gio hang
                        <span class="rounded bg-emerald-700 px-1.5 py-0.5 text-xs text-white">{{ cartCount }}</span>
                    </Link>
                    <Link
                        v-if="user"
                        href="/orders"
                        class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-zinc-700 hover:bg-zinc-100"
                    >
                        <ReceiptText class="h-4 w-4" />
                        Don hang
                    </Link>
                    <Link
                        v-if="user?.role === 'admin'"
                        href="/admin/products"
                        class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-zinc-700 hover:bg-zinc-100"
                    >
                        <LayoutDashboard class="h-4 w-4" />
                        Admin
                    </Link>
                    <Link
                        v-else-if="user"
                        href="/dashboard"
                        class="rounded-md px-3 py-2 text-zinc-700 hover:bg-zinc-100"
                    >
                        Tai khoan
                    </Link>
                    <template v-else>
                        <Link href="/login" class="inline-flex items-center gap-2 rounded-md px-3 py-2 text-zinc-700 hover:bg-zinc-100">
                            <LogIn class="h-4 w-4" />
                            Dang nhap
                        </Link>
                        <Link href="/register" class="inline-flex items-center gap-2 rounded-md bg-zinc-950 px-3 py-2 text-white hover:bg-zinc-800">
                            <UserPlus class="h-4 w-4" />
                            Dang ky
                        </Link>
                    </template>
                </nav>
            </div>
        </header>

        <main class="mx-auto max-w-6xl px-4 py-8">
            <div v-if="page.props.flash?.success" class="mb-6 rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ page.props.flash.success }}
            </div>
            <div v-if="page.props.flash?.error" class="mb-6 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                {{ page.props.flash.error }}
            </div>

            <slot />
        </main>
    </div>
</template>
