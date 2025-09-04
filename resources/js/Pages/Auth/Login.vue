<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <!-- Logo dan Judul -->
        <div class="flex flex-col items-center mb-6">
            <div class="flex items-center space-x-2">
                <img src="/images/Logomark.png" alt="Logo" class="w-10 h-10" />
                <span class="text-2xl font-bold text-gray-800">Semarak</span>
            </div>
            <h2 class="mt-4 text-xl font-semibold">Login Semarak</h2>
            <p class="text-gray-500 text-sm text-center">
                Masuk ke Akun Anda dan Temukan Produk UMKM Terbaik di SEMARAK!
            </p>
        </div>

        <!-- Status -->
        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <!-- Tombol Google -->
        <!-- <div class="mb-6">
            <a
                href="/auth/google"
                class="w-full border border-gray-300 py-2 rounded-lg flex items-center justify-center gap-2 text-gray-700 hover:bg-gray-100 transition"
            >
                <img
                    src="https://www.svgrepo.com/show/475656/google-color.svg"
                    alt="Google"
                    class="w-5 h-5"
                />
                Lanjutkan dengan Google
            </a>
        </div> -->

        

        <!-- Form -->
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Lupa password & Remember me -->
            <div class="mt-2 flex items-center justify-between">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600">Ingat saya</span>
                </label>
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm text-gray-500 hover:text-blue-600"
                >
                    Lupa Password?
                </Link>
            </div>

            <!-- Tombol Login -->
            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center bg-blue-600 hover:bg-blue-700"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Masuk
                </PrimaryButton>
            </div>
        </form>

        <!-- Divider -->
        <div class="flex items-center my-6">
            <hr class="flex-grow border-gray-300" />
            <span class="mx-2 text-gray-400 text-sm">ATAU</span>
            <hr class="flex-grow border-gray-300" />
        </div>

        <!-- Extra login Sakti -->
       <div class="mt-6 flex items-center justify-center">
    <a
        href="/sakti/login"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition flex items-center gap-2"
    >
        <img src="/images/sakti.png" alt="Sakti" class="w-5 h-5" />
        <span>Login dengan Sakti</span>
    </a>
</div>


        <!-- Register -->
        <p class="text-center text-sm text-gray-600 mt-6">
            Belum punya akun?
            <a href="/register" class="font-semibold text-blue-600 hover:underline">Buat Akun Baru</a>
        </p>
    </GuestLayout>
</template>
