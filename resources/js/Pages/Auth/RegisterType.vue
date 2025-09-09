<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';

</script>
<template>
  <Head title="Pilih Jenis Registrasi" />
  <div class="min-h-screen flex flex-col items-center justify-center bg-gray-100">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md flex flex-col items-center">
      <img src="/images/Logomark.png" alt="Logo" class="w-14 h-14 mb-4" />
      <h2 class="text-2xl font-bold mb-2">Daftar Akun Baru</h2>
      <p class="text-gray-500 mb-8 text-center">Pilih jenis akun yang ingin Anda daftarkan di marketplace Semarak.</p>
      <div class="flex flex-col gap-4 w-full">
        <Link :href="route('register')" class="w-full bg-blue-600 text-white py-3 rounded-lg text-lg font-semibold text-center hover:bg-blue-700 transition">Daftar Sebagai User (Pembeli)</Link>
        <Link :href="route('register.toko')" class="w-full bg-yellow-500 text-gray-900 py-3 rounded-lg text-lg font-semibold text-center hover:bg-yellow-600 transition">Daftar Sebagai Toko (Penjual)</Link>
      </div>
    </div>
  </div>
</template>
