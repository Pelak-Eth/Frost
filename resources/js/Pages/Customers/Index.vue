<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    customers: {
        type: Array,
        default: () => [],
    },
});

const showCreateForm = ref(false);

const form = useForm({
    name: '',
    phone: '',
    email: '',
});

const submit = () => {
    form.post('/customers/create', {
        onSuccess: () => {
            form.reset();
            showCreateForm.value = false;
        },
    });
};
</script>

<template>
    <Head title="Customers" />

    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">Customers</h1>
            <button
                type="button"
                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                @click="showCreateForm = !showCreateForm"
            >
                {{ showCreateForm ? 'Cancel' : 'New Customer' }}
            </button>
        </div>

        <div v-if="showCreateForm" class="mb-6 overflow-hidden rounded-lg bg-white shadow">
            <div class="px-6 py-5">
                <form @submit.prevent="submit" class="grid grid-cols-1 gap-4 md:grid-cols-12">
                    <div class="md:col-span-4">
                        <input
                            v-model="form.name"
                            type="text"
                            placeholder="Name"
                            class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :class="form.errors.name ? 'border-red-500' : 'border-gray-300'"
                        />
                    </div>
                    <div class="md:col-span-4">
                        <input
                            v-model="form.phone"
                            type="tel"
                            placeholder="Phone"
                            required
                            class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :class="form.errors.phone ? 'border-red-500' : 'border-gray-300'"
                        />
                    </div>
                    <div class="md:col-span-3">
                        <input
                            v-model="form.email"
                            type="email"
                            placeholder="Email"
                            class="block w-full rounded-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                            :class="form.errors.email ? 'border-red-500' : 'border-gray-300'"
                        />
                    </div>
                    <div class="md:col-span-1">
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-md bg-green-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="overflow-hidden rounded-lg bg-white shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Points</th>
                        <th class="px-6 py-3" />
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    <tr v-for="customer in customers" :key="customer.id" class="hover:bg-gray-50">
                        <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-900">{{ customer.name }}</td>
                        <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-700">{{ customer.phone }}</td>
                        <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-700">{{ customer.email }}</td>
                        <td class="whitespace-nowrap px-6 py-3 text-sm text-gray-700">{{ customer.points }}</td>
                        <td class="whitespace-nowrap px-6 py-3 text-right text-sm">
                            <Link
                                :href="`/customers/${customer.id}/show`"
                                class="inline-flex items-center rounded-md border border-blue-600 px-3 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50"
                            >
                                View
                            </Link>
                        </td>
                    </tr>
                    <tr v-if="!customers.length">
                        <td colspan="5" class="px-6 py-6 text-center text-sm text-gray-500">
                            No customers found.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </AppLayout>
</template>
