<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
    announcements: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout>
        <h1 class="mb-6 text-2xl font-semibold text-gray-900">Dashboard</h1>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <h2 class="text-base font-semibold text-gray-900">Announcements</h2>
                    </div>

                    <div class="divide-y divide-gray-200">
                        <div
                            v-for="announcement in announcements"
                            :key="announcement.id"
                            class="px-6 py-4"
                        >
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ announcement.title }}
                            </h3>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ announcement.created_at }}
                            </p>
                            <div class="mt-2 text-sm text-gray-700" v-html="announcement.content" />
                        </div>

                        <p v-if="!announcements.length" class="px-6 py-4 text-sm text-gray-500">
                            No announcements yet.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
