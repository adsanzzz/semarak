<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <!-- Logo dan Judul -->
        <div class="flex flex-col items-center mb-6">
            <div class="flex items-center space-x-2">
                <img src="/images/Logomark.png" alt="Logo" class="w-10 h-10" />
                <span class="text-2xl font-bold text-gray-800">Semarak</span>
            </div>
            <h2 class="mt-4 text-xl font-semibold">Registrasi Akun Baru Semarak</h2>
            <p class="text-gray-500 text-sm text-center max-w-md">
                Dapatkan pengalaman belanja dan jualan yang lebih mudah di marketplace UMKM Karanganyar.
                Dengan satu akun, anda bisa menemukan produk lokal berkualitas dengan lebih luas!
            </p>
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
            <!-- Nama Lengkap -->
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <!-- No Telepon -->
            <div class="mt-4">
                <InputLabel for="phone" value="No Telepon" />
                <TextInput
                    id="phone"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.phone"
                    required
                    autocomplete="tel"
                />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <!-- Email -->
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autocomplete="username"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Password -->
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <!-- Confirm Password -->
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Confirm Password" />
                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>

            <!-- Syarat & Ketentuan -->
            <p class="text-xs text-gray-500 mt-4 text-center">
                Dengan Membuat Akun, Anda Setuju Dengan
                <a href="/terms" class="font-semibold">Syarat & Ketentuan</a>
                Serta
                <a href="/privacy" class="font-semibold">Kebijakan Privasi</a>
                Kami.
            </p>

            <!-- Tombol Buat Akun -->
            <div class="mt-6">
                <PrimaryButton
                    class="w-full justify-center bg-blue-600 hover:bg-blue-700"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Buat Akun
                </PrimaryButton>
            </div>

        </form>

        <!-- Divider -->
        <div class="flex items-center my-6">
            <hr class="flex-grow border-gray-300" />
            <span class="mx-2 text-gray-400 text-sm">ATAU</span>
            <hr class="flex-grow border-gray-300" />
        </div>

        <div class="mt-6 flex items-center justify-center">
    <a
        href="/sakti/login"
        class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition flex items-center gap-2"
    >
        <!-- Logo di sebelah kiri -->
        <img src="/images/sakti.png" alt="Sakti" class="w-5 h-5" />
        <span>Daftar dengan Sakti</span>
    </a>
</div>


        <!-- Sudah punya akun -->
        <p class="text-center text-sm text-gray-600 mt-6">
            Sudah punya akun?
            <Link :href="route('login')" class="font-semibold text-blue-600 hover:underline">
                Log in
            </Link>
        </p>
    </GuestLayout>
</template>
