<script setup lang="ts">
import type { AdminUser, Paginated } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    users: Paginated<AdminUser>;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            { title: 'Admin', href: '/admin' },
            { title: 'Nguoi dung', href: '/admin/users' },
        ],
    },
});

const editingUserId = ref<number | null>(null);

const form = useForm({
    name: '',
    email: '',
    role: 'user' as 'admin' | 'user',
});

const startEdit = (user: AdminUser) => {
    editingUserId.value = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.clearErrors();
};

const cancelEdit = () => {
    editingUserId.value = null;
    form.reset();
};

const submit = (user: AdminUser) => {
    form.put(`/admin/users/${user.id}`, {
        preserveScroll: true,
        onSuccess: cancelEdit,
    });
};

const deleteUser = (user: AdminUser) => {
    if (confirm(`Xoa nguoi dung "${user.name}"?`)) {
        router.delete(`/admin/users/${user.id}`, { preserveScroll: true });
    }
};
</script>

<template>
    <Head title="Quan ly user" />

    <div class="p-4">
        <div class="mb-5">
            <h1 class="text-2xl font-semibold">Quan ly user</h1>
            <p class="text-sm text-muted-foreground">Xem danh sach, doi role va xoa tai khoan khong can dung.</p>
        </div>

        <div class="overflow-hidden rounded-lg border bg-card">
            <table class="w-full text-sm">
                <thead class="bg-muted text-left">
                    <tr>
                        <th class="p-3">Ten</th>
                        <th class="p-3">Email</th>
                        <th class="p-3">Role</th>
                        <th class="p-3">Don hang</th>
                        <th class="p-3 text-right">Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="user in props.users.data" :key="user.id" class="border-t">
                        <template v-if="editingUserId === user.id">
                            <td class="p-3"><input v-model="form.name" class="w-full rounded-md border bg-background px-2 py-1.5" /></td>
                            <td class="p-3"><input v-model="form.email" class="w-full rounded-md border bg-background px-2 py-1.5" /></td>
                            <td class="p-3">
                                <select v-model="form.role" class="rounded-md border bg-background px-2 py-1.5">
                                    <option value="user">user</option>
                                    <option value="admin">admin</option>
                                </select>
                            </td>
                            <td class="p-3">{{ user.orders_count }}</td>
                            <td class="p-3">
                                <div class="flex justify-end gap-2">
                                    <button class="rounded-md bg-primary px-3 py-1.5 text-primary-foreground" @click="submit(user)">Luu</button>
                                    <button class="rounded-md border px-3 py-1.5" @click="cancelEdit">Huy</button>
                                </div>
                            </td>
                        </template>
                        <template v-else>
                            <td class="p-3 font-medium">{{ user.name }}</td>
                            <td class="p-3">{{ user.email }}</td>
                            <td class="p-3">{{ user.role }}</td>
                            <td class="p-3">{{ user.orders_count }}</td>
                            <td class="p-3">
                                <div class="flex justify-end gap-2">
                                    <button class="rounded-md border px-3 py-1.5 hover:bg-muted" @click="startEdit(user)">Sua</button>
                                    <button class="rounded-md border px-3 py-1.5 text-red-700 hover:bg-red-50" @click="deleteUser(user)">Xoa</button>
                                </div>
                            </td>
                        </template>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
