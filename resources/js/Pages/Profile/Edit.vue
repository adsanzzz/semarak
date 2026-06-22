<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});
</script>

<template>
    <Head title="Profile" />

    <AuthenticatedLayout :with-sidebar-offset="false">
        <template #header>
            <div class="relative mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
                <Link
                    :href="route('dashboard')"
                    class="absolute left-1 top-1/2 inline-flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-gray-200 bg-white text-gray-700 shadow-sm transition hover:bg-gray-50 hover:text-gray-900 sm:left-2 lg:left-4"
                    aria-label="Kembali ke dashboard"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                        class="h-5 w-5"
                        aria-hidden="true"
                    >
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                    </svg>
                </Link>

                <h2 class="flex-1 text-center text-xl font-semibold leading-tight text-gray-800">
                    Profile
                </h2>
            </div>
        </template>

        <div class="py-12">
            <div class="mx-auto w-full max-w-3xl space-y-6 px-4 sm:px-6 lg:px-8">
                <div
                    class="w-full bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdateProfileInformationForm
                        :must-verify-email="mustVerifyEmail"
                        :status="status"
                        class="w-full"
                    />
                </div>

                <div
                    class="w-full bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <UpdatePasswordForm class="w-full" />
                </div>

                <div
                    class="w-full bg-white p-4 shadow sm:rounded-lg sm:p-8"
                >
                    <DeleteUserForm class="w-full" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
