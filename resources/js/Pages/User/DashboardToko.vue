<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head } from '@inertiajs/vue3'
import Sidebar from '@/Components/Sidebar.vue'

const props = defineProps({
  categories: {
    type: Array,
    default: () => []
  },
  products: {
    type: Array,
    default: () => []
  },
  stats: { // tambahan data statistik toko
    type: Object,
    default: () => ({
      totalProduk: 0,
      totalKategori: 0,
      totalPenjualan: 0,
      totalPendapatan: 0
    })
  }
})

// fungsi render bintang rating
function renderStars(rating) {
  const full = Math.floor(rating)
  const half = rating % 1 >= 0.5
  let stars = ''
  for (let i = 0; i < full; i++) stars += '★'
  if (half) stars += '☆'
  while (stars.length < 5) stars += '☆'
  return stars
}

// mapping kategori → emoji
const iconMap = {
  makanan: "🍔",
  minuman: "🥤",
  elektronik: "💻",
  pakaian: "👕",
  olahraga: "⚽",
  hewan: "🐶",
  buku: "📚",
  musik: "🎵",
  mainan: "🎮",
  kecantikan: "💄",
  kesehatan: "💊",
  bunga: "🌸",
  kendaraan: "🚗",
  default: "🛒",
}

function getIcon(name) {
  if (!name) return iconMap.default
  const lower = name.toLowerCase()
  return iconMap[lower] ?? iconMap.default
}
</script>

<template>
  <Head title="Dashboard Toko" />

  <AuthenticatedLayout>
    <template #header>
      <h2 class="text-xl font-semibold leading-tight text-gray-800">
        Dashboard Toko
      </h2>
    </template>

    <div class="flex">
      <!-- Sidebar -->
  <Sidebar class="h-screen fixed left-0 top-0 bg-white shadow-md" />

      <!-- Konten utama -->
      <div class="flex-1 ml-64 bg-gray-100 min-h-screen">
        <div class="py-10 px-6">
          <div class="max-w-7xl mx-auto space-y-8">
            
            <!-- 📊 Statistik Toko -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">📦</span>
                <p class="mt-2 text-gray-500 text-sm">Total Produk</p>
                <h3 class="text-lg font-bold">{{ stats.totalProduk }}</h3>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">📂</span>
                <p class="mt-2 text-gray-500 text-sm">Total Kategori</p>
                <h3 class="text-lg font-bold">{{ stats.totalKategori }}</h3>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">🛒</span>
                <p class="mt-2 text-gray-500 text-sm">Total Penjualan</p>
                <h3 class="text-lg font-bold">{{ stats.totalPenjualan }}</h3>
              </div>
              <div class="bg-white p-6 rounded-xl shadow-md flex flex-col items-center">
                <span class="text-3xl">💰</span>
                <p class="mt-2 text-gray-500 text-sm">Total Pendapatan</p>
                <h3 class="text-lg font-bold">Rp{{ Number(stats.totalPendapatan).toLocaleString('id-ID') }}</h3>
              </div>
            </div>

            <!-- 📌 Produk Terbaru -->
            <div class="bg-white rounded-xl shadow-md p-6">
              <h3 class="text-xl font-bold mb-6">Produk di Marketplace</h3>
              <div 
                v-if="products.length" 
                class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
              >
                <div
                  v-for="produk in products"
                  :key="produk.id"
                  class="bg-white rounded-xl shadow-sm p-4 flex flex-col items-center hover:scale-105 hover:shadow-lg transition"
                >
                  <img
                    v-if="produk.image"
                    :src="produk.image.startsWith('produk/') ? '/storage/' + produk.image : produk.image"
                    :alt="produk.nama"
                    class="w-32 h-32 object-cover rounded-lg mb-3"
                  />
                  <div class="text-base font-semibold text-gray-800 text-center mb-1">
                    {{ produk.nama }}
                  </div>
                  <div class="text-orange-600 font-bold mb-1">
                    Rp{{ Number(produk.harga).toLocaleString('id-ID') }}
                  </div>
                  <div class="text-xs text-gray-500 mb-1">
                    {{ getIcon(produk.kategori) }} {{ produk.kategori }}
                  </div>
                  <div class="flex items-center mb-1">
                    <span class="text-yellow-400 text-lg">{{ renderStars(4.5) }}</span>
                    <span class="ml-1 text-xs text-gray-500">4.5</span>
                  </div>
                  <div class="text-xs text-gray-400 text-center line-clamp-2 mb-1">
                    {{ produk.deskripsi }}
                  </div>
                  <div class="text-xs text-gray-400">Terjual: {{ produk.terjual ?? 0 }}</div>
                </div>
              </div>
              <div v-else class="text-gray-500 text-center py-10">
                Belum ada produk tersedia.
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>
