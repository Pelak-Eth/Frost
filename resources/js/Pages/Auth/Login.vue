<script setup>
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/users/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="flex min-h-screen items-start justify-center bg-gray-50 px-4 pt-16">
        <div class="w-full max-w-md">
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                    <h1 class="text-base font-semibold text-gray-900">Login</h1>
                </div>

                <div class="px-6 py-5">
                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <label for="email" class="mb-1 block text-sm font-medium text-gray-700">
                                Email
                            </label>
                            <input
                                id="email"
                                v-model="form.email"
                                type="email"
                                required
                                autofocus
                                class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                :class="form.errors.email ? 'border-red-500' : 'border-gray-300'"
                            />
                            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                                {{ form.errors.email }}
                            </p>
                        </div>

                        <div>
                            <label for="password" class="mb-1 block text-sm font-medium text-gray-700">
                                Password
                            </label>
                            <input
                                id="password"
                                v-model="form.password"
                                type="password"
                                required
                                class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                :class="form.errors.password ? 'border-red-500' : 'border-gray-300'"
                            />
                            <p v-if="form.errors.password" class="mt-1 text-sm text-red-600">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-gray-700">
                            <input
                                id="remember"
                                v-model="form.remember"
                                type="checkbox"
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            />
                            Remember me
                        </label>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Log in
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
