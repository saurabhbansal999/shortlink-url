<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router, usePage } from '@inertiajs/vue3';

const page = usePage();

defineProps({
    users: Array,
});
</script>

<template>
    <Head title="Team" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Team</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div v-if="page.props.flash?.status === 'success'" class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ page.props.flash.message }}
                </div>
                <div v-if="page.props.flash?.status === 'error'" class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700">
                    {{ page.props.flash.message }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">All Users</h3>
                            <button
                                @click="router.visit(route('members.create'))"
                                class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition"
                            >
                                Invite
                            </button>
                        </div>

                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr>
                                    <th class="py-3 pr-6 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                                    <th class="py-3 pr-6 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                                    <th class="py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Role</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="users.length === 0">
                                    <td colspan="3" class="py-8 text-center text-sm text-gray-400">No users yet.</td>
                                </tr>
                                <tr v-else v-for="user in users" :key="user.ulid" class="hover:bg-gray-50">
                                    <td class="py-3 pr-6 text-sm font-medium text-gray-900">{{ user.name }}</td>
                                    <td class="py-3 pr-6 text-sm text-gray-600">{{ user.email }}</td>
                                    <td class="py-3 text-sm">
                                        <span
                                            :class="user.role === 'Admin' ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-600'"
                                            class="rounded-full px-2.5 py-0.5 text-xs font-medium"
                                        >{{ user.role }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
