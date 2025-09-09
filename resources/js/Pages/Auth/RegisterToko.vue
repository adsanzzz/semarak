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
    phone: '',
    nama_toko: '',
    alamat_toko: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register.toko'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Registrasi Toko" />
        <div class="flex flex-col items-center mb-6">
            <div class="flex items-center space-x-2">
                <img src="/images/Logomark.png" alt="Logo" class="w-10 h-10" />
                <span class="text-2xl font-bold text-gray-800">Semarak</span>
            </div>
            <h2 class="mt-4 text-xl font-semibold">Registrasi Toko (Penjual)</h2>
            <p class="text-gray-500 text-sm text-center max-w-md">
                Daftarkan toko Anda untuk mulai berjualan di marketplace UMKM Karanganyar.
            </p>
        </div>
        <!-- Tombol SSO Sakti -->
        <div class="mb-6 flex flex-col items-center">
            <a :href="route('sakti.login')"
               class="w-full flex items-center justify-center gap-2 bg-blue-700 hover:bg-blue-800 text-white font-semibold py-2 px-4 rounded-lg shadow transition mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m4 4h-1v-4h-1m-4 4h-1v-4h-1" /></svg>
                Daftar / Login dengan SSO Sakti
            </a>
            <span class="text-xs text-gray-400">atau daftar manual:</span>
        </div>
        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Nama Lengkap" />
                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus autocomplete="name" />
                <InputError class="mt-2" :message="form.errors.name" />
            </div>
            <div class="mt-4">
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required autocomplete="username" />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>
            <div class="mt-4">
                <InputLabel for="phone" value="No Telepon" />
                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" required autocomplete="tel" />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>
            <div class="mt-4">
                <InputLabel for="nama_toko" value="Nama Toko" />
                <TextInput id="nama_toko" type="text" class="mt-1 block w-full" v-model="form.nama_toko" required />
                <InputError class="mt-2" :message="form.errors.nama_toko" />
            </div>
            <div class="mt-4">
                <InputLabel for="alamat_toko" value="Alamat Toko" />
                <TextInput id="alamat_toko" type="text" class="mt-1 block w-full" v-model="form.alamat_toko" required />
                <InputError class="mt-2" :message="form.errors.alamat_toko" />
            </div>
            <div class="mt-4">
                <InputLabel for="password" value="Password" />
                <TextInput id="password" type="password" class="mt-1 block w-full" v-model="form.password" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>
            <div class="mt-4">
                <InputLabel for="password_confirmation" value="Konfirmasi Password" />
                <TextInput id="password_confirmation" type="password" class="mt-1 block w-full" v-model="form.password_confirmation" required autocomplete="new-password" />
                <InputError class="mt-2" :message="form.errors.password_confirmation" />
            </div>
            <div class="mt-6">
                <PrimaryButton class="w-full justify-center bg-yellow-500 hover:bg-yellow-600" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Daftar Toko
                </PrimaryButton>
            </div>
        </form>
        <p class="text-center text-sm text-gray-600 mt-6">
            Sudah punya akun?
            <Link :href="route('login')" class="font-semibold text-blue-600 hover:underline">
                Log in
            </Link>
        </p>
    </GuestLayout>
</template>
