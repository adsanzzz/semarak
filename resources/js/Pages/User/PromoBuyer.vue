<template>
  <div>
    <NavbarAuth />
     <div class="px-6 py-6">
    <!-- Breadcrumb -->
    <nav class="text-sm text-gray-500 mb-6">
      <ol class="flex space-x-2">
        <li><a href="#" class="hover:underline">Beranda</a></li>
        <li>/</li>
        <li>Promo & Diskon</li>
      </ol>
    </nav>

    <!-- Judul -->
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Promo & Diskon</h1>

    <!-- Tab Navigation -->
    <div class="border-b flex justify-center space-x-8 mb-8">
      <button
        :class="tab === 'promo' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-500'"
        class="pb-3"
        @click="tab = 'promo'"
      >
        Promo
      </button>
      <button
        :class="tab === 'kupon' ? 'border-b-2 border-blue-600 text-blue-600 font-medium' : 'text-gray-500'"
        class="pb-3"
        @click="tab = 'kupon'"
      >
        Kupon Diskon
      </button>
    </div>

    <!-- Tab Content -->
    <div v-if="tab === 'promo'">
      <!-- Produk Beli 1 Gratis 1 -->
      <div class="mb-8">
        <h2 class="bg-blue-900 text-white inline-block px-4 py-1 rounded-lg text-sm font-medium mb-4">
          Produk Beli 1 Gratis 1
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div
            v-for="(item, index) in products"
            :key="index"
            class="bg-white rounded-xl shadow p-4 flex flex-col"
          >
            <img :src="item.image" alt="Produk" class="w-full h-40 object-cover rounded-lg mb-3" />
            <div class="flex items-center mb-2">
              <span class="text-yellow-400">★</span>
              <span class="ml-1 text-sm">{{ item.rating }}</span>
              <span class="ml-1 text-gray-400 text-xs">(100+ Terjual)</span>
            </div>
            <h3 class="text-sm font-semibold text-gray-700 mb-1">{{ item.title }}</h3>
            <p class="text-xs text-gray-400 mb-1">{{ item.seller }}</p>
            <p class="text-xs text-gray-400 mb-2">{{ item.location }}</p>
            <span class="line-through text-xs text-gray-400">{{ item.oldPrice }}</span>
            <p class="text-blue-700 font-semibold">{{ item.price }}</p>
            <button
              class="mt-auto bg-yellow-400 text-white text-sm font-medium py-2 px-4 rounded-lg hover:bg-yellow-500 flex items-center justify-center"
            >
              Tambah ke Keranjang 🛒
            </button>
          </div>
        </div>
      </div>

      <!-- Promo Puncak Lebaran -->
      <div>
        <h2 class="bg-blue-900 text-white inline-block px-4 py-1 rounded-lg text-sm font-medium mb-4">
          Promo Puncak Lebaran
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
          <div
            v-for="(item, index) in products"
            :key="index"
            class="bg-white rounded-xl shadow p-4 flex flex-col"
          >
            <img :src="item.image" alt="Produk" class="w-full h-40 object-cover rounded-lg mb-3" />
            <div class="flex items-center mb-2">
              <span class="text-yellow-400">★</span>
              <span class="ml-1 text-sm">{{ item.rating }}</span>
              <span class="ml-1 text-gray-400 text-xs">(100+ Terjual)</span>
            </div>
            <h3 class="text-sm font-semibold text-gray-700 mb-1">{{ item.title }}</h3>
            <p class="text-xs text-gray-400 mb-1">{{ item.seller }}</p>
            <p class="text-xs text-gray-400 mb-2">{{ item.location }}</p>
            <span class="line-through text-xs text-gray-400">{{ item.oldPrice }}</span>
            <p class="text-blue-700 font-semibold">{{ item.price }}</p>
            <button
              class="mt-auto bg-yellow-400 text-white text-sm font-medium py-2 px-4 rounded-lg hover:bg-yellow-500 flex items-center justify-center"
            >
              Tambah ke Keranjang 🛒
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Kupon Diskon -->
    <div v-else>
      <div v-for="(kupon, idx) in kuponList" :key="idx" class="mb-8">
        <h2 class="bg-blue-900 text-white inline-block px-4 py-1 rounded-lg text-sm font-medium mb-4">
          {{ kupon.title }}
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
          <div
            v-for="(item, k) in kupon.items"
            :key="k"
            class="bg-gradient-to-r from-white to-yellow-50 border rounded-xl shadow p-4 flex flex-col justify-between"
          >
            <p class="font-semibold text-gray-700 text-sm">{{ item.label }}</p>
            <p class="text-lg font-bold">s.d {{ item.nominal }}</p>
            <p class="text-xs text-gray-500">Min. Transaksi {{ item.min }}</p>
            <p class="text-xs text-red-500 mt-2">Berlaku s.d {{ item.exp }}</p>
            <button
              class="mt-3 bg-blue-700 text-white text-sm py-2 rounded-lg hover:bg-blue-800"
            >
              Klaim Sekarang
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
  </div>
</template>

<script setup>
import NavbarAuth from '@/Components/NavbarAuth.vue';
import { ref } from "vue";

const tab = ref("promo");

const products = [
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Kursi Rotan Tahan Lama",
    seller: "Toko Indonesia",
    location: "Jakarta",
    oldPrice: "Rp 650.000,00",
    price: "Rp 239.000,00",
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Paperbag Custom Ukuran Warna",
    seller: "Toko Indonesia",
    location: "Jakarta",
    oldPrice: "Rp 90.000,00",
    price: "Rp 47.000,00",
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Keripik Oven - Coklat",
    seller: "Toko Indonesia",
    location: "Bandung",
    oldPrice: "Rp 80.000,00",
    price: "Rp 47.000,00",
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Serum Brightening - Avoskin",
    seller: "Toko Indonesia",
    location: "Surabaya",
    oldPrice: "Rp 90.000,00",
    price: "Rp 47.000,00",
  },
];

const kuponList = [
  {
    title: "Kupon Diskon Gratis Ongkir",
    items: [
      { label: "Diskon Gratis Ongkir", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
      { label: "Diskon Gratis Ongkir", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
      { label: "Diskon Gratis Ongkir", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
      { label: "Diskon Gratis Ongkir", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
    ],
  },
  {
    title: "Kupon Cashback",
    items: [
      { label: "Kupon Cashback", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
      { label: "Kupon Cashback", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
      { label: "Kupon Cashback", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
      { label: "Kupon Cashback", nominal: "50rb", min: "200rb", exp: "31 Desember 2025" },
    ],
  },
];
</script>

<style scoped>
</style>