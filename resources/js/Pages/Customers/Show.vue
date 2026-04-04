<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    customer: {
        type: Object,
        required: true,
    },
    orders: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head :title="customer.name" />

    <AppLayout>
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-900">{{ customer.name }}</h1>
            <Link
                href="/customers"
                class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
            >
                Back
            </Link>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-1">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Details</h2>
                    </div>
                    <div class="px-6 py-5">
                        <dl class="space-y-3 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500">Phone</dt>
                                <dd class="text-gray-900">{{ customer.phone }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Email</dt>
                                <dd class="text-gray-900">{{ customer.email || '—' }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Points</dt>
                                <dd class="text-gray-900">{{ customer.points }}</dd>
                            </div>
                            <div>
                                <dt class="font-medium text-gray-500">Preferred</dt>
                                <dd class="text-gray-900">{{ customer.preferred ? 'Yes' : 'No' }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Order History</h2>
                    </div>
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">#</th>
                                <th class="px-6 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Date</th>
                                <th class="px-6 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Total</th>
                                <th class="px-6 py-2 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            <tr v-for="order in orders" :key="order.id" class="hover:bg-gray-50">
                                <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-900">{{ order.id }}</td>
                                <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-700">{{ order.created_at }}</td>
                                <td class="whitespace-nowrap px-6 py-2 text-sm text-gray-700">${{ Number(order.total).toFixed(2) }}</td>
                                <td class="whitespace-nowrap px-6 py-2 text-sm">
                                    <span
                                        class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold"
                                        :class="order.complete
                                            ? 'bg-green-100 text-green-800'
                                            : 'bg-yellow-100 text-yellow-800'"
                                    >
                                        {{ order.complete ? 'Complete' : 'Open' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="!orders.length">
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    No orders yet.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
