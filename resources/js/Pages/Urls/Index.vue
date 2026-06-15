<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const page = usePage();

defineProps({ urls: Array, isAdmin: Boolean, isSuperAdmin: Boolean });

const showModal = ref(false);
const form = useForm({ url: '' });

const submit = () => {
    form.post(route('urls.store'), {
        onSuccess: () => { showModal.value = false; form.reset(); },
    });
};

const copy = (text) => navigator.clipboard.writeText(text);
</script>

<template>
    <Head title="URL Shortener" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">URL Shortener</h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">

                <div v-if="page.props.flash?.status === 'success'" class="mb-4 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700">
                    {{ page.props.flash.message }}
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">

                        <div class="mb-5 flex items-center justify-between">
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ isSuperAdmin ? 'All URLs' : isAdmin ? 'All URLs' : 'My URLs' }}
                            </h3>
                            <div v-if="!isSuperAdmin" class="flex gap-2">
                                <button
                                    @click="router.post(route('urls.generate-all'))"
                                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition"
                                >
                                    Generate All
                                </button>
                                <button
                                    @click="showModal = true"
                                    class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 transition"
                                >
                                    Add URL
                                </button>
                            </div>
                        </div>

                        <table class="min-w-full divide-y divide-gray-100">
                            <thead>
                                <tr>
                                    <th v-if="!isSuperAdmin" class="py-3 pr-6 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Original URL</th>
                                    <th class="py-3 pr-6 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Short URL</th>
                                    <th v-if="isSuperAdmin" class="py-3 pr-6 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Company</th>
                                    <th v-if="isAdmin" class="py-3 pr-6 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Created By</th>
                                    <th v-if="!isSuperAdmin" class="py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-if="urls.length === 0">
                                    <td :colspan="isSuperAdmin ? 2 : isAdmin ? 4 : 3" class="py-8 text-center text-sm text-gray-400">No URLs yet.</td>
                                </tr>
                                <tr v-else v-for="item in urls" :key="item.ulid" class="hover:bg-gray-50">
                                    <td v-if="!isSuperAdmin" class="py-3 pr-6 text-sm text-gray-700 max-w-xs">
                                        <span class="block truncate" :title="item.original_url">{{ item.original_url }}</span>
                                    </td>
                                    <td class="py-3 pr-6 text-sm">
                                        <a v-if="item.short_url" :href="item.short_url" target="_blank" class="text-indigo-600 hover:underline">
                                            {{ item.short_url }}
                                        </a>
                                        <span v-else class="text-gray-400">—</span>
                                    </td>
                                    <td v-if="isSuperAdmin" class="py-3 pr-6 text-sm text-gray-600">{{ item.company_name }}</td>
                                    <td v-if="isAdmin" class="py-3 pr-6 text-sm text-gray-600">{{ item.created_by }}</td>
                                    <td v-if="!isSuperAdmin" class="py-3 text-sm">
                                        <div class="flex gap-2">
                                            <button
                                                @click="router.post(route('urls.shorten', item.ulid))"
                                                class="rounded-md border border-gray-300 px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50 transition"
                                            >
                                                Shorten
                                            </button>
                                            <button
                                                v-if="item.short_url"
                                                @click="copy(item.short_url)"
                                                class="rounded-md border border-indigo-200 px-3 py-1 text-xs font-medium text-indigo-600 hover:bg-indigo-50 transition"
                                            >
                                                Copy
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                    </div>
                </div>
            </div>
        </div>

        <!-- Add URL Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/30">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
                <h3 class="mb-4 text-base font-semibold text-gray-900">Add URL</h3>
                <form @submit.prevent="submit" class="space-y-4">
                    <div>
                        <label for="url" class="block text-sm font-medium text-gray-700">URL</label>
                        <input
                            id="url"
                            v-model="form.url"
                            type="url"
                            placeholder="https://example.com"
                            autofocus
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500"
                        />
                        <p v-if="form.errors.url" class="mt-1 text-xs text-red-500">{{ form.errors.url }}</p>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showModal = false; form.reset()"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition">
                            Cancel
                        </button>
                        <button type="submit" :disabled="form.processing"
                            class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-700 disabled:opacity-50 transition">
                            Add
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
