<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
    company_name:          page.props.companyName  || '',
    company_email:         page.props.companyEmail || '',
    company_phone:         page.props.companyPhone || '',
    name:                  '',
    email:                 page.props.inviteEmail  || '',
    password:              '',
    password_confirmation: '',
    invite_url:            page.props.inviteUrl    || '',
    invite_email:          page.props.inviteEmail  || '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Account" />

        <input type="hidden" v-model="form.invite_url" />
        <input type="hidden" v-model="form.invite_email" />

        <h2 class="mb-6 text-lg font-semibold text-gray-900">
            {{ page.props.invite ? 'Activate account' : 'Create account' }}
        </h2>

        <form @submit.prevent="submit" class="space-y-4">

            <!-- Company fields (first company admin only) -->
            <template v-if="page.props.showCompanyFields">
                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700">Company name</label>
                    <input id="company_name" v-model="form.company_name" type="text"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                    <p v-if="form.errors.company_name" class="mt-1 text-xs text-red-500">{{ form.errors.company_name }}</p>
                </div>

                <div>
                    <label for="company_email" class="block text-sm font-medium text-gray-700">Company email</label>
                    <input id="company_email" v-model="form.company_email" type="email"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                    <p v-if="form.errors.company_email" class="mt-1 text-xs text-red-500">{{ form.errors.company_email }}</p>
                </div>

                <div>
                    <label for="company_phone" class="block text-sm font-medium text-gray-700">Company phone</label>
                    <input id="company_phone" v-model="form.company_phone" type="text"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                    <p v-if="form.errors.company_phone" class="mt-1 text-xs text-red-500">{{ form.errors.company_phone }}</p>
                </div>

                <hr class="border-gray-200" />
            </template>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                <input id="name" v-model="form.name" type="text" autofocus autocomplete="name"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input id="email" v-model="form.email" type="email" autocomplete="username"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input id="password" v-model="form.password" type="password" autocomplete="new-password"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm password</label>
                <input id="password_confirmation" v-model="form.password_confirmation" type="password" autocomplete="new-password"
                    class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500" />
                <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-500">{{ form.errors.password_confirmation }}</p>
            </div>

            <button
                type="submit"
                :disabled="form.processing"
                class="mt-2 w-full rounded-lg bg-gray-900 py-2.5 text-sm font-semibold text-white transition hover:bg-gray-700 disabled:opacity-50"
            >
                {{ page.props.invite ? 'Activate account' : 'Create account' }}
            </button>

            <div v-if="!page.props.invite" class="text-center">
                <Link :href="route('login')" class="text-xs text-gray-400 hover:text-gray-600">
                    Already have an account?
                </Link>
            </div>
        </form>
    </GuestLayout>
</template>
