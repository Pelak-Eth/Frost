<script setup>
import { ref, computed } from 'vue';
import { Head, router, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage();

const enabling = ref(false);
const confirming = ref(false);
const disabling = ref(false);
const qrCodeSvg = ref(null);
const setupKey = ref(null);
const recoveryCodes = ref([]);
const confirmationCode = ref('');
const confirmationError = ref(null);

const twoFactorEnabled = computed(
    () => page.props.auth.user?.two_factor_enabled ?? false
);

const enableTwoFactor = () => {
    enabling.value = true;

    router.post('/user/two-factor-authentication', {}, {
        preserveScroll: true,
        onSuccess: () => Promise.all([
            showQrCode(),
            showSetupKey(),
            showRecoveryCodes(),
        ]).then(() => {
            confirming.value = true;
        }),
        onFinish: () => {
            enabling.value = false;
        },
    });
};

const showQrCode = () => {
    return axios.get('/user/two-factor-qr-code').then((response) => {
        qrCodeSvg.value = response.data.svg;
    });
};

const showSetupKey = () => {
    return axios.get('/user/two-factor-secret-key').then((response) => {
        setupKey.value = response.data.secretKey;
    });
};

const showRecoveryCodes = () => {
    return axios.get('/user/two-factor-recovery-codes').then((response) => {
        recoveryCodes.value = response.data;
    });
};

const confirmTwoFactor = () => {
    confirmationError.value = null;

    router.post('/user/confirmed-two-factor-authentication', {
        code: confirmationCode.value,
    }, {
        preserveScroll: true,
        errorBag: 'confirmTwoFactorAuthentication',
        onSuccess: () => {
            confirming.value = false;
            qrCodeSvg.value = null;
            setupKey.value = null;
            confirmationCode.value = '';
        },
        onError: (errors) => {
            confirmationError.value = errors.code ?? 'Invalid verification code.';
        },
    });
};

const regenerateRecoveryCodes = () => {
    axios.post('/user/two-factor-recovery-codes').then(() => {
        showRecoveryCodes();
    });
};

const disableTwoFactor = () => {
    disabling.value = true;

    router.delete('/user/two-factor-authentication', {
        preserveScroll: true,
        onSuccess: () => {
            confirming.value = false;
            qrCodeSvg.value = null;
            setupKey.value = null;
            recoveryCodes.value = [];
        },
        onFinish: () => {
            disabling.value = false;
        },
    });
};
</script>

<template>
    <Head title="Two-Factor Authentication" />

    <AppLayout>
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Two-Factor Authentication</h1>
            <p class="mt-1 text-sm text-gray-500">
                Add additional security to your account using two-factor authentication.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <div class="overflow-hidden rounded-lg bg-white shadow">
                    <div class="flex items-center gap-2 border-b border-gray-200 bg-gray-50 px-6 py-4">
                        <span
                            v-if="twoFactorEnabled && !confirming"
                            class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-semibold text-green-800"
                        >
                            Enabled
                        </span>
                        <span
                            v-else-if="confirming"
                            class="inline-flex items-center rounded-full bg-yellow-100 px-2.5 py-0.5 text-xs font-semibold text-yellow-800"
                        >
                            Pending Confirmation
                        </span>
                        <span
                            v-else
                            class="inline-flex items-center rounded-full bg-gray-200 px-2.5 py-0.5 text-xs font-semibold text-gray-700"
                        >
                            Disabled
                        </span>
                        <span class="text-base font-semibold text-gray-900">Authenticator App</span>
                    </div>

                    <div class="space-y-4 px-6 py-5 text-sm text-gray-700">
                        <p>
                            When two-factor authentication is enabled, you will be prompted
                            for a secure, random token during authentication. You may retrieve
                            this token from your phone's Google Authenticator, Authy, or
                            1Password application.
                        </p>

                        <!-- QR Code + Setup Key -->
                        <div v-if="qrCodeSvg" class="space-y-3">
                            <p class="font-semibold text-gray-900">
                                Scan the following QR code using your authenticator app:
                            </p>
                            <div class="inline-block rounded border border-gray-200 bg-white p-3" v-html="qrCodeSvg" />

                            <p v-if="setupKey" class="text-sm">
                                <strong class="font-semibold text-gray-900">Setup Key:</strong>
                                <code class="ml-1 select-all rounded bg-gray-100 px-1.5 py-0.5 font-mono text-xs">{{ setupKey }}</code>
                            </p>
                        </div>

                        <!-- Confirmation input -->
                        <div v-if="confirming" class="space-y-2">
                            <label for="confirmation-code" class="block text-sm font-semibold text-gray-900">
                                Enter the 6-digit code from your app to confirm:
                            </label>
                            <div class="flex max-w-xs">
                                <input
                                    id="confirmation-code"
                                    v-model="confirmationCode"
                                    type="text"
                                    inputmode="numeric"
                                    autocomplete="one-time-code"
                                    placeholder="123456"
                                    class="block w-full rounded-l-md border px-3 py-2 text-sm shadow-sm focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    :class="confirmationError ? 'border-red-500' : 'border-gray-300'"
                                    @keyup.enter="confirmTwoFactor"
                                />
                                <button
                                    type="button"
                                    class="rounded-r-md border border-l-0 border-blue-600 bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    @click="confirmTwoFactor"
                                >
                                    Confirm
                                </button>
                            </div>
                            <p v-if="confirmationError" class="text-xs text-red-600">
                                {{ confirmationError }}
                            </p>
                        </div>

                        <!-- Recovery codes -->
                        <div v-if="recoveryCodes.length > 0" class="space-y-2">
                            <p class="font-semibold text-gray-900">Recovery Codes</p>
                            <p class="text-xs text-gray-500">
                                Store these recovery codes in a secure password manager.
                                They can be used to recover access to your account if your
                                two-factor authentication device is lost.
                            </p>
                            <div class="rounded bg-gray-100 p-3 font-mono text-xs text-gray-800">
                                <div v-for="code in recoveryCodes" :key="code">{{ code }}</div>
                            </div>
                        </div>

                        <!-- Action buttons -->
                        <div class="flex flex-wrap items-center gap-2 pt-2">
                            <button
                                v-if="!twoFactorEnabled && !confirming"
                                type="button"
                                :disabled="enabling"
                                class="rounded-md bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                                @click="enableTwoFactor"
                            >
                                {{ enabling ? 'Enabling...' : 'Enable Two-Factor' }}
                            </button>

                            <template v-else>
                                <button
                                    v-if="twoFactorEnabled && recoveryCodes.length === 0"
                                    type="button"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                    @click="showRecoveryCodes"
                                >
                                    Show Recovery Codes
                                </button>
                                <button
                                    v-if="twoFactorEnabled && recoveryCodes.length > 0"
                                    type="button"
                                    class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2"
                                    @click="regenerateRecoveryCodes"
                                >
                                    Regenerate Recovery Codes
                                </button>
                                <button
                                    type="button"
                                    :disabled="disabling"
                                    class="rounded-md bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-60"
                                    @click="disableTwoFactor"
                                >
                                    {{ disabling ? 'Disabling...' : 'Disable Two-Factor' }}
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
