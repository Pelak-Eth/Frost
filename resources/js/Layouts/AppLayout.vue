<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    title: String,
});

const userMenuOpen = ref(false);
const userMenuRef = ref(null);

const closeOnOutsideClick = (event) => {
    if (userMenuRef.value && !userMenuRef.value.contains(event.target)) {
        userMenuOpen.value = false;
    }
};

onMounted(() => document.addEventListener('click', closeOnOutsideClick));
onBeforeUnmount(() => document.removeEventListener('click', closeOnOutsideClick));
</script>

<template>
    <div class="min-h-screen bg-gray-50 text-gray-900">
        <nav class="bg-gray-900 text-gray-100 shadow">
            <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <Link href="/" class="text-lg font-semibold text-white hover:text-gray-200">
                    Frost
                </Link>

                <div class="flex items-center gap-1">
                    <Link
                        href="/schedule"
                        class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white"
                    >
                        Schedule
                    </Link>
                    <Link
                        href="/customers"
                        class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white"
                    >
                        Customers
                    </Link>
                    <Link
                        href="/orders"
                        class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white"
                    >
                        Orders
                    </Link>
                    <Link
                        href="/announcements"
                        class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white"
                    >
                        Announcements
                    </Link>
                </div>

                <div ref="userMenuRef" class="relative">
                    <template v-if="$page.props.auth.user">
                        <button
                            type="button"
                            class="flex items-center gap-2 rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white focus:outline-none focus:ring-2 focus:ring-gray-500"
                            @click.stop="userMenuOpen = !userMenuOpen"
                        >
                            {{ $page.props.auth.user.name }}
                            <span
                                v-if="$page.props.auth.user.two_factor_enabled"
                                class="inline-flex items-center rounded-full bg-green-600 px-2 py-0.5 text-xs font-semibold text-white"
                                title="2FA enabled"
                            >
                                <i class="fa fa-shield"></i>
                            </span>
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div
                            v-if="userMenuOpen"
                            class="absolute right-0 z-50 mt-2 w-48 origin-top-right rounded-md bg-white py-1 text-gray-900 shadow-lg ring-1 ring-black/5"
                        >
                            <Link
                                href="/account/edit"
                                class="block px-4 py-2 text-sm hover:bg-gray-100"
                                @click="userMenuOpen = false"
                            >
                                Account Settings
                            </Link>
                            <Link
                                href="/account/two-factor"
                                class="block px-4 py-2 text-sm hover:bg-gray-100"
                                @click="userMenuOpen = false"
                            >
                                Two-Factor Auth
                            </Link>
                            <hr class="my-1 border-gray-200" />
                            <Link
                                href="/users/logout"
                                class="block px-4 py-2 text-sm hover:bg-gray-100"
                                @click="userMenuOpen = false"
                            >
                                Logout
                            </Link>
                        </div>
                    </template>
                    <Link
                        v-else
                        href="/users/login"
                        class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white"
                    >
                        Login
                    </Link>
                </div>
            </div>
        </nav>

        <main class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div
                v-if="$page.props.flash?.message"
                class="mb-4 rounded-md border border-blue-200 bg-blue-50 px-4 py-3 text-sm text-blue-800"
            >
                {{ $page.props.flash.message }}
            </div>

            <slot />
        </main>
    </div>
</template>
