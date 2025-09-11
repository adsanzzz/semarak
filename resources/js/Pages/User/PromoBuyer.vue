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
              v-for="(item, index) in productsBeli1"
              :key="index"
              class="bg-white rounded-xl shadow p-4 flex flex-col"
            >
              <!-- Container gambar + ikon like -->
              <div class="relative">
                <img :src="item.image" alt="Produk" class="w-full h-40 object-cover rounded-lg mb-3" />
                <!-- Ikon Like -->
                <button
                  @click="toggleLike(item)"
                  class="absolute top-2 right-2 bg-white rounded-full p-2 shadow flex items-center justify-center hover:scale-110 transition"
                >
                  <HeartOutline v-if="!item.liked" class="w-5 h-5 text-gray-600" />
                  <HeartSolid v-else class="w-5 h-5 text-red-500" />
                </button>
              </div>

              <!-- Info produk -->
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
              v-for="(item, index) in productsLebaran"
              :key="index"
              class="bg-white rounded-xl shadow p-4 flex flex-col"
            >
              <!-- Container gambar + ikon like -->
              <div class="relative">
                <img :src="item.image" alt="Produk" class="w-full h-40 object-cover rounded-lg mb-3" />
                <!-- Ikon Like -->
                <button
                  @click="toggleLike(item)"
                  class="absolute top-2 right-2 bg-white rounded-full p-2 shadow flex items-center justify-center hover:scale-110 transition"
                >
                  <HeartOutline v-if="!item.liked" class="w-5 h-5 text-gray-600" />
                  <HeartSolid v-else class="w-5 h-5 text-red-500" />
                </button>
              </div>

              <!-- Info produk -->
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
import { HeartIcon as HeartOutline } from "@heroicons/vue/24/outline";
import { HeartIcon as HeartSolid } from "@heroicons/vue/24/solid";

const tab = ref("promo");

// Produk untuk Beli 1 Gratis 1
const productsBeli1 = ref([
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Kursi Rotan Tahan Lama",
    seller: "Toko Indonesia",
    location: "Jakarta",
    oldPrice: "Rp 650.000,00",
    price: "Rp 239.000,00",
    liked: false,
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Paperbag Custom Ukuran Warna",
    seller: "Toko Indonesia",
    location: "Jakarta",
    oldPrice: "Rp 90.000,00",
    price: "Rp 47.000,00",
    liked: false,
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.8,
    title: "Sendal Jepit",
    seller: "Toko Indonesia",
    location: "Solo",
    oldPrice: "Rp 20.000,00",
    price: "Rp 15.000,00",
    liked: false,
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.0,
    title: "Tumbler Minum",
    seller: "Toko Indonesia",
    location: "Sukoharjo",
    oldPrice: "Rp 150.000,00",
    price: "Rp 120.000,00",
    liked: false,
  },
]);

// Produk untuk Promo Puncak Lebaran
const productsLebaran = ref([
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Keripik Oven - Coklat",
    seller: "Toko Indonesia",
    location: "Bandung",
    oldPrice: "Rp 80.000,00",
    price: "Rp 47.000,00",
    liked: false,
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.7,
    title: "Serum Brightening - Avoskin",
    seller: "Toko Indonesia",
    location: "Surabaya",
    oldPrice: "Rp 90.000,00",
    price: "Rp 47.000,00",
    liked: false,
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.9,
    title: "Toples Lebaran",
    seller: "Toko Indonesia",
    location: "Semarang",
    oldPrice: "Rp 50.000,00",
    price: "Rp 45.000,00",
    liked: false,
  },
  {
    image: "https://via.placeholder.com/300x200",
    rating: 4.9,
    title: "Abaya Raya",
    seller: "Toko Indonesia",
    location: "Magelang",
    oldPrice: "Rp 200.000,00",
    price: "Rp 180.000,00",
    liked: false,
  },
]);

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

function toggleLike(item) {
  item.liked = !item.liked;
}
</script>
